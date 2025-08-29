import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const AdminPage = () => {
    const { user } = useAuth();
    const [activeTab, setActiveTab] = useState('dashboard');
    const [loading, setLoading] = useState(false);
    const [stats, setStats] = useState({
        totalSpots: 0,
        availableSpots: 0,
        occupiedSpots: 0,
        totalUsers: 0,
        totalReservations: 0,
        activeReservations: 0,
        totalRevenue: 0
    });
    const [parkingSpots, setParkingSpots] = useState([]);
    const [users, setUsers] = useState([]);
    const [reservations, setReservations] = useState([]);
    const [spotForm, setSpotForm] = useState({
        spot_number: '',
        location: '',
        hourly_rate: '',
        status: 'available'
    });
    const [editingSpot, setEditingSpot] = useState(null);
    const [editingUser, setEditingUser] = useState(null);
    const [userForm, setUserForm] = useState({
        name: '',
        email: '',
        balance: '',
        role: 1
    });

    useEffect(() => {
        if (user?.role === 3) { // Admin role
            fetchAdminData();
        }
    }, [user]);

    const fetchAdminData = async () => {
        try {
            setLoading(true);
            
            // Fetch parking spots
            const spotsResponse = await axios.get('/api/parking-spots');
            if (spotsResponse.data.success) {
                const spots = spotsResponse.data.data.data;
                setParkingSpots(spots);
                
                setStats(prev => ({
                    ...prev,
                    totalSpots: Array.isArray(spots) ? spots.length : 0,
                    availableSpots: Array.isArray(spots) ? spots.filter(s => s.status === 'available').length : 0,
                    occupiedSpots: Array.isArray(spots) ? spots.filter(s => s.status === 'occupied').length : 0
                }));
            }

            // Fetch users statistics
            const usersResponse = await axios.get('/api/users/statistics');
            if (usersResponse.data.success) {
                setStats(prev => ({
                    ...prev,
                    totalUsers: usersResponse.data.data.total_users
                }));
            }

            // Fetch all reservations
            const reservationsResponse = await axios.get('/api/reservations/all');
            if (reservationsResponse.data.success) {
                const allReservations = reservationsResponse.data.data.data;
                setReservations(allReservations);
                
                const activeReservations = Array.isArray(allReservations) ? allReservations.filter(r => r.status === 'active') : [];
                const completedReservations = Array.isArray(allReservations) ? allReservations.filter(r => r.status === 'completed') : [];
                const totalRevenue = Array.isArray(completedReservations) ? completedReservations.reduce((sum, r) => sum + parseFloat(r.total_cost), 0) : 0;
                
                setStats(prev => ({
                    ...prev,
                    totalReservations: allReservations.length,
                    activeReservations: activeReservations.length,
                    totalRevenue: totalRevenue
                }));
            }

            // Fetch users
            const allUsersResponse = await axios.get('/api/users');
            if (allUsersResponse.data.success) {
                setUsers(allUsersResponse.data.data.data);
            }

        } catch (error) {
            console.error('Error fetching admin data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleCreateSpot = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            const response = await axios.post('/api/parking-spots', spotForm);
            
            if (response.data.success) {
                console.log('Parking spot created successfully!');
                setSpotForm({
                    spot_number: '',
                    location: '',
                    hourly_rate: '',
                    status: 'available'
                });
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to create parking spot';
            console.error('Create spot error:', message);
        } finally {
            setLoading(false);
        }
    };

    const handleUpdateSpot = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            const response = await axios.put(`/api/parking-spots/${editingSpot.id}`, spotForm);
            
            if (response.data.success) {
                console.log('Parking spot updated successfully!');
                setEditingSpot(null);
                setSpotForm({
                    spot_number: '',
                    location: '',
                    hourly_rate: '',
                    status: 'available'
                });
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to update parking spot';
            console.error('Update spot error:', message);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteSpot = async (spotId) => {
        try {
            setLoading(true);
            const response = await axios.delete(`/api/parking-spots/${spotId}`);
            
            if (response.data.success) {
                console.log('Parking spot deleted successfully!');
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to delete parking spot';
            console.error('Delete spot error:', message);
        } finally {
            setLoading(false);
        }
    };

    const handleCompleteReservation = async (reservationId) => {
        try {
            setLoading(true);
            const response = await axios.post(`/api/reservations/${reservationId}/complete`);
            
            if (response.data.success) {
                console.log('Reservation completed successfully!');
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to complete reservation';
            console.error('Complete reservation error:', message);
        } finally {
            setLoading(false);
        }
    };

    const handleAddBalance = async (userId, amount) => {
        try {
            setLoading(true);
            const response = await axios.post(`/api/users/${userId}/add-balance`, {
                amount: parseFloat(amount)
            });
            
            if (response.data.success) {
                console.log('Balance added successfully!');
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to add balance';
            console.error('Add balance error:', message);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteUser = async (userId) => {
        if (!confirm('Are you sure you want to delete this user?')) return;
        
        try {
            setLoading(true);
            const response = await axios.delete(`/api/users/${userId}`);
            
            if (response.data.success) {
                console.log('User deleted successfully!');
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to delete user';
            console.error('Delete user error:', message);
        } finally {
            setLoading(false);
        }
    };

    const startEditSpot = (spot) => {
        setEditingSpot(spot);
        setSpotForm({
            spot_number: spot.spot_number,
            location: spot.location,
            hourly_rate: spot.hourly_rate,
            status: spot.status
        });
    };

    const cancelEdit = () => {
        setEditingSpot(null);
        setSpotForm({
            spot_number: '',
            location: '',
            hourly_rate: '',
            status: 'available'
        });
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'available': return 'text-green-400 bg-green-500/20 border-green-500/30';
            case 'occupied': return 'text-red-400 bg-red-500/20 border-red-500/30';
            case 'maintenance': return 'text-yellow-400 bg-yellow-500/20 border-yellow-500/30';
            case 'active': return 'text-blue-400 bg-blue-500/20 border-blue-500/30';
            case 'completed': return 'text-green-400 bg-green-500/20 border-green-500/30';
            case 'cancelled': return 'text-red-400 bg-red-500/20 border-red-500/30';
            default: return 'text-gray-400 bg-gray-500/20 border-gray-500/30';
        }
    };

    const tabs = [
        { key: 'dashboard', label: 'Dashboard', icon: 'fas fa-tachometer-alt' },
        { key: 'analytics', label: 'Analytics', icon: 'fas fa-chart-line' },
        { key: 'spots', label: 'Parking Spots', icon: 'fas fa-parking' },
        { key: 'reservations', label: 'Reservations', icon: 'fas fa-calendar-check' },
        { key: 'users', label: 'Users', icon: 'fas fa-users' },
        { key: 'payments', label: 'Payment History', icon: 'fas fa-credit-card' }
    ];

    if (user?.role !== 3) {
        return (
            <div className="container py-6">
                <div className="text-center">
                    <i className="fas fa-lock text-4xl text-red-400 mb-4"></i>
                    <h2 className="text-2xl font-bold mb-2">Access Denied</h2>
                    <p className="text-muted-foreground">You don't have permission to access this page.</p>
                </div>
            </div>
        );
    }

    return (
        <div className="container py-6">
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold mb-2">Admin Dashboard</h1>
                    <p className="text-muted-foreground">Manage parking system and monitor operations</p>
                </div>
                <div className="flex gap-3">
                    <Link 
                        to="/dashboard" 
                        className="btn btn-outline"
                    >
                        <i className="fas fa-tachometer-alt mr-2"></i>
                        User Dashboard
                    </Link>
                    <Link 
                        to="/parking" 
                        className="btn btn-primary"
                    >
                        <i className="fas fa-parking mr-2"></i>
                        View Parking
                    </Link>
                </div>
            </div>

            {/* Tab Navigation */}
            <div className="glass-card p-6 mb-8">
                <div className="flex flex-wrap gap-2">
                    {tabs.map(tab => (
                        <button
                            key={tab.key}
                            onClick={() => setActiveTab(tab.key)}
                            className={`flex items-center px-4 py-2 rounded-md font-medium transition-all ${
                                activeTab === tab.key
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-parkBlue-800/40 hover:bg-parkBlue-800/60'
                            }`}
                        >
                            <i className={`${tab.icon} mr-2`}></i>
                            {tab.label}
                        </button>
                    ))}
                </div>
            </div>

            {/* Dashboard Tab */}
            {activeTab === 'dashboard' && (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-primary">{stats.totalSpots}</h3>
                                <p className="text-muted-foreground">Total Spots</p>
                            </div>
                            <i className="fas fa-parking text-2xl text-primary"></i>
                        </div>
                    </div>

                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-green-400">{stats.availableSpots}</h3>
                                <p className="text-muted-foreground">Available</p>
                            </div>
                            <i className="fas fa-check-circle text-2xl text-green-400"></i>
                        </div>
                    </div>

                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-red-400">{stats.occupiedSpots}</h3>
                                <p className="text-muted-foreground">Occupied</p>
                            </div>
                            <i className="fas fa-car text-2xl text-red-400"></i>
                        </div>
                    </div>

                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-primary">{stats.totalUsers}</h3>
                                <p className="text-muted-foreground">Total Users</p>
                            </div>
                            <i className="fas fa-users text-2xl text-primary"></i>
                        </div>
                    </div>

                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-blue-400">{stats.activeReservations}</h3>
                                <p className="text-muted-foreground">Active Reservations</p>
                            </div>
                            <i className="fas fa-calendar-check text-2xl text-blue-400"></i>
                        </div>
                    </div>

                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-primary">{stats.totalReservations}</h3>
                                <p className="text-muted-foreground">Total Reservations</p>
                            </div>
                            <i className="fas fa-calendar text-2xl text-primary"></i>
                        </div>
                    </div>

                    <div className="glass-card p-6 md:col-span-2">
                        <div className="flex items-center justify-between">
                            <div>
                                <h3 className="text-2xl font-bold text-green-400">${stats.totalRevenue.toFixed(2)}</h3>
                                <p className="text-muted-foreground">Total Revenue</p>
                            </div>
                            <i className="fas fa-dollar-sign text-2xl text-green-400"></i>
                        </div>
                    </div>
                </div>
            )}

            {/* Analytics Tab */}
            {activeTab === 'analytics' && (
                <div className="space-y-8">
                    {/* Revenue Analytics */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Revenue Analytics</h2>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="text-center">
                                <h3 className="text-3xl font-bold text-green-400">{stats.totalRevenue.toFixed(2)} Birr</h3>
                                <p className="text-muted-foreground">Total Revenue</p>
                            </div>
                            <div className="text-center">
                                <h3 className="text-3xl font-bold text-blue-400">{(stats.totalRevenue / Math.max(stats.totalReservations, 1)).toFixed(2)} Birr</h3>
                                <p className="text-muted-foreground">Average per Reservation</p>
                            </div>
                            <div className="text-center">
                                <h3 className="text-3xl font-bold text-purple-400">{((stats.occupiedSpots / Math.max(stats.totalSpots, 1)) * 100).toFixed(1)}%</h3>
                                <p className="text-muted-foreground">Occupancy Rate</p>
                            </div>
                        </div>
                    </div>

                    {/* Usage Statistics */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Usage Statistics</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div className="bg-parkBlue-800/40 p-4 rounded-lg">
                                <h4 className="font-semibold text-green-400">Active Sessions</h4>
                                <p className="text-2xl font-bold">{stats.activeReservations}</p>
                            </div>
                            <div className="bg-parkBlue-800/40 p-4 rounded-lg">
                                <h4 className="font-semibold text-blue-400">Completed Today</h4>
                                <p className="text-2xl font-bold">{reservations.filter(r => r.status === 'completed' && new Date(r.updated_at).toDateString() === new Date().toDateString()).length}</p>
                            </div>
                            <div className="bg-parkBlue-800/40 p-4 rounded-lg">
                                <h4 className="font-semibold text-yellow-400">Peak Hours</h4>
                                <p className="text-2xl font-bold">9-11 AM</p>
                            </div>
                            <div className="bg-parkBlue-800/40 p-4 rounded-lg">
                                <h4 className="font-semibold text-purple-400">Most Popular Spot</h4>
                                <p className="text-2xl font-bold">A-001</p>
                            </div>
                        </div>
                    </div>

                    {/* Recent Activity */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Recent Activity</h2>
                        <div className="space-y-3">
                            {reservations.slice(0, 10).map(reservation => (
                                <div key={reservation.id} className="flex items-center justify-between p-3 bg-parkBlue-800/40 rounded-lg">
                                    <div className="flex items-center space-x-3">
                                        <i className={`fas ${reservation.status === 'active' ? 'fa-play text-blue-400' : reservation.status === 'completed' ? 'fa-check text-green-400' : 'fa-clock text-yellow-400'}`}></i>
                                        <div>
                                            <p className="font-medium">{reservation.user?.name || 'Unknown User'}</p>
                                            <p className="text-sm text-muted-foreground">Spot {reservation.parking_spot?.spot_number}</p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-medium">{reservation.total_cost} Birr</p>
                                        <p className="text-sm text-muted-foreground">{new Date(reservation.updated_at).toLocaleDateString()}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}

            {/* Parking Spots Tab */}
            {activeTab === 'spots' && (
                <div className="space-y-8">
                    {/* Add/Edit Spot Form */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">
                            {editingSpot ? 'Edit Parking Spot' : 'Add New Parking Spot'}
                        </h2>
                        <form onSubmit={editingSpot ? handleUpdateSpot : handleCreateSpot} className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label className="block text-sm font-medium mb-2">Spot Number</label>
                                <input
                                    type="text"
                                    className="input"
                                    value={spotForm.spot_number}
                                    onChange={(e) => setSpotForm(prev => ({
                                        ...prev,
                                        spot_number: e.target.value
                                    }))}
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-2">Location</label>
                                <input
                                    type="text"
                                    className="input"
                                    value={spotForm.location}
                                    onChange={(e) => setSpotForm(prev => ({
                                        ...prev,
                                        location: e.target.value
                                    }))}
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-2">Hourly Rate ($)</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    className="input"
                                    value={spotForm.hourly_rate}
                                    onChange={(e) => setSpotForm(prev => ({
                                        ...prev,
                                        hourly_rate: e.target.value
                                    }))}
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium mb-2">Status</label>
                                <select
                                    className="input"
                                    value={spotForm.status}
                                    onChange={(e) => setSpotForm(prev => ({
                                        ...prev,
                                        status: e.target.value
                                    }))}
                                >
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                    <option value="maintenance">Maintenance</option>
                                </select>
                            </div>
                            <div className="flex gap-2">
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="btn btn-primary"
                                >
                                    {loading ? (
                                        <i className="fas fa-spinner fa-spin mr-2"></i>
                                    ) : (
                                        <i className={`fas ${editingSpot ? 'fa-save' : 'fa-plus'} mr-2`}></i>
                                    )}
                                    {loading ? 'Processing...' : (editingSpot ? 'Update' : 'Add Spot')}
                                </button>
                                {editingSpot && (
                                    <button
                                        type="button"
                                        onClick={cancelEdit}
                                        className="btn btn-outline"
                                    >
                                        Cancel
                                    </button>
                                )}
                            </div>
                        </form>
                    </div>

                    {/* Spots List */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Parking Spots</h2>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-white/10">
                                        <th className="text-left py-3">Spot Number</th>
                                        <th className="text-left py-3">Location</th>
                                        <th className="text-left py-3">Rate</th>
                                        <th className="text-left py-3">Status</th>
                                        <th className="text-left py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {parkingSpots.map(spot => (
                                        <tr key={spot.id} className="border-b border-white/5">
                                            <td className="py-3 font-medium">{spot.spot_number}</td>
                                            <td className="py-3 text-muted-foreground">{spot.location}</td>
                                            <td className="py-3">${spot.hourly_rate}/hr</td>
                                            <td className="py-3">
                                                <span className={`badge ${getStatusColor(spot.status)}`}>
                                                    {spot.status}
                                                </span>
                                            </td>
                                            <td className="py-3">
                                                <div className="flex gap-2">
                                                    <button
                                                        onClick={() => startEditSpot(spot)}
                                                        className="text-blue-400 hover:text-blue-300"
                                                    >
                                                        <i className="fas fa-edit"></i>
                                                    </button>
                                                    <button
                                                        onClick={() => handleDeleteSpot(spot.id)}
                                                        className="text-red-400 hover:text-red-300"
                                                    >
                                                        <i className="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}

            {/* Reservations Tab */}
            {activeTab === 'reservations' && (
                <div className="glass-card p-6">
                    <h2 className="text-xl font-semibold mb-6">All Reservations</h2>
                    <div className="space-y-4">
                        {reservations.map(reservation => (
                            <div key={reservation.id} className="bg-parkBlue-800/30 p-4 rounded-lg">
                                <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div className="flex-1">
                                        <div className="flex items-center gap-3 mb-2">
                                            <h3 className="font-semibold">
                                                {reservation.parking_spot.spot_number}
                                            </h3>
                                            <span className={`badge ${getStatusColor(reservation.status)}`}>
                                                {reservation.status}
                                            </span>
                                        </div>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-muted-foreground">
                                            <div>User: {reservation.user.name}</div>
                                            <div>Cost: ${reservation.total_cost}</div>
                                            <div>Start: {new Date(reservation.start_time).toLocaleString()}</div>
                                            <div>End: {new Date(reservation.end_time).toLocaleString()}</div>
                                        </div>
                                    </div>
                                    {reservation.status === 'active' && (
                                        <button
                                            onClick={() => handleCompleteReservation(reservation.id)}
                                            className="btn btn-primary"
                                        >
                                            <i className="fas fa-check mr-2"></i>
                                            Complete
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Users Tab */}
            {activeTab === 'users' && (
                <div className="space-y-8">
                    {/* User Statistics */}
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div className="glass-card p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h3 className="text-2xl font-bold text-primary">{stats.totalUsers}</h3>
                                    <p className="text-muted-foreground">Total Users</p>
                                </div>
                                <i className="fas fa-users text-2xl text-primary"></i>
                            </div>
                        </div>
                        <div className="glass-card p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h3 className="text-2xl font-bold text-green-400">{users.filter(u => u.role !== 3).length}</h3>
                                    <p className="text-muted-foreground">Regular Users</p>
                                </div>
                                <i className="fas fa-user text-2xl text-green-400"></i>
                            </div>
                        </div>
                        <div className="glass-card p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h3 className="text-2xl font-bold text-blue-400">{users.filter(u => u.role === 3).length}</h3>
                                    <p className="text-muted-foreground">Admins</p>
                                </div>
                                <i className="fas fa-shield-alt text-2xl text-blue-400"></i>
                            </div>
                        </div>
                        <div className="glass-card p-6">
                            <div className="flex items-center justify-between">
                                <div>
                                    <h3 className="text-2xl font-bold text-yellow-400">{users.reduce((sum, u) => sum + parseFloat(u.balance || 0), 0).toFixed(2)} Birr</h3>
                                    <p className="text-muted-foreground">Total Balance</p>
                                </div>
                                <i className="fas fa-wallet text-2xl text-yellow-400"></i>
                            </div>
                        </div>
                    </div>

                    {/* Users Management */}
                    <div className="glass-card p-6">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-xl font-semibold">User Management</h2>
                            <button className="btn btn-primary">
                                <i className="fas fa-user-plus mr-2"></i>
                                Add User
                            </button>
                        </div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-white/10">
                                        <th className="text-left py-3">User</th>
                                        <th className="text-left py-3">Balance</th>
                                        <th className="text-left py-3">Role</th>
                                        <th className="text-left py-3">Reservations</th>
                                        <th className="text-left py-3">Joined</th>
                                        <th className="text-left py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {users.map(userData => (
                                        <tr key={userData.id} className="border-b border-white/5">
                                            <td className="py-3">
                                                <div className="flex items-center space-x-3">
                                                    <div className="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                                                        <i className="fas fa-user text-primary text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <p className="font-medium">{userData.name}</p>
                                                        <p className="text-sm text-muted-foreground">{userData.email}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="py-3">
                                                <span className="font-bold text-green-400">{userData.balance} Birr</span>
                                            </td>
                                            <td className="py-3">
                                                <span className={`badge ${userData.role === 3 ? 'text-blue-400 bg-blue-500/20 border-blue-500/30' : 'text-green-400 bg-green-500/20 border-green-500/30'}`}>
                                                    {userData.role === 3 ? 'Admin' : 'User'}
                                                </span>
                                            </td>
                                            <td className="py-3">
                                                <span className="text-muted-foreground">
                                                    {reservations.filter(r => r.user_id === userData.id).length} total
                                                </span>
                                            </td>
                                            <td className="py-3 text-muted-foreground">
                                                {new Date(userData.created_at).toLocaleDateString()}
                                            </td>
                                            <td className="py-3">
                                                <div className="flex items-center space-x-2">
                                                    <button
                                                        onClick={() => {
                                                            const amount = prompt('Enter amount to add to balance:');
                                                            if (amount && !isNaN(amount)) {
                                                                handleAddBalance(userData.id, amount);
                                                            }
                                                        }}
                                                        className="btn btn-sm bg-green-500/20 hover:bg-green-500/30 text-green-400 border-green-500/30"
                                                        title="Add Balance"
                                                    >
                                                        <i className="fas fa-plus"></i>
                                                    </button>
                                                    <button
                                                        onClick={() => handleDeleteUser(userData.id)}
                                                        className="btn btn-sm bg-red-500/20 hover:bg-red-500/30 text-red-400 border-red-500/30"
                                                        title="Delete User"
                                                        disabled={userData.role === 3}
                                                    >
                                                        <i className="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        
                        {users.length === 0 && (
                            <div className="text-center py-8">
                                <i className="fas fa-users text-4xl text-muted-foreground mb-4"></i>
                                <p className="text-muted-foreground">No users found</p>
                            </div>
                        )}
                    </div>
                </div>
            )}

            {/* Payment History Tab */}
            {activeTab === 'payments' && (
                <div className="space-y-8">
                    {/* Payment Summary */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Payment Summary</h2>
                        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div className="text-center">
                                <h3 className="text-2xl font-bold text-green-400">{stats.totalRevenue.toFixed(2)} Birr</h3>
                                <p className="text-muted-foreground">Total Revenue</p>
                            </div>
                            <div className="text-center">
                                <h3 className="text-2xl font-bold text-blue-400">{reservations.filter(r => r.status === 'completed').length}</h3>
                                <p className="text-muted-foreground">Completed Payments</p>
                            </div>
                            <div className="text-center">
                                <h3 className="text-2xl font-bold text-yellow-400">{reservations.filter(r => r.status === 'active').length}</h3>
                                <p className="text-muted-foreground">Pending Payments</p>
                            </div>
                            <div className="text-center">
                                <h3 className="text-2xl font-bold text-purple-400">{(stats.totalRevenue / Math.max(reservations.filter(r => r.status === 'completed').length, 1)).toFixed(2)} Birr</h3>
                                <p className="text-muted-foreground">Average Payment</p>
                            </div>
                        </div>
                    </div>

                    {/* Payment History Table */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Payment History</h2>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-white/10">
                                        <th className="text-left py-3">Transaction ID</th>
                                        <th className="text-left py-3">User</th>
                                        <th className="text-left py-3">Spot</th>
                                        <th className="text-left py-3">Amount</th>
                                        <th className="text-left py-3">Duration</th>
                                        <th className="text-left py-3">Date</th>
                                        <th className="text-left py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {reservations.filter(r => r.status === 'completed').map(payment => (
                                        <tr key={payment.id} className="border-b border-white/5">
                                            <td className="py-3 font-mono text-sm">#{payment.id.toString().padStart(6, '0')}</td>
                                            <td className="py-3">
                                                <div>
                                                    <p className="font-medium">{payment.user?.name || 'Unknown'}</p>
                                                    <p className="text-sm text-muted-foreground">{payment.user?.email}</p>
                                                </div>
                                            </td>
                                            <td className="py-3 font-medium">{payment.parking_spot?.spot_number}</td>
                                            <td className="py-3">
                                                <span className="font-bold text-green-400">{payment.total_cost} Birr</span>
                                            </td>
                                            <td className="py-3">
                                                {payment.actual_start_time && payment.actual_end_time ? 
                                                    `${Math.ceil((new Date(payment.actual_end_time) - new Date(payment.actual_start_time)) / (1000 * 60 * 60))}h` : 
                                                    'N/A'
                                                }
                                            </td>
                                            <td className="py-3 text-muted-foreground">
                                                {new Date(payment.updated_at).toLocaleDateString()}
                                                <br />
                                                <span className="text-xs">{new Date(payment.updated_at).toLocaleTimeString()}</span>
                                            </td>
                                            <td className="py-3">
                                                <span className="badge text-green-400 bg-green-500/20 border-green-500/30">
                                                    <i className="fas fa-check-circle mr-1"></i>
                                                    Paid
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        
                        {reservations.filter(r => r.status === 'completed').length === 0 && (
                            <div className="text-center py-8">
                                <i className="fas fa-credit-card text-4xl text-muted-foreground mb-4"></i>
                                <p className="text-muted-foreground">No payment history available</p>
                            </div>
                        )}
                    </div>

                    {/* Recent Transactions */}
                    <div className="glass-card p-6">
                        <h2 className="text-xl font-semibold mb-6">Recent Transactions</h2>
                        <div className="space-y-3">
                            {reservations.filter(r => r.status === 'completed').slice(0, 5).map(transaction => (
                                <div key={transaction.id} className="flex items-center justify-between p-4 bg-parkBlue-800/40 rounded-lg">
                                    <div className="flex items-center space-x-4">
                                        <div className="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                                            <i className="fas fa-check text-green-400"></i>
                                        </div>
                                        <div>
                                            <p className="font-medium">{transaction.user?.name}</p>
                                            <p className="text-sm text-muted-foreground">Spot {transaction.parking_spot?.spot_number}</p>
                                        </div>
                                    </div>
                                    <div className="text-right">
                                        <p className="font-bold text-green-400">+{transaction.total_cost} Birr</p>
                                        <p className="text-sm text-muted-foreground">{new Date(transaction.updated_at).toLocaleDateString()}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default AdminPage;
