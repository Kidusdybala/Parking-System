import { useState, useEffect } from 'react';
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
                    totalSpots: spots.length,
                    availableSpots: spots.filter(s => s.status === 'available').length,
                    occupiedSpots: spots.filter(s => s.status === 'occupied').length
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
                
                const activeReservations = allReservations.filter(r => r.status === 'active');
                const completedReservations = allReservations.filter(r => r.status === 'completed');
                const totalRevenue = completedReservations.reduce((sum, r) => sum + parseFloat(r.total_cost), 0);
                
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
                alert('Parking spot created successfully!');
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
            alert(message);
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
                alert('Parking spot updated successfully!');
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
            alert(message);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteSpot = async (spotId) => {
        if (!confirm('Are you sure you want to delete this parking spot?')) {
            return;
        }

        try {
            setLoading(true);
            const response = await axios.delete(`/api/parking-spots/${spotId}`);
            
            if (response.data.success) {
                alert('Parking spot deleted successfully!');
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to delete parking spot';
            alert(message);
        } finally {
            setLoading(false);
        }
    };

    const handleCompleteReservation = async (reservationId) => {
        try {
            setLoading(true);
            const response = await axios.post(`/api/reservations/${reservationId}/complete`);
            
            if (response.data.success) {
                alert('Reservation completed successfully!');
                fetchAdminData();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to complete reservation';
            alert(message);
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
        { key: 'spots', label: 'Parking Spots', icon: 'fas fa-parking' },
        { key: 'reservations', label: 'Reservations', icon: 'fas fa-calendar-check' },
        { key: 'users', label: 'Users', icon: 'fas fa-users' }
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
            <div className="mb-8">
                <h1 className="text-3xl font-bold mb-2">Admin Dashboard</h1>
                <p className="text-muted-foreground">Manage parking system and monitor operations</p>
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
                <div className="glass-card p-6">
                    <h2 className="text-xl font-semibold mb-6">Users</h2>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-white/10">
                                    <th className="text-left py-3">Name</th>
                                    <th className="text-left py-3">Email</th>
                                    <th className="text-left py-3">Balance</th>
                                    <th className="text-left py-3">Role</th>
                                    <th className="text-left py-3">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.map(user => (
                                    <tr key={user.id} className="border-b border-white/5">
                                        <td className="py-3 font-medium">{user.name}</td>
                                        <td className="py-3 text-muted-foreground">{user.email}</td>
                                        <td className="py-3">${user.balance}</td>
                                        <td className="py-3">
                                            <span className={`badge ${user.role === 3 ? 'badge-reserved' : 'badge-available'}`}>
                                                {user.role === 3 ? 'Admin' : 'User'}
                                            </span>
                                        </td>
                                        <td className="py-3 text-muted-foreground">
                                            {new Date(user.created_at).toLocaleDateString()}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
};

export default AdminPage;
