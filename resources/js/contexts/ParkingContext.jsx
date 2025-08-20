import { createContext, useContext, useState, useEffect } from 'react';
import { useAuth } from './AuthContext';

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
    const [activeParkingSessions, setActiveParkingSessions] = useState(new Map());
    const [completedSessions, setCompletedSessions] = useState([]);
    const [reservationTimers, setReservationTimers] = useState(new Map());

    // Global spot state management (simulating backend persistence)
    const getGlobalSpotState = () => {
        const stored = localStorage.getItem('globalParkingSpots');
        if (stored) {
            const parsed = JSON.parse(stored);
            // Convert date strings back to Date objects
            return parsed.map(spot => ({
                ...spot,
                start_time: spot.start_time ? new Date(spot.start_time) : null,
                reserved_at: spot.reserved_at ? new Date(spot.reserved_at) : null
            }));
        }
        return null;
    };

    const setGlobalSpotState = (spots) => {
        localStorage.setItem('globalParkingSpots', JSON.stringify(spots));
        // Broadcast change to other tabs/windows
        window.dispatchEvent(new CustomEvent('parkingSpotsUpdated', { detail: spots }));
    };

    const generateMockSpots = () => {
        // Check if global state already exists
        const existingSpots = getGlobalSpotState();
        if (existingSpots) {
            return existingSpots;
        }

        const spots = [];
        const sections = ['Section A', 'Section B', 'Section C', 'Section D', 'Section E'];
        
        for (let i = 1; i <= 100; i++) {
            const sectionIndex = Math.floor((i - 1) / 20);
            const spotInSection = ((i - 1) % 20) + 1;
            const section = sections[sectionIndex];
            
            // Pricing structure based on spot number
            let hourlyRate;
            if (i <= 20) {
                hourlyRate = 30; // Spots 1-20: 30 birr/hour
            } else if (i <= 40) {
                hourlyRate = 25; // Spots 21-40: 25 birr/hour
            } else {
                hourlyRate = 20; // Spots 41-100: 20 birr/hour
            }
            
            spots.push({
                id: i,
                spot_number: `${section.charAt(section.length - 1)}-${spotInSection.toString().padStart(2, '0')}`,
                location: section,
                status: 'available', // All spots start as available
                hourly_rate: hourlyRate,
                name: `Spot ${section.charAt(section.length - 1)}-${spotInSection.toString().padStart(2, '0')}`,
                reservation_id: null,
                reserved_by: null,
                occupied_by: null,
                start_time: null,
                reserved_at: null
            });
        }
        
        // Save initial state
        setGlobalSpotState(spots);
        return spots;
    };

    // Initialize parking spots
    useEffect(() => {
        const spots = generateMockSpots();
        setParkingSpots(spots);

        // Listen for parking spot updates from other tabs/windows
        const handleParkingSpotsUpdate = (event) => {
            const updatedSpots = event.detail;
            setParkingSpots(updatedSpots);
        };

        window.addEventListener('parkingSpotsUpdated', handleParkingSpotsUpdate);

        return () => {
            window.removeEventListener('parkingSpotsUpdated', handleParkingSpotsUpdate);
        };
    }, []);

    // Cleanup reservation timers on unmount
    useEffect(() => {
        return () => {
            // Clear all reservation timers
            reservationTimers.forEach(timerId => clearTimeout(timerId));
        };
    }, [reservationTimers]);

    // Function to expire a reservation
    const expireReservation = (spotId) => {
        const updatedSpots = parkingSpots.map(spot => 
            spot.id === spotId 
                ? { 
                    ...spot, 
                    status: 'available', 
                    reservation_id: null,
                    reserved_by: null,
                    reserved_at: null
                }
                : spot
        );

        setParkingSpots(updatedSpots);
        setGlobalSpotState(updatedSpots);

        // Remove the timer
        setReservationTimers(prev => {
            const newTimers = new Map(prev);
            const timerId = newTimers.get(spotId);
            if (timerId) {
                clearTimeout(timerId);
                newTimers.delete(spotId);
            }
            return newTimers;
        });

        // Notify user if they're still logged in
        if (user) {
            const spot = parkingSpots.find(s => s.id === spotId);
            if (spot && String(spot.reserved_by) === String(user?.id)) {
                alert(`Your reservation for ${spot.spot_number} has expired after 2 minutes and has been automatically released.`);
            }
        }
    };

    // Get user's reservations (formatted for ReservationsPage)
    const getUserReservations = () => {
        if (!user) return [];

        const userReservedSpots = parkingSpots.filter(spot => 
            (spot.status === 'reserved' && String(spot.reserved_by) === String(user?.id)) ||
            (spot.status === 'occupied' && String(spot.occupied_by) === String(user?.id))
        );

        const userCompletedSessions = completedSessions.filter(session => 
            String(session.userId) === String(user?.id)
        );

        // Convert to reservation format
        const reservations = [
            // Active reservations
            ...userReservedSpots.map(spot => ({
                id: spot.reservation_id || spot.id,
                status: spot.status === 'reserved' ? 'active' : 'active',
                parking_spot: {
                    id: spot.id,
                    spot_number: spot.spot_number,
                    location: spot.location
                },
                start_time: spot.reserved_at || spot.start_time,
                end_time: null, // We don't have end time for reservations
                total_cost: 0, // Will be calculated when parking ends
                created_at: spot.reserved_at || spot.start_time,
                updated_at: spot.reserved_at || spot.start_time
            })),
            // Completed sessions
            ...userCompletedSessions.map(session => ({
                id: session.id,
                status: 'completed',
                parking_spot: {
                    id: session.spotId,
                    spot_number: session.spotNumber,
                    location: 'Section ' + session.spotNumber.charAt(0)
                },
                start_time: session.startTime,
                end_time: session.endTime,
                total_cost: session.totalCost,
                created_at: session.startTime,
                updated_at: session.endTime
            }))
        ];

        return reservations.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    };

    // Parking action handlers
    const handleReserveSpot = (spotId) => {
        if (!user) return;

        // Check if user already has 3 reservations
        const currentUserReservations = parkingSpots.filter(spot => 
            spot.status === 'reserved' && String(spot.reserved_by) === String(user?.id)
        ).length;
        
        if (currentUserReservations >= 3) {
            alert('You cannot reserve more than 3 parking spots at a time. Please cancel an existing reservation or complete your parking session first.');
            return;
        }

        // Check if spot is still available
        const currentSpot = parkingSpots.find(spot => spot.id === spotId);
        if (!currentSpot || currentSpot.status !== 'available') {
            alert('This parking spot is no longer available. Please choose another spot.');
            return;
        }

        const updatedSpots = parkingSpots.map(spot => 
            spot.id === spotId 
                ? { 
                    ...spot, 
                    status: 'reserved', 
                    reservation_id: Date.now(),
                    reserved_by: user?.id,
                    reserved_at: new Date()
                }
                : spot
        );

        setParkingSpots(updatedSpots);
        setGlobalSpotState(updatedSpots);

        // Set 2-minute expiration timer (for testing - change to 30 minutes in production)
        const timerId = setTimeout(() => {
            expireReservation(spotId);
        }, 2 * 60 * 1000); // 2 minutes in milliseconds (change to 30 * 60 * 1000 for production)

        setReservationTimers(prev => {
            const newTimers = new Map(prev);
            newTimers.set(spotId, timerId);
            return newTimers;
        });
    };

    const handleStartParking = (spotId) => {
        if (!user) return;

        const spot = parkingSpots.find(s => s.id === spotId);
        if (!spot) return;

        // Check if user owns this reservation
        // Convert both to strings for comparison to handle type mismatches
        if (String(spot.reserved_by) !== String(user?.id)) {
            alert('You can only start parking in spots you have reserved.');
            return;
        }

        const startTime = new Date();
        
        // Update spot status to occupied
        const updatedSpots = parkingSpots.map(s => 
            s.id === spotId 
                ? { 
                    ...s, 
                    status: 'occupied', 
                    start_time: startTime,
                    occupied_by: user?.id,
                    reserved_by: null, // Clear reservation
                    reservation_id: null
                }
                : s
        );

        setParkingSpots(updatedSpots);
        setGlobalSpotState(updatedSpots);

        // Clear the reservation timer since parking has started
        setReservationTimers(prev => {
            const newTimers = new Map(prev);
            const timerId = newTimers.get(spotId);
            if (timerId) {
                clearTimeout(timerId);
                newTimers.delete(spotId);
            }
            return newTimers;
        });

        // Start tracking the parking session
        setActiveParkingSessions(prev => {
            const newSessions = new Map(prev);
            newSessions.set(spotId, {
                spotId,
                spotNumber: spot.spot_number,
                hourlyRate: spot.hourly_rate,
                startTime,
                userId: user?.id,
                intervalId: setInterval(() => {
                    // This will trigger re-renders to update the timer display
                    setActiveParkingSessions(current => new Map(current));
                }, 1000)
            });
            return newSessions;
        });
    };

    const handleCancelReservation = (spotId) => {
        if (!user) return;

        const spot = parkingSpots.find(s => s.id === spotId);
        if (!spot) return;

        // Check if user owns this reservation
        // Convert both to strings for comparison to handle type mismatches
        if (String(spot.reserved_by) !== String(user?.id)) {
            alert('You can only cancel your own reservations.');
            return;
        }

        const updatedSpots = parkingSpots.map(s => 
            s.id === spotId 
                ? { 
                    ...s, 
                    status: 'available', 
                    reservation_id: null,
                    reserved_by: null,
                    reserved_at: null
                }
                : s
        );

        setParkingSpots(updatedSpots);
        setGlobalSpotState(updatedSpots);

        // Clear the reservation timer
        setReservationTimers(prev => {
            const newTimers = new Map(prev);
            const timerId = newTimers.get(spotId);
            if (timerId) {
                clearTimeout(timerId);
                newTimers.delete(spotId);
            }
            return newTimers;
        });
    };

    const handleEndParking = (spotId, onComplete) => {
        if (!user) return;

        const session = activeParkingSessions.get(spotId);
        const spot = parkingSpots.find(s => s.id === spotId);
        
        if (!session || !spot) return;

        // Check if user owns this parking session
        // Convert both to strings for comparison to handle type mismatches
        if (String(spot.occupied_by) !== String(user?.id)) {
            alert('You can only end your own parking sessions.');
            return;
        }

        const endTime = new Date();
        const durationMs = endTime - session.startTime;
        const durationHours = durationMs / (1000 * 60 * 60);
        const totalCost = Math.ceil(durationHours * session.hourlyRate);

        // Clear the interval
        clearInterval(session.intervalId);

        // Remove from active sessions
        setActiveParkingSessions(prev => {
            const newSessions = new Map(prev);
            newSessions.delete(spotId);
            return newSessions;
        });

        // Add to completed sessions
        const completedSession = {
            id: Date.now(),
            spotId,
            spotNumber: session.spotNumber,
            hourlyRate: session.hourlyRate,
            startTime: session.startTime,
            endTime,
            durationHours: Math.round(durationHours * 100) / 100,
            totalCost,
            timestamp: new Date(),
            userId: user.id
        };

        setCompletedSessions(prev => [completedSession, ...prev]);

        // Update spot status back to available
        const updatedSpots = parkingSpots.map(s => 
            s.id === spotId 
                ? { 
                    ...s, 
                    status: 'available', 
                    start_time: null,
                    occupied_by: null
                }
                : s
        );

        setParkingSpots(updatedSpots);
        setGlobalSpotState(updatedSpots);

        // Call completion callback
        if (onComplete) {
            onComplete(completedSession);
        }
    };

    // Get remaining time for a reservation (in minutes)
    const getReservationTimeRemaining = (spotId) => {
        const spot = parkingSpots.find(s => s.id === spotId);
        if (!spot || spot.status !== 'reserved' || !spot.reserved_at) {
            return 0;
        }

        const reservedTime = new Date(spot.reserved_at);
        const now = new Date();
        const elapsedMs = now - reservedTime;
        const remainingMs = (2 * 60 * 1000) - elapsedMs; // 2 minutes - elapsed time (change to 30 * 60 * 1000 for production)
        
        const remainingMinutes = Math.max(0, Math.ceil(remainingMs / (60 * 1000)));
        
        // If time has expired, trigger expiration immediately
        if (remainingMs <= 0) {
            // Use a more immediate approach
            setTimeout(() => {
                expireReservation(spotId);
            }, 0);
        }
        
        return remainingMinutes;
    };

    // Manual release function for debugging/admin purposes
    const manualReleaseSpot = (spotId) => {
        const spot = parkingSpots.find(s => s.id === spotId);
        if (!spot) return;

        // Clear any active parking session
        const session = activeParkingSessions.get(spotId);
        if (session) {
            clearInterval(session.intervalId);
            setActiveParkingSessions(prev => {
                const newSessions = new Map(prev);
                newSessions.delete(spotId);
                return newSessions;
            });
        }

        // Clear any reservation timer
        setReservationTimers(prev => {
            const newTimers = new Map(prev);
            const timerId = newTimers.get(spotId);
            if (timerId) {
                clearTimeout(timerId);
                newTimers.delete(spotId);
            }
            return newTimers;
        });

        // Reset spot to available
        const updatedSpots = parkingSpots.map(s => 
            s.id === spotId 
                ? { 
                    ...s, 
                    status: 'available', 
                    reservation_id: null,
                    reserved_by: null,
                    reserved_at: null,
                    start_time: null,
                    occupied_by: null
                }
                : s
        );

        setParkingSpots(updatedSpots);
        setGlobalSpotState(updatedSpots);
    };

    const value = {
        parkingSpots,
        setParkingSpots,
        activeParkingSessions,
        setActiveParkingSessions,
        completedSessions,
        setCompletedSessions,
        getUserReservations,
        handleReserveSpot,
        handleStartParking,
        handleCancelReservation,
        handleEndParking,
        setGlobalSpotState,
        getReservationTimeRemaining,
        manualReleaseSpot
    };

    return (
        <ParkingContext.Provider value={value}>
            {children}
        </ParkingContext.Provider>
    );
};