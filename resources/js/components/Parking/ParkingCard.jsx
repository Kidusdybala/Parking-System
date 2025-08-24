import { useState } from 'react';
import Card from '../ui/Card';
import Badge from '../ui/Badge';
import Button from '../ui/Button';
import axios from 'axios';

const statusVariant = (status) => {
    switch (status) {
        case 'available':
            return 'success';
        case 'occupied':
            return 'danger';
        case 'reserved':
            return 'warning';
        case 'maintenance':
            return 'warning';
        default:
            return 'default';
    }
};

const ParkingCard = ({ spot, onReserve, userReservations = [], user, onUpdate }) => {
    const [isLoading, setIsLoading] = useState(false);
    
    // Check if current user has a reservation for this spot
    const userReservation = userReservations.find(
        reservation => reservation.parking_spot_id === spot.id && 
        ['active', 'reserved'].includes(reservation.status)
    );

    const handleAction = async (actionType) => {
        setIsLoading(true);
        try {
            if (actionType === 'reserve') {
                const response = await axios.post('/api/reservations', {
                    parking_spot_id: spot.id
                });
                
                if (response.data.success) {
                    alert('Parking spot reserved successfully! You can now start parking.');
                    if (onUpdate) onUpdate();
                } else {
                    alert(response.data.message || 'Failed to reserve spot');
                }
            } else if (actionType === 'start' && userReservation) {
                const response = await axios.post(`/api/reservations/${userReservation.id}/start`);
                
                if (response.data.success) {
                    alert('Parking started! Timer is now running at 30 birr/hour.');
                    if (onUpdate) onUpdate();
                } else {
                    alert(response.data.message || 'Failed to start parking');
                }
            } else if (actionType === 'end' && userReservation) {
                const response = await axios.post(`/api/reservations/${userReservation.id}/end`);
                
                if (response.data.success) {
                    const { duration_hours, total_cost, remaining_balance } = response.data.data;
                    alert(`Parking completed!\n\nDuration: ${duration_hours} hour(s)\nCost: ${total_cost} birr\nRemaining balance: ${remaining_balance} birr`);
                    if (onUpdate) onUpdate();
                } else {
                    alert(response.data.message || 'Failed to end parking');
                }
            } else if (actionType === 'cancel' && userReservation) {
                const response = await axios.post(`/api/reservations/${userReservation.id}/cancel`);
                
                if (response.data.success) {
                    alert('Reservation cancelled successfully!');
                    if (onUpdate) onUpdate();
                } else {
                    alert(response.data.message || 'Failed to cancel reservation');
                }
            }
        } catch (error) {
            console.error('Action failed:', error);
            alert(error.response?.data?.message || 'Action failed. Please try again.');
        } finally {
            setIsLoading(false);
        }
    };

    const renderActionButtons = () => {
        if (!userReservation) {
            // No reservation - show reserve button if available
            const canReserve = spot.status === 'available';
            return (
                <Button
                    onClick={() => handleAction('reserve')}
                    disabled={!canReserve || isLoading}
                    loading={isLoading}
                    variant={canReserve ? "primary" : "secondary"}
                    className="w-full text-sm py-2"
                >
                    {canReserve ? 'Reserve Now' : 'Not Available'}
                </Button>
            );
        }

        // Has reservation - show appropriate actions based on status
        if (userReservation.status === 'reserved') {
            // Reserved but not started parking yet
            return (
                <div className="space-y-1">
                    <Button
                        onClick={() => handleAction('start')}
                        disabled={isLoading}
                        loading={isLoading}
                        variant="primary"
                        className="w-full text-sm py-2"
                    >
                        🚗 Start Parking
                    </Button>
                    <Button
                        onClick={() => handleAction('cancel')}
                        disabled={isLoading}
                        variant="outline"
                        className="w-full text-sm py-1 text-red-600 border-red-300 hover:bg-red-50"
                    >
                        Cancel
                    </Button>
                </div>
            );
        } else if (userReservation.status === 'active') {
            // Currently parking
            const startTime = userReservation.actual_start_time || userReservation.start_time;
            return (
                <div className="space-y-1">
                    <div className="text-center text-xs text-blue-600 mb-1">
                        ⏱️ Parking in progress
                        <br />
                        <span className="font-medium">{spot.hourly_rate} birr/hour</span>
                    </div>
                    <Button
                        onClick={() => handleAction('end')}
                        disabled={isLoading}
                        loading={isLoading}
                        variant="primary"
                        className="w-full text-sm py-2 bg-red-600 hover:bg-red-700"
                    >
                        🏁 End & Pay
                    </Button>
                </div>
            );
        }

        return (
            <div className="text-center text-sm text-gray-600">
                Reservation Status: {userReservation.status}
            </div>
        );
    };

    return (
        <Card hover className="h-full">
            <Card.Header className="pb-2">
                <div className="flex items-center justify-between">
                    <Card.Title className="text-lg">{spot.spot_number || `SPOT-${spot.id.toString().padStart(3, '0')}`}</Card.Title>
                    <Badge variant={statusVariant(spot.status)}>
                        {spot.status}
                    </Badge>
                </div>
            </Card.Header>
            <Card.Content className="pb-3">
                <div className="space-y-1 text-sm text-gray-600">
                    <div className="flex items-center justify-between">
                        <span className="font-medium">Rate:</span>
                        <span className="text-primary font-semibold">{spot.hourly_rate || spot.price_per_hour} birr/hour</span>
                    </div>
                    <div className="flex items-center justify-between">
                        <span className="font-medium">Location:</span>
                        <span className="text-xs">{spot.location?.split(' - ')[0] || 'N/A'}</span>
                    </div>
                </div>
                
                {userReservation && (
                    <div className="mt-2 bg-blue-50 p-2 rounded text-xs">
                        <p className="text-blue-800 font-medium">Your Reservation</p>
                        <p className="text-blue-600">Status: {userReservation.status}</p>
                    </div>
                )}
            </Card.Content>
            <Card.Footer>
                {renderActionButtons()}
            </Card.Footer>
        </Card>
    );
};

export default ParkingCard;
