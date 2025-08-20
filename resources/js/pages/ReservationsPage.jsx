import { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useParking } from '../contexts/ParkingContext';
import axios from 'axios';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const ReservationsPage = () => {
    const { user } = useAuth();
    const { userReservations, handleCancelReservation, getReservationTimeRemaining, loading } = useParking();
    const [actionLoading, setActionLoading] = useState(null);
    const [filter, setFilter] = useState('all');
    const [updateTrigger, setUpdateTrigger] = useState(0);

    useEffect(() => {
        // Set up timer to update reservation countdowns every 10 seconds
        const timer = setInterval(() => {
            setUpdateTrigger(prev => prev + 1);
        }, 10000);

        return () => {
            clearInterval(timer);
        };
    }, []);

    const handleCancelReservationLocal = async (reservationId) => {
        if (!confirm('Are you sure you want to cancel this reservation?')) {
            return;
        }

        try {
            setActionLoading(reservationId);
            // Find the reservation to get the spot ID
            const reservation = userReservations.find(r => r.id === reservationId);
            if (reservation && (reservation.status === 'reserved' || reservation.status === 'active')) {
                // Use the parking context to cancel the reservation
                await handleCancelReservation(reservation.parking_spot_id);
            }
        } catch (error) {
            console.error('Failed to cancel reservation:', error);
        } finally {
            setActionLoading(null);
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'active': return 'text-green-400 bg-green-500/20 border-green-500/30';
            case 'completed': return 'text-blue-400 bg-blue-500/20 border-blue-500/30';
            case 'cancelled': return 'text-red-400 bg-red-500/20 border-red-500/30';
            default: return 'text-gray-400 bg-gray-500/20 border-gray-500/30';
        }
    };

    const getStatusIcon = (status) => {
        switch (status) {
            case 'active': return 'fas fa-clock';
            case 'completed': return 'fas fa-check-circle';
            case 'cancelled': return 'fas fa-times-circle';
            default: return 'fas fa-question-circle';
        }
    };

    const formatDateTime = (dateString) => {
        return new Date(dateString).toLocaleString();
    };

    const isReservationCancellable = (reservation) => {
        return reservation.status === 'active' && new Date(reservation.start_time) > new Date();
    };

    const isReservationActive = (reservation) => {
        const now = new Date();
        const start = new Date(reservation.start_time);
        const end = new Date(reservation.end_time);
        return reservation.status === 'active' && start <= now && end > now;
    };

    const getTimeRemaining = (endTime) => {
        const now = new Date();
        const end = new Date(endTime);
        const diff = end - now;
        
        if (diff <= 0) return 'Expired';
        
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        
        return `${hours}h ${minutes}m remaining`;
    };

    const filteredReservations = Array.isArray(userReservations) ? userReservations.filter(reservation => {
        if (filter === 'all') return true;
        return reservation.status === filter;
    }) : [];

    if (loading) {
        return <LoadingSpinner />;
    }

    return (
        <div className="container py-6">
            <div className="mb-8">
                <h1 className="text-3xl font-bold mb-2">My Reservations</h1>
                <p className="text-muted-foreground">Manage your parking reservations</p>
            </div>

            {/* Filter Tabs */}
            <div className="glass-card p-6 mb-8">
                <div className="flex flex-wrap gap-2">
                    {[
                        { key: 'all', label: 'All', count: Array.isArray(userReservations) ? userReservations.length : 0 },
                        { key: 'active', label: 'Active', count: Array.isArray(userReservations) ? userReservations.filter(r => r.status === 'active').length : 0 },
                        { key: 'completed', label: 'Completed', count: Array.isArray(userReservations) ? userReservations.filter(r => r.status === 'completed').length : 0 },
                        { key: 'cancelled', label: 'Cancelled', count: Array.isArray(userReservations) ? userReservations.filter(r => r.status === 'cancelled').length : 0 }
                    ].map(tab => (
                        <button
                            key={tab.key}
                            onClick={() => setFilter(tab.key)}
                            className={`px-4 py-2 rounded-md font-medium transition-all ${
                                filter === tab.key
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-parkBlue-800/40 hover:bg-parkBlue-800/60'
                            }`}
                        >
                            {tab.label} ({tab.count})
                        </button>
                    ))}
                </div>
            </div>

            {/* Reservations List */}
            <div className="space-y-6">
                {filteredReservations.map((reservation) => (
                    <div key={reservation.id} className="glass-card p-6">
                        <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div className="flex-1">
                                <div className="flex items-center gap-3 mb-3">
                                    <h3 className="text-xl font-semibold">
                                        {reservation.parking_spot.spot_number}
                                    </h3>
                                    <span className={`badge ${getStatusColor(reservation.status)}`}>
                                        <i className={`${getStatusIcon(reservation.status)} mr-1`}></i>
                                        {reservation.status}
                                    </span>
                                    {isReservationActive(reservation) && (
                                        <span className="badge badge-available">
                                            <i className="fas fa-car mr-1"></i>
                                            Currently Parked
                                        </span>
                                    )}
                                    {reservation.status === 'active' && !isReservationActive(reservation) && (
                                        <span className="badge text-yellow-600 bg-yellow-100 border-yellow-300">
                                            <i className="fas fa-clock mr-1"></i>
                                            Expires in {getReservationTimeRemaining(reservation.parking_spot.id)} min
                                        </span>
                                    )}
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div className="flex items-center text-muted-foreground mb-2">
                                            <i className="fas fa-map-marker-alt mr-2"></i>
                                            <span>{reservation.parking_spot.location}</span>
                                        </div>
                                        <div className="flex items-center text-muted-foreground mb-2">
                                            <i className="fas fa-calendar mr-2"></i>
                                            <span>Started: {formatDateTime(reservation.start_time)}</span>
                                        </div>
                                        {reservation.end_time && (
                                            <div className="flex items-center text-muted-foreground">
                                                <i className="fas fa-calendar-check mr-2"></i>
                                                <span>Ended: {formatDateTime(reservation.end_time)}</span>
                                            </div>
                                        )}
                                        {!reservation.end_time && reservation.status === 'active' && (
                                            <div className="flex items-center text-blue-600">
                                                <i className="fas fa-clock mr-2"></i>
                                                <span>Currently Active</span>
                                            </div>
                                        )}
                                    </div>
                                    <div>
                                        {reservation.end_time && (
                                            <>
                                                <div className="flex items-center text-muted-foreground mb-2">
                                                    <i className="fas fa-dollar-sign mr-2"></i>
                                                    <span>Total Cost: {reservation.total_cost} Birr</span>
                                                </div>
                                                <div className="flex items-center text-muted-foreground mb-2">
                                                    <i className="fas fa-clock mr-2"></i>
                                                    <span>
                                                        Duration: {Math.ceil(
                                                            (new Date(reservation.end_time) - new Date(reservation.start_time)) / (1000 * 60 * 60)
                                                        )} hours
                                                    </span>
                                                </div>
                                            </>
                                        )}
                                        {!reservation.end_time && reservation.status === 'active' && (
                                            <div className="flex items-center text-muted-foreground mb-2">
                                                <i className="fas fa-info-circle mr-2"></i>
                                                <span>Cost will be calculated when parking ends</span>
                                            </div>
                                        )}

                                    </div>
                                </div>
                            </div>

                            <div className="flex flex-col gap-2 lg:w-auto">
                                {isReservationCancellable(reservation) && (
                                    <button
                                        onClick={() => handleCancelReservationLocal(reservation.id)}
                                        disabled={actionLoading === reservation.id}
                                        className="btn btn-danger"
                                    >
                                        {actionLoading === reservation.id ? (
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                        ) : (
                                            <i className="fas fa-times mr-2"></i>
                                        )}
                                        {actionLoading === reservation.id ? 'Cancelling...' : 'Cancel'}
                                    </button>
                                )}
                                
                                {reservation.status === 'completed' && (
                                    <button className="btn btn-outline">
                                        <i className="fas fa-receipt mr-2"></i>
                                        View Receipt
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {filteredReservations.length === 0 && (
                <div className="text-center py-12">
                    <i className="fas fa-calendar-times text-4xl text-muted-foreground mb-4"></i>
                    <h3 className="text-xl font-semibold mb-2">No reservations found</h3>
                    <p className="text-muted-foreground mb-6">
                        {filter === 'all' 
                            ? "You haven't made any reservations yet" 
                            : `No ${filter} reservations found`
                        }
                    </p>
                    <a href="/parking" className="btn btn-primary">
                        <i className="fas fa-plus mr-2"></i>
                        Make a Reservation
                    </a>
                </div>
            )}
        </div>
    );
};

export default ReservationsPage;
