import { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { useParking } from '../contexts/ParkingContext';
import { useApi } from '../hooks/useApi';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const DashboardPage = () => {
    const { user } = useAuth();
    const { get, loading } = useApi();
    const navigate = useNavigate();
    const {
        parkingSpots,
        activeParkingSessions,
        userReservations,
        handleReserveSpot,
        handleStartParking,
        handleCancelReservation,
        handleEndParking,
        getReservationTimeRemaining,
        manualReleaseSpot,
        clearAllReservations,
        loading: parkingLoading
    } = useParking();
    
    const [stats, setStats] = useState({
        availableSpots: 0,
        totalHours: 0,
        totalSpent: 0
    });
    const [activeSection, setActiveSection] = useState(1);
    const [currentPage, setCurrentPage] = useState(1);
    const [reservationUpdateTrigger, setReservationUpdateTrigger] = useState(0);
    const spotsPerPage = 20;
    const totalPages = Math.ceil((Array.isArray(parkingSpots) ? parkingSpots.length : 0) / spotsPerPage);

    useEffect(() => {
        fetchDashboardData();
        
        // Set up timer to update reservation countdowns every 10 seconds
        const reservationTimer = setInterval(() => {
            setReservationUpdateTrigger(prev => prev + 1);
        }, 10000); // Update every 10 seconds

        return () => {
            clearInterval(reservationTimer);
        };
    }, []);

    // Cleanup intervals on unmount
    useEffect(() => {
        return () => {
            activeParkingSessions.forEach(session => {
                if (session.intervalId) {
                    clearInterval(session.intervalId);
                }
            });
        };
    }, [activeParkingSessions]);

    // Update stats when parking spots change
    useEffect(() => {
        if (parkingSpots.length > 0) {
            setStats(prev => ({
                ...prev,
                availableSpots: parkingSpots.filter(spot => spot.status === 'available').length
            }));
        }
    }, [parkingSpots]);

    // Update user stats when reservations change
    useEffect(() => {
        calculateUserStats();
    }, [userReservations, user?.id]);



    const fetchDashboardData = async () => {
        try {
            // Calculate user statistics from reservation history
            await calculateUserStats();
            // Stats will be updated by the useEffect that watches parkingSpots
            // The parking spots are managed by the ParkingContext
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    };

    const calculateUserStats = async () => {
        try {
            console.log('Calculating user stats for user:', user?.id);
            console.log('User reservations:', userReservations);

            // Get user's completed reservations to calculate total hours and spent
            const completedReservations = userReservations.filter(reservation => {
                const isCompleted = reservation.status === 'completed';
                const isUserReservation = String(reservation.user_id) === String(user?.id);
                console.log(`Reservation ${reservation.id}: status=${reservation.status}, user_id=${reservation.user_id}, isCompleted=${isCompleted}, isUserReservation=${isUserReservation}`);
                return isCompleted && isUserReservation;
            });

            console.log('Completed reservations for user:', completedReservations);

            let totalHours = 0;
            let totalSpent = 0;

            completedReservations.forEach(reservation => {
                console.log('Processing reservation:', reservation);

                // Try different time field combinations
                let startTime = null;
                let endTime = null;

                if (reservation.actual_start_time && reservation.actual_end_time) {
                    startTime = new Date(reservation.actual_start_time);
                    endTime = new Date(reservation.actual_end_time);
                } else if (reservation.start_time && reservation.end_time) {
                    startTime = new Date(reservation.start_time);
                    endTime = new Date(reservation.end_time);
                }

                if (startTime && endTime && !isNaN(startTime) && !isNaN(endTime)) {
                    const durationMs = endTime - startTime;
                    const durationHours = durationMs / (1000 * 60 * 60);
                    totalHours += durationHours;

                    console.log(`Duration calculated: ${durationHours} hours`);

                    // Use total_cost if available, otherwise calculate
                    if (reservation.total_cost) {
                        totalSpent += parseFloat(reservation.total_cost);
                        console.log(`Using total_cost: ${reservation.total_cost}`);
                    } else {
                        // Calculate cost based on hourly rate and duration
                        const hourlyRate = reservation.hourly_rate || reservation.parking_spot?.hourly_rate || 10;
                        const calculatedCost = Math.ceil(durationHours * hourlyRate);
                        totalSpent += calculatedCost;
                        console.log(`Calculated cost: ${calculatedCost} (rate: ${hourlyRate})`);
                    }
                } else {
                    console.log('Invalid time data for reservation:', reservation);
                }
            });

            console.log(`Final stats - Hours: ${totalHours}, Spent: ${totalSpent}`);

            setStats(prev => ({
                ...prev,
                totalHours: parseFloat(totalHours.toFixed(1)),
                totalSpent: totalSpent
            }));
        } catch (error) {
            console.error('Error calculating user stats:', error);
        }
    };

    const handleSectionChange = (section) => {
        setActiveSection(section);
    };

    const handleNewReservation = () => {
        // Logic to make a new reservation
        console.log('Making new reservation...');
    };

    if (loading) {
        return <LoadingSpinner message="Loading dashboard..." />;
    }

    // Calculate pagination
    const startIndex = (currentPage - 1) * spotsPerPage;
    const endIndex = startIndex + spotsPerPage;
    const currentPageSpots = Array.isArray(parkingSpots) ? parkingSpots.slice(startIndex, endIndex) : [];
    
    // Group current page spots by section
    const groupedSpots = currentPageSpots.reduce((acc, spot) => {
        const section = spot.location || 'Unknown';
        if (!acc[section]) acc[section] = [];
        acc[section].push(spot);
        return acc;
    }, {});

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    const handlePrevPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1);
        }
    };

    const handleNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1);
        }
    };





    const showReceipt = (session) => {
        // Navigate to receipt page with session data
        navigate('/receipt', { state: { session } });
    };

    const formatDuration = (startTime) => {
        const now = new Date();
        const durationMs = now - startTime;
        const hours = Math.floor(durationMs / (1000 * 60 * 60));
        const minutes = Math.floor((durationMs % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((durationMs % (1000 * 60)) / 1000);
        
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    };

    const calculateCurrentCost = (startTime, hourlyRate) => {
        const now = new Date();
        const durationMs = now - startTime;
        const durationHours = durationMs / (1000 * 60 * 60);
        return Math.ceil(durationHours * hourlyRate);
    };

    return (
        <div className="flex-1 container py-8">
            <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                {/* Sidebar */}
                <div className="lg:col-span-1">
                    <div className="sticky top-24">
                        <div className="glass-card p-4 mb-4">
                            {/* User Info */}
                            <div className="flex items-center space-x-4 mb-6">
                                <div className="h-14 w-14 rounded-full bg-parkBlue-700 flex items-center justify-center text-xl">
                                    <i className="fas fa-user"></i>
                                </div>
                                <div>
                                    <h2 className="font-bold">{user?.name || 'John Doe'}</h2>
                                    <p className="text-sm text-muted-foreground">{user?.email || 'john.doe@example.com'}</p>
                                </div>
                            </div>
                            
                            {/* Navigation */}
                            <nav className="space-y-1">
                                <Link to="/dashboard" className="nav-link active flex items-center w-full">
                                    <i className="fas fa-th-large w-5"></i>
                                    <span className="ml-3">Dashboard</span>
                                </Link>
                                <Link to="/reservations" className="nav-link flex items-center w-full">
                                    <i className="fas fa-ticket-alt w-5"></i>
                                    <span className="ml-3">My Reservations</span>
                                </Link>
                                <Link to="/reservations" className="nav-link flex items-center w-full">
                                    <i className="fas fa-history w-5"></i>
                                    <span className="ml-3">Booking History</span>
                                </Link>
                                <Link to="/profile" className="nav-link flex items-center w-full">
                                    <i className="fas fa-credit-card w-5"></i>
                                    <span className="ml-3">Payment Methods</span>
                                </Link>
                                <Link to="/profile" className="nav-link flex items-center w-full">
                                    <i className="fas fa-cog w-5"></i>
                                    <span className="ml-3">Account Settings</span>
                                </Link>
                            </nav>
                        </div>
                        
                        {/* Current Status Card */}
                        <div className="glass-card p-4">
                            <h3 className="font-bold mb-4">Current Status</h3>
                            <div className="space-y-3">
                                {(() => {
                                    // Find user's reserved spots (from reservations, not spot status)
                                    const reservedSpots = parkingSpots.filter(spot => {
                                        // Check if user has a reserved reservation for this spot
                                        return userReservations.some(reservation => 
                                            reservation.parking_spot_id === spot.id && 
                                            reservation.status === 'reserved' &&
                                            String(reservation.user_id) === String(user?.id)
                                        );
                                    });
                                    // Find user's occupied spots (from active reservations)
                                    const occupiedSpots = parkingSpots.filter(spot => {
                                        // Check if user has an active reservation for this spot
                                        return userReservations.some(reservation => 
                                            reservation.parking_spot_id === spot.id && 
                                            reservation.status === 'active' &&
                                            String(reservation.user_id) === String(user?.id)
                                        );
                                    });
                                    
                                    if (occupiedSpots.length > 0) {
                                        const spot = occupiedSpots[0];
                                        const session = activeParkingSessions.get(spot.id);
                                        return (
                                            <div className="bg-parkBlue-800/50 rounded-lg p-4">
                                                <div className="flex items-center justify-between mb-2">
                                                    <span className="text-sm text-muted-foreground">Active Parking</span>
                                                    <span className="badge badge-occupied px-2 py-1">Parking</span>
                                                </div>
                                                <h4 className="font-bold">{spot.spot_number}</h4>
                                                {session && (
                                                    <>
                                                        <div className="text-sm mb-2">
                                                            <span className="text-muted-foreground">Time Elapsed:</span>
                                                            <span className="ml-1 text-primary">{formatDuration(session.startTime)}</span>
                                                        </div>
                                                        <div className="text-sm mb-2">
                                                            <span className="text-muted-foreground">Current Fee:</span>
                                                            <span className="ml-1">{calculateCurrentCost(session.startTime, session.hourlyRate)} Birr</span>
                                                        </div>
                                                    </>
                                                )}
                                                <div className="flex justify-between text-sm mb-3">
                                                    <span className="text-muted-foreground">Hourly Rate:</span>
                                                    <span>{spot.hourly_rate} Birr</span>
                                                </div>
                                                <div className="flex space-x-2">
                                                    <button 
                                                        className="btn btn-danger w-full text-sm py-2"
                                                        onClick={() => handleEndParking(spot.id, showReceipt)}
                                                    >
                                                        <i className="fas fa-stop-circle mr-1"></i> End Parking
                                                    </button>
                                                </div>
                                            </div>
                                        );
                                    } else if (reservedSpots.length > 0) {
                                        const spot = reservedSpots[0];
                                        return (
                                            <div className="bg-parkBlue-800/50 rounded-lg p-4">
                                                <div className="flex items-center justify-between mb-2">
                                                    <span className="text-sm text-muted-foreground">Active Reservation</span>
                                                    <span className="badge badge-reserved px-2 py-1">Reserved</span>
                                                </div>
                                                <h4 className="font-bold">{spot.spot_number}</h4>
                                                <div className="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded mb-2">
                                                    Expires in: {getReservationTimeRemaining(spot.id)} minutes
                                                </div>
                                                <div className="flex justify-between text-sm mb-3">
                                                    <span className="text-muted-foreground">Hourly Rate:</span>
                                                    <span>{spot.hourly_rate} Birr</span>
                                                </div>
                                                <div className="flex space-x-2">
                                                    <button 
                                                        className="btn btn-primary flex-1 text-sm py-1"
                                                        onClick={() => handleStartParking(spot.id)}
                                                    >
                                                        <i className="fas fa-play-circle mr-1"></i> Start Parking
                                                    </button>
                                                    <button 
                                                        className="btn btn-danger flex-1 text-sm py-1"
                                                        onClick={() => handleCancelReservation(spot.id)}
                                                    >
                                                        <i className="fas fa-times-circle mr-1"></i> Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        );
                                    } else {
                                        return (
                                            <div className="bg-parkBlue-800/50 rounded-lg p-4 text-center">
                                                <p className="text-muted-foreground">You don't have any active reservations</p>
                                                <button 
                                                    className="mt-2 text-primary text-sm hover:underline"
                                                    onClick={handleNewReservation}
                                                >
                                                    <i className="fas fa-plus-circle mr-1"></i> Make a reservation
                                                </button>
                                            </div>
                                        );
                                    }
                                })()}
                            </div>
                        </div>
                    </div>
                </div>
                
                {/* Main Content Area */}
                <div className="lg:col-span-3 space-y-8">
                    {/* Quick Stats */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div className="glass-card p-4">
                            <div className="flex items-center">
                                <div className="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                                    <i className="fas fa-car text-primary"></i>
                                </div>
                                <div>
                                    <p className="text-muted-foreground text-sm">Available Slots</p>
                                    <p className="text-2xl font-bold">{stats.availableSpots}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div className="glass-card p-4">
                            <div className="flex items-center">
                                <div className="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                                    <i className="fas fa-clock text-primary"></i>
                                </div>
                                <div>
                                    <p className="text-muted-foreground text-sm">Total Hours Parked</p>
                                    <p className="text-2xl font-bold">{stats.totalHours}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div className="glass-card p-4">
                            <div className="flex items-center">
                                <div className="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                                    <i className="fas fa-wallet text-primary"></i>
                                </div>
                                <div>
                                    <p className="text-muted-foreground text-sm">Total Spent</p>
                                    <p className="text-2xl font-bold">{stats.totalSpent} birr</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {/* Parking Map Section */}
                    <div className="card-container">
                        <div className="flex justify-between items-center mb-6">
                            <div>
                                <h2 className="text-xl font-bold">Parking Slots</h2>
                                <p className="text-sm text-muted-foreground">
                                    Page {currentPage} of {totalPages} • {currentPageSpots.length} spots shown
                                </p>

                            </div>
                            <div className="flex items-center space-x-4">
                                <div className="flex items-center space-x-2">
                                    <div className="h-3 w-3 rounded-full bg-parkGreen"></div>
                                    <span className="text-sm text-muted-foreground">Available</span>
                                </div>
                                <div className="flex items-center space-x-2">
                                    <div className="h-3 w-3 rounded-full bg-parkRed"></div>
                                    <span className="text-sm text-muted-foreground">Occupied</span>
                                </div>
                                <div className="flex items-center space-x-2">
                                    <div className="h-3 w-3 rounded-full bg-yellow-500"></div>
                                    <span className="text-sm text-muted-foreground">Reserved</span>
                                </div>
                            
                                <button
                                    onClick={() => {
                                        fetchDashboardData();
                                        window.location.reload();
                                    }}
                                    className="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 transition-colors"
                                    title="Refresh data"
                                >
                                    Refresh
                                </button>
                            </div>
                        </div>
                        
                        {/* Quick Actions */}
                        <div className="flex flex-wrap gap-4 mb-6">
                            <Link 
                                to="/parking" 
                                className="btn btn-primary"
                            >
                                <i className="fas fa-search mr-2"></i>
                                View All Spots
                            </Link>
                            <Link 
                                to="/reservations" 
                                className="btn btn-outline"
                            >
                                <i className="fas fa-ticket-alt mr-2"></i>
                                My Reservations
                            </Link>
                        </div>
                        
                        {/* Real Parking Spots */}
                        <div className="fade-in">
                            <div className="grid grid-cols-1 gap-6">
                                {Object.entries(groupedSpots).map(([section, spots]) => (
                                    <div key={section} className="space-y-4">
                                        <h3 className="font-bold text-lg text-muted-foreground">
                                            {section} ({spots.length} spots)
                                        </h3>
                                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                            {spots.map((spot) => {
                                                const parkingSession = activeParkingSessions.get(spot.id);
                                                
                                                return (
                                                    <div 
                                                        key={spot.id}
                                                        className={`p-4 rounded-lg border text-center transition-all ${
                                                            spot.status === 'available' 
                                                                ? 'bg-green-50 border-green-200 text-green-800' 
                                                                : spot.status === 'occupied'
                                                                ? 'bg-red-50 border-red-200 text-red-800'
                                                                : 'bg-yellow-50 border-yellow-200 text-yellow-800'
                                                        }`}
                                                    >
                                                        <div className="font-bold text-lg mb-2">{spot.spot_number}</div>
                                                        <div className="text-sm opacity-75 mb-2">
                                                            {spot.status.charAt(0).toUpperCase() + spot.status.slice(1)}
                                                        </div>
                                                        <div className="text-xs opacity-60 mb-3">
                                                            {spot.hourly_rate} birr/hour
                                                        </div>

                                                        {/* Available - Show Reserve Button */}
                                                        {spot.status === 'available' && (() => {
                                                            // Count user's current active reservations and parking sessions
                                                            const userActiveCount = userReservations.filter(r => 
                                                                (r.status === 'reserved' || r.status === 'active') && 
                                                                String(r.user_id) === String(user?.id)
                                                            ).length;
                                                            const canReserve = userActiveCount < 3;
                                                            
                                                            return (
                                                                <button
                                                                    onClick={() => handleReserveSpot(spot.id)}
                                                                    disabled={!canReserve}
                                                                    className={`w-full px-3 py-2 rounded text-sm font-medium transition-colors ${
                                                                        canReserve 
                                                                            ? 'bg-blue-600 text-white hover:bg-blue-700' 
                                                                            : 'bg-gray-400 text-gray-200 cursor-not-allowed'
                                                                    }`}
                                                                    title={!canReserve ? 'Maximum 3 reservations/parking sessions allowed' : ''}
                                                                >
                                                                    {canReserve ? 'Reserve Now' : 'Limit Reached'}
                                                                </button>
                                                            );
                                                        })()}

                                                        {/* Reserved - Show Park Now and Cancel buttons */}
                                                        {spot.status === 'reserved' && (
                                                            <div className="space-y-2">
                                                                {userReservations.some(r => r.parking_spot_id === spot.id && r.status === 'reserved' && String(r.user_id) === String(user?.id)) ? (
                                                                    <>
                                                                        <div className="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded mb-2">
                                                                            Expires in: {getReservationTimeRemaining(spot.id)} min
                                                                        </div>
                                                                        <button
                                                                            onClick={() => handleStartParking(spot.id)}
                                                                            className="w-full bg-green-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-green-700 transition-colors"
                                                                        >
                                                                            Park Now
                                                                        </button>
                                                                        <button
                                                                            onClick={() => handleCancelReservation(spot.id)}
                                                                            className="w-full bg-gray-500 text-white px-3 py-1 rounded text-xs hover:bg-gray-600 transition-colors"
                                                                        >
                                                                            Cancel Reservation
                                                                        </button>
                                                                    </>
                                                                ) : (
                                                                    <div className="text-xs text-center text-muted-foreground py-2">
                                                                        Reserved by another user
                                                                    </div>
                                                                )}
                                                            </div>
                                                        )}

                                                        {/* Occupied - Show timer and End Parking button */}
                                                        {spot.status === 'occupied' && (
                                                            <div className="space-y-2">
                                                                {userReservations.some(r => r.parking_spot_id === spot.id && r.status === 'active' && String(r.user_id) === String(user?.id)) && parkingSession ? (
                                                                    <>
                                                                        <div className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                                            Time: {formatDuration(parkingSession.startTime)}
                                                                        </div>
                                                                        <div className="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">
                                                                            Cost: {calculateCurrentCost(parkingSession.startTime, parkingSession.hourlyRate)} birr
                                                                        </div>
                                                                        <button
                                                                            onClick={() => handleEndParking(spot.id, showReceipt)}
                                                                            className="w-full bg-red-600 text-white px-3 py-2 rounded text-sm font-medium hover:bg-red-700 transition-colors"
                                                                        >
                                                                            End Parking
                                                                        </button>
                                                                    </>
                                                                ) : (
                                                                    <div className="text-xs text-center text-muted-foreground py-2">
                                                                        Occupied by another user
                                                                    </div>
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                ))}
                                
                                {parkingSpots.length === 0 && (
                                    <div className="text-center py-8 text-muted-foreground">
                                        <i className="fas fa-parking text-4xl mb-4"></i>
                                        <p className="text-lg">No parking spots available</p>
                                        <p className="text-sm">Check back later or contact support</p>
                                    </div>
                                )}
                            </div>
                            
                            {/* Pagination Controls */}
                            {parkingSpots.length > 0 && (
                                <div className="mt-8 flex items-center justify-between">
                                    <div className="text-sm text-muted-foreground">
                                        Showing {startIndex + 1}-{Math.min(endIndex, parkingSpots.length)} of {parkingSpots.length} spots
                                    </div>
                                    
                                    <div className="flex items-center space-x-2">
                                        <button
                                            onClick={handlePrevPage}
                                            disabled={currentPage === 1}
                                            className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                                                currentPage === 1
                                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                    : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                            }`}
                                        >
                                            <i className="fas fa-chevron-left mr-1"></i>
                                            Previous
                                        </button>
                                        
                                        <div className="flex space-x-1">
                                            {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                                                <button
                                                    key={page}
                                                    onClick={() => handlePageChange(page)}
                                                    className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                                                        currentPage === page
                                                            ? 'bg-primary text-white'
                                                            : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                                    }`}
                                                >
                                                    {page}
                                                </button>
                                            ))}
                                        </div>
                                        
                                        <button
                                            onClick={handleNextPage}
                                            disabled={currentPage === totalPages}
                                            className={`px-3 py-2 rounded-lg text-sm font-medium transition-colors ${
                                                currentPage === totalPages
                                                    ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                                    : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                                            }`}
                                        >
                                            Next
                                            <i className="fas fa-chevron-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DashboardPage;
