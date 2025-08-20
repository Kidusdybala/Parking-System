import { useCallback, useMemo, useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../components/Common/LoadingSpinner';
import { useParkingSpots } from '../hooks/useParkingSpots';
import ParkingFilters from '../components/Parking/ParkingFilters';
import ParkingCard from '../components/Parking/ParkingCard';
import Modal from '../components/ui/Modal';
import Button from '../components/ui/Button';
import Input from '../components/ui/Input';
import { useReservationForm } from '../hooks/useReservationForm';

const ParkingPage = () => {
    const { user } = useAuth();
    const { allSpots, spots, loading, applyFilter, refreshSpots } = useParkingSpots();
    const [filters, setFilters] = useState({ location: '', status: 'available', maxRate: '' });
    const [selectedSpot, setSelectedSpot] = useState(null);
    const [showReservationModal, setShowReservationModal] = useState(false);

    const filteredSpots = useMemo(() => {
        let data = [...spots];
        if (filters.location) {
            data = data.filter(spot => spot.location?.toLowerCase().includes(filters.location.toLowerCase()));
        }
        if (filters.status) {
            data = data.filter(spot => spot.status === filters.status);
        }
        if (filters.maxRate) {
            data = data.filter(spot => parseFloat(spot.hourly_rate) <= parseFloat(filters.maxRate));
        }
        return data;
    }, [spots, filters]);

    const handleFilterChange = useCallback((key, value) => {
        setFilters(prev => ({ ...prev, [key]: value }));
        if (key === 'status') {
            applyFilter(value || 'all');
        }
    }, [applyFilter]);

    const resetFilters = useCallback(() => {
        setFilters({ location: '', status: 'available', maxRate: '' });
        applyFilter('available');
    }, [applyFilter]);

    const openReservationModal = useCallback((spot) => {
        setSelectedSpot(spot);
        setShowReservationModal(true);
    }, []);

    const closeReservationModal = useCallback(() => {
        setShowReservationModal(false);
        setSelectedSpot(null);
    }, []);

    const { form, updateField, submit, submitting, hours, totalCost } = useReservationForm({
        initialSpot: selectedSpot,
        onSuccess: () => {
            closeReservationModal();
            refreshSpots();
        }
    });

    const handleReservation = useCallback(async () => {
        const result = await submit();
        if (!result.success) {
            alert(result.message || 'Failed to create reservation');
        }
    }, [submit]);

    const canAfford = useMemo(() => (user?.balance || 0) >= totalCost, [user?.balance, totalCost]);

    if (loading) return <LoadingSpinner />;

    return (
        <div className="container py-6">
            <div className="mb-8">
                <h1 className="text-3xl font-bold mb-2">Parking Spots</h1>
                <p className="text-muted-foreground">Find and reserve your perfect parking spot</p>
            </div>

            <ParkingFilters filters={filters} onChange={handleFilterChange} onReset={resetFilters} />

            {/* Parking Spots Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredSpots.map((spot) => (
                    <ParkingCard key={spot.id} spot={spot} onReserve={openReservationModal} />
                ))}
            </div>

            {filteredSpots.length === 0 && (
                <div className="text-center py-12">
                    <i className="fas fa-search text-4xl text-muted-foreground mb-4"></i>
                    <h3 className="text-xl font-semibold mb-2">No parking spots found</h3>
                    <p className="text-muted-foreground">Try adjusting your filters</p>
                </div>
            )}

            {/* Reservation Modal */}
            <Modal isOpen={showReservationModal} onClose={closeReservationModal} title="Reserve Parking Spot">
                {selectedSpot && (
                    <div className="space-y-4">
                        <div className="bg-parkBlue-800/30 p-4 rounded-lg">
                            <h3 className="font-semibold">{selectedSpot.spot_number}</h3>
                            <p className="text-sm text-muted-foreground">{selectedSpot.location}</p>
                            <p className="text-sm text-primary">${selectedSpot.hourly_rate}/hour</p>
                        </div>
                        <Input
                            label="Start Time"
                            type="datetime-local"
                            value={form.start_time}
                            onChange={(e) => updateField('start_time', e.target.value)}
                        />
                        <Input
                            label="End Time"
                            type="datetime-local"
                            value={form.end_time}
                            onChange={(e) => updateField('end_time', e.target.value)}
                        />
                        <div className="bg-parkBlue-800/30 p-4 rounded-lg">
                            <div className="flex justify-between items-center">
                                <span className="font-medium">Total Cost:</span>
                                <span className="text-xl font-bold text-primary">${totalCost}</span>
                            </div>
                            <p className="text-xs text-muted-foreground mt-1">Current balance: ${user?.balance || 0}</p>
                        </div>
                        <div className="flex gap-3">
                            <Button variant="outline" className="flex-1" onClick={closeReservationModal} disabled={submitting}>Cancel</Button>
                            <Button className="flex-1" icon="fas fa-check" onClick={handleReservation} loading={submitting} disabled={!canAfford}>
                                Confirm Reservation
                            </Button>
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
};

export default ParkingPage;
