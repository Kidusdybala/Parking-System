import React, { useState } from 'react';
import { useReservations } from '../hooks';
import { Card, Button, Modal, Alert } from '../components/ui';
import { getStatusColor, formatCurrency, formatDateTime } from '../utils';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const ReservationsPage = () => {
    const {
        reservations,
        activeReservations,
        completedReservations,
        cancelledReservations,
        loading,
        error,
        cancelReservation,
        refreshReservations
    } = useReservations();

    const [selectedTab, setSelectedTab] = useState('all');
    const [showCancelModal, setShowCancelModal] = useState(false);
    const [reservationToCancel, setReservationToCancel] = useState(null);
    const [cancelLoading, setCancelLoading] = useState(false);

    const tabs = [
        { id: 'all', label: 'All Reservations', count: reservations.length },
        { id: 'active', label: 'Active', count: activeReservations.length },
        { id: 'completed', label: 'Completed', count: completedReservations.length },
        { id: 'cancelled', label: 'Cancelled', count: cancelledReservations.length }
    ];

    const getFilteredReservations = () => {
        switch (selectedTab) {
            case 'active':
                return activeReservations;
            case 'completed':
                return completedReservations;
            case 'cancelled':
                return cancelledReservations;
            default:
                return reservations;
        }
    };

    const handleCancelClick = (reservation) => {
        setReservationToCancel(reservation);
        setShowCancelModal(true);
    };

    const handleConfirmCancel = async () => {
        if (!reservationToCancel) return;

        setCancelLoading(true);
        const result = await cancelReservation(reservationToCancel.id);
        
        if (result.success) {
            setShowCancelModal(false);
            setReservationToCancel(null);
        }
        setCancelLoading(false);
    };

    const handleCloseModal = () => {
        setShowCancelModal(false);
        setReservationToCancel(null);
    };

    if (loading) {
        return <LoadingSpinner message="Loading reservations..." />;
    }

    const filteredReservations = getFilteredReservations();

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex justify-between items-center">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">My Reservations</h1>
                    <p className="mt-1 text-sm text-gray-600">
                        Manage your parking reservations
                    </p>
                </div>
                <Button 
                    onClick={refreshReservations}
                    variant="outline"
                    icon="fas fa-sync-alt"
                >
                    Refresh
                </Button>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                {tabs.map((tab) => (
                    <Card key={tab.id} hover>
                        <Card.Content>
                            <div className="text-center">
                                <div className="text-2xl font-bold text-blue-600">{tab.count}</div>
                                <div className="text-sm text-gray-500">{tab.label}</div>
                            </div>
                        </Card.Content>
                    </Card>
                ))}
            </div>

            {/* Tab Navigation */}
            <Card>
                <Card.Content>
                    <div className="flex flex-wrap gap-2">
                        {tabs.map((tab) => (
                            <Button
                                key={tab.id}
                                onClick={() => setSelectedTab(tab.id)}
                                variant={selectedTab === tab.id ? 'primary' : 'outline'}
                                size="small"
                            >
                                {tab.label} ({tab.count})
                            </Button>
                        ))}
                    </div>
                </Card.Content>
            </Card>

            {/* Reservations List */}
            {filteredReservations.length > 0 ? (
                <div className="space-y-4">
                    {filteredReservations.map((reservation) => (
                        <Card key={reservation.id}>
                            <Card.Content>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center space-x-4">
                                        <div className="flex-shrink-0">
                                            <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i className="fas fa-parking text-blue-600 text-xl"></i>
                                            </div>
                                        </div>
                                        <div className="flex-1">
                                            <div className="flex items-center space-x-2 mb-1">
                                                <h3 className="text-lg font-medium text-gray-900">
                                                    Parking Spot #{reservation.parking_spot_id}
                                                </h3>
                                                <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(reservation.status)}`}>
                                                    {reservation.status}
                                                </span>
                                            </div>
                                            <div className="text-sm text-gray-600 space-y-1">
                                                <div className="flex items-center">
                                                    <i className="fas fa-calendar mr-2"></i>
                                                    <span>
                                                        {formatDateTime(reservation.start_time)} - {formatDateTime(reservation.end_time)}
                                                    </span>
                                                </div>
                                                <div className="flex items-center">
                                                    <i className="fas fa-dollar-sign mr-2"></i>
                                                    <span>Total Cost: {formatCurrency(reservation.total_cost || 0)}</span>
                                                </div>
                                                {reservation.notes && (
                                                    <div className="flex items-center">
                                                        <i className="fas fa-sticky-note mr-2"></i>
                                                        <span>{reservation.notes}</span>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-2">
                                        {reservation.status === 'active' && (
                                            <Button
                                                onClick={() => handleCancelClick(reservation)}
                                                variant="danger"
                                                size="small"
                                                icon="fas fa-times"
                                            >
                                                Cancel
                                            </Button>
                                        )}
                                        <Button
                                            variant="outline"
                                            size="small"
                                            icon="fas fa-eye"
                                        >
                                            View Details
                                        </Button>
                                    </div>
                                </div>
                            </Card.Content>
                        </Card>
                    ))}
                </div>
            ) : (
                <Card>
                    <Card.Content>
                        <div className="text-center py-12">
                            <i className="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                            <p className="text-gray-500 mb-4">
                                {selectedTab === 'all' 
                                    ? 'No reservations found' 
                                    : `No ${selectedTab} reservations found`
                                }
                            </p>
                            <Button variant="primary" icon="fas fa-plus">
                                Make a Reservation
                            </Button>
                        </div>
                    </Card.Content>
                </Card>
            )}

            {/* Cancel Confirmation Modal */}
            <Modal
                isOpen={showCancelModal}
                onClose={handleCloseModal}
                title="Cancel Reservation"
                size="small"
            >
                {reservationToCancel && (
                    <div className="space-y-4">
                        <Alert type="warning">
                            Are you sure you want to cancel this reservation? This action cannot be undone.
                        </Alert>
                        
                        <div className="bg-gray-50 p-4 rounded-lg">
                            <h4 className="font-medium text-gray-900 mb-2">
                                Reservation Details
                            </h4>
                            <div className="text-sm text-gray-600 space-y-1">
                                <p><strong>Spot:</strong> #{reservationToCancel.parking_spot_id}</p>
                                <p><strong>Start:</strong> {formatDateTime(reservationToCancel.start_time)}</p>
                                <p><strong>End:</strong> {formatDateTime(reservationToCancel.end_time)}</p>
                                <p><strong>Cost:</strong> {formatCurrency(reservationToCancel.total_cost)}</p>
                            </div>
                        </div>
                        
                        <div className="flex justify-end space-x-3">
                            <Button
                                onClick={handleCloseModal}
                                variant="outline"
                                disabled={cancelLoading}
                            >
                                Keep Reservation
                            </Button>
                            <Button
                                onClick={handleConfirmCancel}
                                variant="danger"
                                loading={cancelLoading}
                                icon="fas fa-times"
                            >
                                {cancelLoading ? 'Cancelling...' : 'Cancel Reservation'}
                            </Button>
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
};

export default ReservationsPage;