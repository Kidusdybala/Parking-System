import { createContext, useContext, useState, useEffect } from 'react';
import { useAuth } from './AuthContext';
import axios from 'axios';

const ParkingContext = createContext();

export const useParking = () => {
    const context = useContext(ParkingContext);
    if (!context) {
        throw new Error('useParking must be used within a ParkingProvider');
    }
    return context;
};

export const ParkingProvider = ({ children }) => {
    const { user } = useAuth();
    const [parkingSpots, setParkingSpots] = useState([]);
    const [userReservations, setUserReservations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [reservationTimers, setReservationTimers] = useState(new Map());

    // Fetch parking spots from API
    const fetchParkingSpots = async () => {
        try {
            // Add cache busting parameter
            const response = await axios.get(`/api/parking-spots?_t=${Date.now()}`);
            if (response.data.success) {
                setParkingSpots(response.data.data.data || response.data.data);
            }
        } catch (error) {
            console.error('Error fetching parking spots:', error);
        }
    };

    // Fetch user reservations from API
    const fetchUserReservations = async () => {
        if (!user) return;
        try {
            const response = await axios.get('/api/reservations');
            if (response.data.success) {
                setUserReservations(response.data.data.data || response.data.data);
            }
        } catch (error) {
            console.error('Error fetching reservations:', error);
        }
    };

    // Initial data fetch
    useEffect(() => {
        const loadData = async () => {
            setLoading(true);
            await fetchParkingSpots();
            if (user) {
                await fetchUserReservations();
            }
            setLoading(false);
        };

        loadData();
        
        // Set up periodic refresh
        const interval = setInterval(() => {
            fetchParkingSpots();
            if (user) {
                fetchUserReservations();
            }
        }, 30000); // Refresh every 30 seconds

        return () => clearInterval(interval);
    }, [user]);

    // Cleanup reservation timers on unmount
    useEffect(() => {
        return () => {
            reservationTimers.forEach(timerId => clearTimeout(timerId));
        };
    }, [reservationTimers]);

    // Function to expire a reservation (for frontend timer)
    const expireReservation = async (reservationId) => {
        try {
            await axios.post(`/api/reservations/${reservationId}/cancel`);
            await fetchParkingSpots();
            await fetchUserReservations();
            
            // Remove the timer
            setReservationTimers(prev => {
                const newTimers = new Map(prev);
                const timerId = newTimers.get(reservationId);
                if (timerId) {
                    clearTimeout(timerId);
                    newTimers.delete(reservationId);
                }
                return newTimers;
            });

            alert('Your reservation has expired after 30 minutes and has been automatically cancelled.');
        } catch (error) {
            console.error('Error expiring reservation:', error);
        }
    };

    // Get user's reservations (formatted for ReservationsPage)
    const getUserReservations = () => {
        return userReservations;
    };

    // Reserve a parking spot
    const handleReserveSpot = async (spotId) => {
        if (!user) return;

        try {
            const response = await axios.post('/api/reservations', {
                parking_spot_id: spotId
            });

            if (response.data.success) {
                const reservation = response.data.data;
                
                // Set 30-minute expiration timer
                const timerId = setTimeout(() => {
                    expireReservation(reservation.id);
                }, 30 * 60 * 1000); // 30 minutes in milliseconds

                setReservationTimers(prev => {
                    const newTimers = new Map(prev);
                    newTimers.set(reservation.id, timerId);
                    return newTimers;
                });

                // Refresh data
                await fetchParkingSpots();
                await fetchUserReservations();
                
                alert('Parking spot reserved successfully! You have 30 minutes to start parking.');
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to reserve parking spot';
            alert(message);
        }
    };

    // Start parking
    const handleStartParking = async (spotId) => {
        if (!user) return;

        try {
            // Find the reservation for this spot
            const reservation = userReservations.find(r => 
                r.parking_spot_id === spotId && r.status === 'reserved'
            );

            if (!reservation) {
                alert('No active reservation found for this spot.');
                return;
            }

            const response = await axios.post(`/api/reservations/${reservation.id}/start`);

            if (response.data.success) {
                // Clear the reservation timer since parking has started
                setReservationTimers(prev => {
                    const newTimers = new Map(prev);
                    const timerId = newTimers.get(reservation.id);
                    if (timerId) {
                        clearTimeout(timerId);
                        newTimers.delete(reservation.id);
                    }
                    return newTimers;
                });

                // Refresh data
                await fetchParkingSpots();
                await fetchUserReservations();
                
                alert('Parking started! Timer is now running.');
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to start parking';
            alert(message);
        }
    };

    // Cancel reservation
    const handleCancelReservation = async (spotId) => {
        if (!user) return;

        try {
            // Find the reservation for this spot
            const reservation = userReservations.find(r => 
                r.parking_spot_id === spotId && r.status === 'reserved'
            );

            if (!reservation) {
                alert('No active reservation found for this spot.');
                return;
            }

            const response = await axios.post(`/api/reservations/${reservation.id}/cancel`);

            if (response.data.success) {
                // Clear the reservation timer
                setReservationTimers(prev => {
                    const newTimers = new Map(prev);
                    const timerId = newTimers.get(reservation.id);
                    if (timerId) {
                        clearTimeout(timerId);
                        newTimers.delete(reservation.id);
                    }
                    return newTimers;
                });

                // Refresh data
                await fetchParkingSpots();
                await fetchUserReservations();
                
                // Reservation cancelled successfully
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to cancel reservation';
            console.error('Cancel reservation error:', message);
        }
    };

    // End parking
    const handleEndParking = async (spotId, onComplete) => {
        if (!user) return;

        try {
            // Find the active reservation for this spot
            const reservation = userReservations.find(r => 
                r.parking_spot_id === spotId && r.status === 'active'
            );

            if (!reservation) {
                console.error('No active parking session found for this spot.');
                return;
            }

            const response = await axios.post(`/api/reservations/${reservation.id}/end`);

            if (response.data.success) {
                const completedSession = response.data.data;
                
                // Refresh data
                await fetchParkingSpots();
                await fetchUserReservations();
                
                // Call completion callback if provided
                if (onComplete) {
                    onComplete({
                        id: reservation.id,
                        spotNumber: reservation.parking_spot?.spot_number,
                        startTime: new Date(reservation.actual_start_time || reservation.start_time),
                        endTime: new Date(),
                        totalCost: completedSession.total_cost,
                        duration: completedSession.duration_hours
                    });
                }
                
                console.log(`Parking completed! Total cost: ${completedSession.total_cost} birr`);
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to end parking';
            console.error('End parking error:', message);
        }
    };

    // Get remaining time for a reservation (in minutes)
    const getReservationTimeRemaining = (spotId) => {
        const reservation = userReservations.find(r => 
            r.parking_spot_id === spotId && r.status === 'reserved'
        );
        
        if (!reservation || !reservation.reservation_expires_at) {
            return 0;
        }

        const expiryTime = new Date(reservation.reservation_expires_at);
        const now = new Date();
        const remainingMs = expiryTime - now;
        
        const remainingMinutes = Math.max(0, Math.ceil(remainingMs / (60 * 1000)));
        
        // If time has expired, trigger expiration immediately
        if (remainingMs <= 0) {
            setTimeout(() => {
                expireReservation(reservation.id);
            }, 0);
        }
        
        return remainingMinutes;
    };

    // Manual release function for debugging/admin purposes
    const manualReleaseSpot = async (spotId) => {
        if (!user) return;

        try {
            // Find any reservation for this spot
            const reservation = userReservations.find(r => r.parking_spot_id === spotId);

            if (reservation) {
                if (reservation.status === 'reserved') {
                    await handleCancelReservation(spotId);
                } else if (reservation.status === 'active') {
                    await handleEndParking(spotId);
                }
            }
        } catch (error) {
            console.error('Error releasing spot:', error);
            alert('Failed to release spot');
        }
    };

    // Clear all user reservations (debug function)
    const clearAllReservations = async () => {
        if (!user) return;

        try {
            const response = await axios.post('/api/reservations/debug/clear-all');
            if (response.data.success) {
                alert(`Cleared ${response.data.cleared_count} reservations successfully!`);
                // Refresh data
                await fetchParkingSpots();
                await fetchUserReservations();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to clear reservations';
            alert(message);
        }
    };

    // Get active parking sessions (for dashboard display)
    const getActiveParkingSessions = () => {
        const activeSessions = new Map();
        
        userReservations.forEach(reservation => {
            if (reservation.status === 'active' && reservation.actual_start_time) {
                activeSessions.set(reservation.parking_spot_id, {
                    spotId: reservation.parking_spot_id,
                    spotNumber: reservation.parking_spot?.spot_number,
                    hourlyRate: reservation.parking_spot?.hourly_rate || 30,
                    startTime: new Date(reservation.actual_start_time),
                    userId: reservation.user_id
                });
            }
        });
        
        return activeSessions;
    };

    const value = {
        parkingSpots,
        setParkingSpots,
        userReservations,
        loading,
        activeParkingSessions: getActiveParkingSessions(),
        completedSessions: [], // Could be implemented if needed
        getUserReservations,
        handleReserveSpot,
        handleStartParking,
        handleCancelReservation,
        handleEndParking,
        getReservationTimeRemaining,
        manualReleaseSpot,
        clearAllReservations,
        fetchParkingSpots,
        fetchUserReservations
    };

    return (
        <ParkingContext.Provider value={value}>
            {children}
        </ParkingContext.Provider>
    );
};