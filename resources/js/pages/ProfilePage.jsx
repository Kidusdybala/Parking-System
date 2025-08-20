import { useState, useEffect } from 'react';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const ProfilePage = () => {
    const { user, updateUser } = useAuth();
    const [loading, setLoading] = useState(false);
    const [activeTab, setActiveTab] = useState('profile');
    const [profileForm, setProfileForm] = useState({
        name: user?.name || '',
        email: user?.email || ''
    });
    const [passwordForm, setPasswordForm] = useState({
        current_password: '',
        password: '',
        password_confirmation: ''
    });
    const [balanceForm, setBalanceForm] = useState({
        amount: ''
    });
    const [reservationStats, setReservationStats] = useState({
        total: 0,
        completed: 0,
        cancelled: 0,
        totalSpent: 0,
        totalHours: 0
    });

    useEffect(() => {
        fetchUserStats();
    }, []);

    const fetchUserStats = async () => {
        try {
            const response = await axios.get('/api/reservations');
            if (response.data.success) {
                const reservations = response.data.data.data;
                const completed = Array.isArray(reservations) ? reservations.filter(r => r.status === 'completed') : [];
                const cancelled = Array.isArray(reservations) ? reservations.filter(r => r.status === 'cancelled') : [];
                
                const totalSpent = Array.isArray(completed) ? completed.reduce((sum, r) => sum + parseFloat(r.total_cost), 0) : 0;
                const totalHours = Array.isArray(completed) ? completed.reduce((sum, r) => {
                    const start = new Date(r.start_time);
                    const end = new Date(r.end_time);
                    return sum + Math.abs(end - start) / 36e5;
                }, 0) : 0;

                setReservationStats({
                    total: Array.isArray(reservations) ? reservations.length : 0,
                    completed: completed.length,
                    cancelled: cancelled.length,
                    totalSpent: totalSpent,
                    totalHours: Math.round(totalHours * 10) / 10
                });
            }
        } catch (error) {
            console.error('Error fetching user stats:', error);
        }
    };

    const handleProfileUpdate = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            const response = await axios.put(`/api/users/${user.id}`, profileForm);
            
            if (response.data.success) {
                updateUser(response.data.data);
                alert('Profile updated successfully!');
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to update profile';
            alert(message);
        } finally {
            setLoading(false);
        }
    };

    const handlePasswordUpdate = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            const response = await axios.put(`/api/users/${user.id}/password`, passwordForm);
            
            if (response.data.success) {
                alert('Password updated successfully!');
                setPasswordForm({
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                });
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to update password';
            alert(message);
        } finally {
            setLoading(false);
        }
    };

    const handleAddBalance = async (e) => {
        e.preventDefault();
        try {
            setLoading(true);
            const response = await axios.post(`/api/users/${user.id}/add-balance`, {
                amount: parseFloat(balanceForm.amount)
            });
            
            if (response.data.success) {
                // Only update the balance, preserve all other user data
                updateUser({ 
                    ...user, 
                    balance: response.data.data.new_balance 
                });
                alert(`$${balanceForm.amount} added to your balance successfully!`);
                setBalanceForm({ amount: '' });
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to add balance';
            alert(message);
        } finally {
            setLoading(false);
        }
    };

    const tabs = [
        { key: 'profile', label: 'Profile', icon: 'fas fa-user' },
        { key: 'security', label: 'Security', icon: 'fas fa-shield-alt' },
        { key: 'balance', label: 'Balance', icon: 'fas fa-wallet' },
        { key: 'stats', label: 'Statistics', icon: 'fas fa-chart-bar' }
    ];

    return (
        <div className="container py-6">
            <div className="mb-8">
                <h1 className="text-3xl font-bold mb-2">Profile Settings</h1>
                <p className="text-muted-foreground">Manage your account settings and preferences</p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
                {/* Sidebar */}
                <div className="lg:col-span-1">
                    <div className="glass-card p-6">
                        <div className="text-center mb-6">
                            <div className="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i className="fas fa-user text-2xl text-primary"></i>
                            </div>
                            <h3 className="font-semibold">{user?.name}</h3>
                            <p className="text-sm text-muted-foreground">{user?.email}</p>
                            <div className="mt-3 px-3 py-1 bg-primary/20 rounded-full inline-block">
                                <span className="text-sm text-primary font-medium">
                                    Balance: ${user?.balance || 0}
                                </span>
                            </div>
                        </div>

                        <nav className="space-y-2">
                            {tabs.map(tab => (
                                <button
                                    key={tab.key}
                                    onClick={() => setActiveTab(tab.key)}
                                    className={`w-full flex items-center px-3 py-2 rounded-md text-left transition-all ${
                                        activeTab === tab.key
                                            ? 'bg-primary/20 text-primary'
                                            : 'hover:bg-accent'
                                    }`}
                                >
                                    <i className={`${tab.icon} mr-3`}></i>
                                    {tab.label}
                                </button>
                            ))}
                        </nav>
                    </div>
                </div>

                {/* Main Content */}
                <div className="lg:col-span-3">
                    <div className="glass-card p-6">
                        {/* Profile Tab */}
                        {activeTab === 'profile' && (
                            <div>
                                <h2 className="text-xl font-semibold mb-6">Profile Information</h2>
                                <form onSubmit={handleProfileUpdate} className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium mb-2">Full Name</label>
                                        <input
                                            type="text"
                                            className="input"
                                            value={profileForm.name}
                                            onChange={(e) => setProfileForm(prev => ({
                                                ...prev,
                                                name: e.target.value
                                            }))}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium mb-2">Email Address</label>
                                        <input
                                            type="email"
                                            className="input"
                                            value={profileForm.email}
                                            onChange={(e) => setProfileForm(prev => ({
                                                ...prev,
                                                email: e.target.value
                                            }))}
                                            required
                                        />
                                    </div>
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="btn btn-primary"
                                    >
                                        {loading ? (
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                        ) : (
                                            <i className="fas fa-save mr-2"></i>
                                        )}
                                        {loading ? 'Updating...' : 'Update Profile'}
                                    </button>
                                </form>
                            </div>
                        )}

                        {/* Security Tab */}
                        {activeTab === 'security' && (
                            <div>
                                <h2 className="text-xl font-semibold mb-6">Change Password</h2>
                                <form onSubmit={handlePasswordUpdate} className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium mb-2">Current Password</label>
                                        <input
                                            type="password"
                                            className="input"
                                            value={passwordForm.current_password}
                                            onChange={(e) => setPasswordForm(prev => ({
                                                ...prev,
                                                current_password: e.target.value
                                            }))}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium mb-2">New Password</label>
                                        <input
                                            type="password"
                                            className="input"
                                            value={passwordForm.password}
                                            onChange={(e) => setPasswordForm(prev => ({
                                                ...prev,
                                                password: e.target.value
                                            }))}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium mb-2">Confirm New Password</label>
                                        <input
                                            type="password"
                                            className="input"
                                            value={passwordForm.password_confirmation}
                                            onChange={(e) => setPasswordForm(prev => ({
                                                ...prev,
                                                password_confirmation: e.target.value
                                            }))}
                                            required
                                        />
                                    </div>
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="btn btn-primary"
                                    >
                                        {loading ? (
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                        ) : (
                                            <i className="fas fa-key mr-2"></i>
                                        )}
                                        {loading ? 'Updating...' : 'Update Password'}
                                    </button>
                                </form>
                            </div>
                        )}

                        {/* Balance Tab */}
                        {activeTab === 'balance' && (
                            <div>
                                <h2 className="text-xl font-semibold mb-6">Manage Balance</h2>
                                
                                <div className="bg-parkBlue-800/30 p-6 rounded-lg mb-6">
                                    <div className="text-center">
                                        <h3 className="text-2xl font-bold text-primary mb-2">
                                            ${user?.balance || 0}
                                        </h3>
                                        <p className="text-muted-foreground">Current Balance</p>
                                    </div>
                                </div>

                                <form onSubmit={handleAddBalance} className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium mb-2">Add Amount</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="1"
                                            className="input"
                                            placeholder="Enter amount to add"
                                            value={balanceForm.amount}
                                            onChange={(e) => setBalanceForm(prev => ({
                                                ...prev,
                                                amount: e.target.value
                                            }))}
                                            required
                                        />
                                    </div>
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="btn btn-primary"
                                    >
                                        {loading ? (
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                        ) : (
                                            <i className="fas fa-plus mr-2"></i>
                                        )}
                                        {loading ? 'Adding...' : 'Add Balance'}
                                    </button>
                                </form>

                                <div className="mt-8">
                                    <h3 className="font-semibold mb-4">Quick Add Options</h3>
                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        {[10, 25, 50, 100].map(amount => (
                                            <button
                                                key={amount}
                                                onClick={() => setBalanceForm({ amount: amount.toString() })}
                                                className="btn btn-outline"
                                            >
                                                ${amount}
                                            </button>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Statistics Tab */}
                        {activeTab === 'stats' && (
                            <div>
                                <h2 className="text-xl font-semibold mb-6">Usage Statistics</h2>
                                
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div className="bg-parkBlue-800/30 p-6 rounded-lg">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="text-2xl font-bold text-primary">
                                                    {reservationStats.total}
                                                </h3>
                                                <p className="text-muted-foreground">Total Reservations</p>
                                            </div>
                                            <i className="fas fa-calendar text-2xl text-primary"></i>
                                        </div>
                                    </div>

                                    <div className="bg-parkBlue-800/30 p-6 rounded-lg">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="text-2xl font-bold text-green-400">
                                                    {reservationStats.completed}
                                                </h3>
                                                <p className="text-muted-foreground">Completed</p>
                                            </div>
                                            <i className="fas fa-check-circle text-2xl text-green-400"></i>
                                        </div>
                                    </div>

                                    <div className="bg-parkBlue-800/30 p-6 rounded-lg">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="text-2xl font-bold text-primary">
                                                    ${reservationStats.totalSpent.toFixed(2)}
                                                </h3>
                                                <p className="text-muted-foreground">Total Spent</p>
                                            </div>
                                            <i className="fas fa-dollar-sign text-2xl text-primary"></i>
                                        </div>
                                    </div>

                                    <div className="bg-parkBlue-800/30 p-6 rounded-lg">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <h3 className="text-2xl font-bold text-primary">
                                                    {reservationStats.totalHours}h
                                                </h3>
                                                <p className="text-muted-foreground">Total Hours</p>
                                            </div>
                                            <i className="fas fa-clock text-2xl text-primary"></i>
                                        </div>
                                    </div>
                                </div>

                                {reservationStats.cancelled > 0 && (
                                    <div className="mt-6 bg-red-500/10 border border-red-500/30 p-4 rounded-lg">
                                        <div className="flex items-center">
                                            <i className="fas fa-times-circle text-red-400 mr-3"></i>
                                            <div>
                                                <h4 className="font-semibold text-red-400">
                                                    {reservationStats.cancelled} Cancelled Reservations
                                                </h4>
                                                <p className="text-sm text-muted-foreground">
                                                    Consider planning ahead to avoid cancellations
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ProfilePage;
