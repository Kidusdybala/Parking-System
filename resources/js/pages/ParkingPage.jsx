import React, { useState } from 'react';
import { useParkingSpots } from '../hooks';
import { Card, Button, Modal, Input } from '../components/ui';
import { getStatusColor, formatCurrency } from '../utils';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const ParkingPage = () => {
    const { spots, filter, loading, error, applyFilter, refreshSpots } = useParkingSpots();
    const [selectedSpot, setSelectedSpot] = useState(null);
    const [showReserveModal, setShowReserveModal] = useState(false);

    const filterOptions = [
        { value: 'all', label: 'All Spots', icon: 'fas fa-list' },
        { value: 'available', label: 'Available', icon: 'fas fa-check-circle' },
        { value: 'occupied', label: 'Occupied', icon: 'fas fa-times-circle' },
        { value: 'reserved', label: 'Reserved', icon: 'fas fa-clock' }
    ];

    const handleReserveSpot = (spot) => {
        setSelectedSpot(spot);
        setShowReserveModal(true);
    };

    const handleCloseModal = () => {
        setShowReserveModal(false);
        setSelectedSpot(null);
    };

    if (loading) {
        return <LoadingSpinner message="Loading parking spots..." />;
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Parking Spots</h1>
                    <p className="mt-1 text-sm text-gray-600">
                        Find and reserve your perfect parking spot
                    </p>
                </div>
                <Button 
                    onClick={refreshSpots}
                    variant="outline"
                    icon="fas fa-sync-alt"
                >
                    Refresh
                </Button>
            </div>

            {/* Filter Buttons */}
            <Card>
                <Card.Content>
                    <div className="flex flex-wrap gap-3">
                        {filterOptions.map((option) => (
                            <Button
                                key={option.value}
                                onClick={() => applyFilter(option.value)}
                                variant={filter === option.value ? 'primary' : 'outline'}
                                icon={option.icon}
                                size="small"
                            >
                                {option.label}
                            </Button>
                        ))}
                    </div>
                </Card.Content>
            </Card>

            {/* Stats */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                <Card>
                    <Card.Content>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">{spots.length}</div>
                            <div className="text-sm text-gray-500">Total Spots</div>
                        </div>
                    </Card.Content>
                </Card>
                <Card>
                    <Card.Content>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">
                                {spots.filter(s => s.status === 'available').length}
                            </div>
                            <div className="text-sm text-gray-500">Available</div>
                        </div>
                    </Card.Content>
                </Card>
                <Card>
                    <Card.Content>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-red-600">
                                {spots.filter(s => s.status === 'occupied').length}
                            </div>
                            <div className="text-sm text-gray-500">Occupied</div>
                        </div>
                    </Card.Content>
                </Card>
                <Card>
                    <Card.Content>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-yellow-600">
                                {spots.filter(s => s.status === 'reserved').length}
                            </div>
                            <div className="text-sm text-gray-500">Reserved</div>
                        </div>
                    </Card.Content>
                </Card>
            </div>

            {/* Parking Spots Grid */}
            {spots.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {spots.map((spot) => (
                        <Card key={spot.id} hover>
                            <Card.Content>
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-lg font-medium text-gray-900">
                                        Spot #{spot.id}
                                    </h3>
                                    <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(spot.status)}`}>
                                        {spot.status}
                                    </span>
                                </div>
                                
                                <div className="space-y-2 text-sm text-gray-600 mb-4">
                                    <div className="flex items-center">
                                        <i className="fas fa-map-marker-alt mr-2"></i>
                                        <span>{spot.location || 'Location not specified'}</span>
                                    </div>
                                    <div className="flex items-center">
                                        <i className="fas fa-car mr-2"></i>
                                        <span>{spot.type || 'Standard'}</span>
                                    </div>
                                    <div className="flex items-center">
                                        <i className="fas fa-dollar-sign mr-2"></i>
                                        <span>{formatCurrency(spot.hourly_rate || 0)}/hour</span>
                                    </div>
                                </div>

                                {spot.status === 'available' && (
                                    <Button
                                        onClick={() => handleReserveSpot(spot)}
                                        variant="primary"
                                        size="small"
                                        className="w-full"
                                        icon="fas fa-calendar-plus"
                                    >
                                        Reserve Spot
                                    </Button>
                                )}
                            </Card.Content>
                        </Card>
                    ))}
                </div>
            ) : (
                <Card>
                    <Card.Content>
                        <div className="text-center py-12">
                            <i className="fas fa-parking text-4xl text-gray-400 mb-4"></i>
                            <p className="text-gray-500 mb-4">No parking spots found</p>
                            <Button onClick={refreshSpots} variant="primary">
                                Refresh Spots
                            </Button>
                        </div>
                    </Card.Content>
                </Card>
            )}

            {/* Reserve Modal */}
            <Modal
                isOpen={showReserveModal}
                onClose={handleCloseModal}
                title="Reserve Parking Spot"
                size="medium"
            >
                {selectedSpot && (
                    <div className="space-y-4">
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <h4 className="font-medium text-gray-900 mb-2">
                                Spot #{selectedSpot.id}
                            </h4>
                            <div className="text-sm text-gray-600 space-y-1">
                                <p><strong>Location:</strong> {selectedSpot.location}</p>
                                <p><strong>Type:</strong> {selectedSpot.type}</p>
                                <p><strong>Rate:</strong> {formatCurrency(selectedSpot.hourly_rate)}/hour</p>
                            </div>
                        </div>
                        
                        <div className="grid grid-cols-2 gap-4">
                            <Input
                                label="Start Date"
                                type="datetime-local"
                                required
                            />
                            <Input
                                label="End Date"
                                type="datetime-local"
                                required
                            />
                        </div>
                        
                        <div className="flex justify-end space-x-3 pt-4">
                            <Button
                                onClick={handleCloseModal}
                                variant="outline"
                            >
                                Cancel
                            </Button>
                            <Button
                                variant="primary"
                                icon="fas fa-calendar-check"
                            >
                                Reserve Spot
                            </Button>
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
};

export default ParkingPage;