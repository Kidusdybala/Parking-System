import { useState, useEffect } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import axios from 'axios';

const UserDashboard = () => {
    const { user, logout } = useAuth();
    const [parkingSpots, setParkingSpots] = useState([]);
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);
    const [activeTab, setActiveTab] = useState('dashboard');

    useEffect(() => {
        fetchData();
    }, []);

    const fetchData = async () => {
        try {
            const [spotsResponse, reservationsResponse] = await Promise.all([
                axios.get('/api/parking-spots'),
                axios.get('/api/reservations')
            ]);
            
            setParkingSpots(spotsResponse.data.data || []);
            setReservations(reservationsResponse.data.data || []);
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleReservation = async (spotId) => {
        try {
            await axios.post('/api/reservations', {
                parking_spot_id: spotId,
                start_time: new Date().toISOString(),
                duration: 1 // 1 hour default
            });
            
            // Refresh data
            fetchData();
            alert('Reservation created successfully!');
        } catch (error) {
            console.error('Error creating reservation:', error);
            alert('Failed to create reservation. Please try again.');
        }
    };

    const handleLogout = async () => {
        await logout();
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-parkBlue-900">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary"></div>
                    <p className="mt-4 text-white">Loading...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen flex flex-col bg-parkBlue-900">
            {/* Noise Background */}
            <div className="fixed inset-0 noise-bg pointer-events-none"></div>
            
            {/* Header/Navigation */}
            <header className="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
                <div className="container mx-auto px-4 flex items-center justify-between h-16">
                    <div className="flex items-center gap-2">
                        <i className="fas fa-parking text-primary text-2xl"></i>
                        <span className="font-bold text-xl">Miki<span className="text-primary">Park</span></span>
                    </div>
                    
                    {/* Desktop Navigation */}
                    <div className="hidden md:flex items-center space-x-4">
                        <span className="text-muted-foreground">Welcome, <span className="text-foreground">{user?.name}</span></span>
                        <div className="relative group">
                            <button className="h-8 w-8 rounded-full bg-parkBlue-700 flex items-center justify-center hover:bg-primary/80 transition-colors">
                                <i className="fas fa-user"></i>
                            </button>
                            <div className="absolute right-0 mt-2 w-48 py-2 bg-card rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50 bg-parkBlue-800 border border-white/10">
                                <button 
                                    onClick={() => setActiveTab('profile')}
                                    className="block w-full text-left px-4 py-2 hover:bg-accent text-sm"
                                >
                                    <i className="fas fa-user-circle mr-2"></i> Profile
                                </button>
                                <button 
                                    onClick={() => setActiveTab('settings')}
                                    className="block w-full text-left px-4 py-2 hover:bg-accent text-sm"
                                >
                                    <i className="fas fa-cog mr-2"></i> Settings
                                </button>
                                <div className="border-t border-white/10 my-1"></div>
                                <button 
                                    onClick={handleLogout}
                                    className="block w-full text-left px-4 py-2 hover:bg-accent text-sm text-red-400"
                                >
                                    <i className="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <div className="flex-1 container mx-auto px-4 py-8">
                <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    {/* Sidebar */}
                    <div className="lg:col-span-1">
                        <div className="sticky top-24">
                            <div className="glass-card p-4 mb-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                {/* User Info */}
                                <div className="flex items-center space-x-4 mb-6">
                                    <div className="h-14 w-14 rounded-full bg-parkBlue-700 flex items-center justify-center text-xl">
                                        <i className="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <h2 className="font-bold">{user?.name}</h2>
                                        <p className="text-sm text-muted-foreground">{user?.email}</p>
                                    </div>
                                </div>
                                
                                {/* Navigation */}
                                <nav className="space-y-1">
                                    <button 
                                        onClick={() => setActiveTab('dashboard')}
                                        className={`nav-link flex items-center w-full p-2 rounded ${activeTab === 'dashboard' ? 'bg-primary/20 text-primary' : 'hover:bg-white/10'}`}
                                    >
                                        <i className="fas fa-th-large w-5"></i>
                                        <span className="ml-3">Dashboard</span>
                                    </button>
                                    <button 
                                        onClick={() => setActiveTab('reservations')}
                                        className={`nav-link flex items-center w-full p-2 rounded ${activeTab === 'reservations' ? 'bg-primary/20 text-primary' : 'hover:bg-white/10'}`}
                                    >
                                        <i className="fas fa-ticket-alt w-5"></i>
                                        <span className="ml-3">My Reservations</span>
                                    </button>
                                    <button 
                                        onClick={() => setActiveTab('parking')}
                                        className={`nav-link flex items-center w-full p-2 rounded ${activeTab === 'parking' ? 'bg-primary/20 text-primary' : 'hover:bg-white/10'}`}
                                    >
                                        <i className="fas fa-car w-5"></i>
                                        <span className="ml-3">Find Parking</span>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                    
                    {/* Main Content Area */}
                    <div className="lg:col-span-3 space-y-8">
                        {activeTab === 'dashboard' && (
                            <>
                                {/* Quick Stats */}
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div className="glass-card p-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                        <div className="flex items-center">
                                            <div className="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                                                <i className="fas fa-car text-primary"></i>
                                            </div>
                                            <div>
                                                <p className="text-muted-foreground text-sm">Available Slots</p>
                                                <p className="text-2xl font-bold">{Array.isArray(parkingSpots) ? parkingSpots.filter(spot => spot.status === 'available').length : 0}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div className="glass-card p-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                        <div className="flex items-center">
                                            <div className="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                                                <i className="fas fa-ticket-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <p className="text-muted-foreground text-sm">My Reservations</p>
                                                <p className="text-2xl font-bold">{reservations.length}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div className="glass-card p-4 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                        <div className="flex items-center">
                                            <div className="mr-4 h-12 w-12 bg-primary/20 rounded-lg flex items-center justify-center">
                                                <i className="fas fa-wallet text-primary"></i>
                                            </div>
                                            <div>
                                                <p className="text-muted-foreground text-sm">Balance</p>
                                                <p className="text-2xl font-bold">{user?.balance || 0} Birr</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Recent Reservations */}
                                <div className="glass-card p-6 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                    <h2 className="text-xl font-bold mb-4">Recent Reservations</h2>
                                    {reservations.length > 0 ? (
                                        <div className="space-y-4">
                                            {reservations.slice(0, 5).map((reservation) => (
                                                <div key={reservation.id} className="flex items-center justify-between p-4 bg-parkBlue-800/50 rounded-lg">
                                                    <div>
                                                        <h3 className="font-medium">Spot {reservation.parking_spot?.spot_number}</h3>
                                                        <p className="text-sm text-muted-foreground">
                                                            {new Date(reservation.start_time).toLocaleDateString()}
                                                        </p>
                                                    </div>
                                                    <div className="text-right">
                                                        <span className={`px-2 py-1 rounded text-xs ${
                                                            reservation.status === 'active' ? 'bg-green-500/20 text-green-400' :
                                                            reservation.status === 'completed' ? 'bg-blue-500/20 text-blue-400' :
                                                            'bg-red-500/20 text-red-400'
                                                        }`}>
                                                            {reservation.status}
                                                        </span>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-muted-foreground text-center py-8">No reservations yet</p>
                                    )}
                                </div>
                            </>
                        )}

                        {activeTab === 'parking' && (
                            <div className="glass-card p-6 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                <h2 className="text-xl font-bold mb-6">Available Parking Spots</h2>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {Array.isArray(parkingSpots) ? parkingSpots.filter(spot => spot.status === 'available').map((spot) => (
                                        <div key={spot.id} className="p-4 bg-parkBlue-800/50 rounded-lg border border-white/10">
                                            <div className="flex items-center justify-between mb-2">
                                                <h3 className="font-bold">Spot {spot.spot_number}</h3>
                                                <span className="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs">
                                                    Available
                                                </span>
                                            </div>
                                            <p className="text-sm text-muted-foreground mb-3">
                                                Location: {spot.location || 'Main Area'}
                                            </p>
                                            <p className="text-sm mb-3">
                                                Rate: <span className="text-primary font-medium">30 Birr/hour</span>
                                            </p>
                                            <button
                                                onClick={() => handleReservation(spot.id)}
                                                className="w-full py-2 px-4 bg-primary hover:bg-primary/80 text-white rounded-lg transition-colors"
                                            >
                                                Reserve Now
                                            </button>
                                        </div>
                                    ))}
                                </div>
                                {(!Array.isArray(parkingSpots) || parkingSpots.filter(spot => spot.status === 'available').length === 0) && (
                                    <p className="text-muted-foreground text-center py-8">No available parking spots at the moment</p>
                                )}
                            </div>
                        )}

                        {activeTab === 'reservations' && (
                            <div className="glass-card p-6 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                <h2 className="text-xl font-bold mb-6">My Reservations</h2>
                                {reservations.length > 0 ? (
                                    <div className="space-y-4">
                                        {reservations.map((reservation) => (
                                            <div key={reservation.id} className="p-4 bg-parkBlue-800/50 rounded-lg border border-white/10">
                                                <div className="flex items-center justify-between mb-2">
                                                    <h3 className="font-bold">Spot {reservation.parking_spot?.spot_number}</h3>
                                                    <span className={`px-2 py-1 rounded text-xs ${
                                                        reservation.status === 'active' ? 'bg-green-500/20 text-green-400' :
                                                        reservation.status === 'completed' ? 'bg-blue-500/20 text-blue-400' :
                                                        'bg-red-500/20 text-red-400'
                                                    }`}>
                                                        {reservation.status}
                                                    </span>
                                                </div>
                                                <div className="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <p className="text-muted-foreground">Start Time:</p>
                                                        <p>{new Date(reservation.start_time).toLocaleString()}</p>
                                                    </div>
                                                    <div>
                                                        <p className="text-muted-foreground">Duration:</p>
                                                        <p>{reservation.duration} hours</p>
                                                    </div>
                                                    <div>
                                                        <p className="text-muted-foreground">Total Cost:</p>
                                                        <p className="text-primary font-medium">{reservation.total_cost} Birr</p>
                                                    </div>
                                                    <div>
                                                        <p className="text-muted-foreground">Created:</p>
                                                        <p>{new Date(reservation.created_at).toLocaleDateString()}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-muted-foreground text-center py-8">No reservations found</p>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default UserDashboard;
