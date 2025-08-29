import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';
import LoadingSpinner from '../components/Common/LoadingSpinner';
import { chapaService } from '../services/chapaService';

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
        amount: '',
        phone_number: ''
    });
    const [reservationStats, setReservationStats] = useState({
        total: 0,
        completed: 0,
        cancelled: 0,
        totalSpent: 0,
        totalHours: 0
    });
    const [paymentLoading, setPaymentLoading] = useState(false);
    const [paymentError, setPaymentError] = useState('');
    const [bonusInfo, setBonusInfo] = useState({ hasBonus: false });

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
        setPaymentError('');
        
        // Validate amount
        const validation = chapaService.validateAmount(balanceForm.amount);
        if (!validation.valid) {
            setPaymentError(validation.message);
            return;
        }

        try {
            setPaymentLoading(true);
            
            const paymentData = {
                amount: parseFloat(balanceForm.amount),
                phone_number: balanceForm.phone_number || undefined,
                description: `Wallet Top-up - ${balanceForm.amount} ETB`
            };

            const result = await chapaService.initializeWalletTopup(paymentData);
            
            if (result.success) {
                // Store transaction reference for verification later
                sessionStorage.setItem('pendingTopup', JSON.stringify({
                    txRef: result.txRef,
                    amount: balanceForm.amount,
                    userId: user.id
                }));
                
                // Show success message
             
                // Redirect to Chapa checkout
                chapaService.redirectToCheckout(result.checkoutUrl);
            } else {
                setPaymentError(result.message || 'Payment initialization failed');
                if (result.errors && result.errors.length > 0) {
                    console.error('Payment errors:', result.errors);
                }
            }
        } catch (error) {
            setPaymentError(error.message || 'An error occurred while processing payment');
            console.error('Payment error:', error);
        } finally {
            setPaymentLoading(false);
        }
    };

    // Handle amount change and check for bonuses
    const handleAmountChange = (amount) => {
        setBalanceForm(prev => ({ ...prev, amount }));
        const bonus = chapaService.checkForBonuses(amount);
        setBonusInfo(bonus);
        setPaymentError('');
    };

    const tabs = [
        { key: 'profile', label: 'Profile', icon: 'fas fa-user' },
        { key: 'security', label: 'Security', icon: 'fas fa-shield-alt' },
        { key: 'balance', label: 'Balance', icon: 'fas fa-wallet' },
        { key: 'stats', label: 'Statistics', icon: 'fas fa-chart-bar' }
    ];

    return (
        <div className="container py-6">
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold mb-2">Profile Settings</h1>
                    <p className="text-muted-foreground">Manage your account settings and preferences</p>
                </div>
                <Link 
                    to="/dashboard" 
                    className="btn btn-primary"
                >
                    <i className="fas fa-tachometer-alt mr-2"></i>
                    Go to Dashboard
                </Link>
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

                        {/* Balance Tab - Secure Chapa Integration */}
                        {activeTab === 'balance' && (
                            <div>
                                <h2 className="text-xl font-semibold mb-6">
                                    <i className="fas fa-wallet mr-2"></i>
                                    Wallet Top-up via Chapa
                                </h2>
                                
                                <div className="bg-parkBlue-800/30 p-6 rounded-lg mb-6">
                                    <div className="text-center">
                                        <h3 className="text-2xl font-bold text-primary mb-2">
                                            {user?.balance || 0} ETB
                                        </h3>
                                        <p className="text-muted-foreground">Current Balance</p>
                                    </div>
                                </div>

                                {/* Security Notice */}
                                <div className="bg-green-500/10 border border-green-500/30 p-4 rounded-lg mb-6">
                                    <div className="flex items-start">
                                        <i className="fas fa-shield-alt text-green-400 mr-3 mt-1"></i>
                                        <div>
                                            <h4 className="font-semibold text-green-400 mb-1">Secure Payment via Chapa</h4>
                                            <p className="text-sm text-muted-foreground">
                                                Pay securely using mobile money, cards, or bank transfer. 
                                                No more manual proof uploads required!
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Payment Error */}
                                {paymentError && (
                                    <div className="bg-red-500/10 border border-red-500/30 p-4 rounded-lg mb-6">
                                        <div className="flex items-center">
                                            <i className="fas fa-exclamation-triangle text-red-400 mr-3"></i>
                                            <p className="text-red-400">{paymentError}</p>
                                        </div>
                                    </div>
                                )}

                                <form onSubmit={handleAddBalance} className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium mb-2">
                                            Amount (ETB)
                                            <span className="text-red-400 ml-1">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            step="1"
                                            min="10"
                                            max="50000"
                                            className="input"
                                            placeholder="Enter amount (minimum 10 ETB)"
                                            value={balanceForm.amount}
                                            onChange={(e) => handleAmountChange(e.target.value)}
                                            required
                                        />
                                        <p className="text-xs text-muted-foreground mt-1">
                                            Minimum: 10 ETB | Maximum: 50,000 ETB
                                        </p>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium mb-2">
                                            Phone Number (Optional)
                                        </label>
                                        <input
                                            type="tel"
                                            className="input"
                                            placeholder="+251911123456 (for mobile money)"
                                            value={balanceForm.phone_number}
                                            onChange={(e) => setBalanceForm(prev => ({
                                                ...prev,
                                                phone_number: e.target.value
                                            }))}
                                        />
                                        <p className="text-xs text-muted-foreground mt-1">
                                            Required for mobile money payments
                                        </p>
                                    </div>

                                    {/* Bonus Information */}
                                    {bonusInfo.hasBonus && (
                                        <div className="bg-yellow-500/10 border border-yellow-500/30 p-4 rounded-lg">
                                            <div className="flex items-center">
                                                <i className="fas fa-gift text-yellow-400 mr-3"></i>
                                                <div>
                                                    <h4 className="font-semibold text-yellow-400">
                                                        Bonus: +{bonusInfo.bonusAmount.toFixed(2)} ETB
                                                    </h4>
                                                    <p className="text-sm text-muted-foreground">
                                                        {bonusInfo.message}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    <button
                                        type="submit"
                                        disabled={paymentLoading || !balanceForm.amount}
                                        className="btn btn-primary w-full"
                                    >
                                        {paymentLoading ? (
                                            <i className="fas fa-spinner fa-spin mr-2"></i>
                                        ) : (
                                            <i className="fas fa-credit-card mr-2"></i>
                                        )}
                                        {paymentLoading ? 'Processing...' : `Pay ${balanceForm.amount || '0'} ETB via Chapa`}
                                    </button>
                                </form>

                                {/* Quick Amount Options */}
                                <div className="mt-8">
                                    <h3 className="font-semibold mb-4">
                                        <i className="fas fa-bolt mr-2"></i>
                                        Quick Amount Options
                                    </h3>
                                    <div className="grid grid-cols-2 md:grid-cols-5 gap-3">
                                        {chapaService.getRecommendedAmounts().map(amount => (
                                            <button
                                                key={amount}
                                                onClick={() => handleAmountChange(amount.toString())}
                                                className="btn btn-outline text-sm"
                                                type="button"
                                            >
                                                {amount} ETB
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                {/* Payment Methods */}
                                <div className="mt-8">
                                    <h3 className="font-semibold mb-4">
                                        <i className="fas fa-payment mr-2"></i>
                                        Available Payment Methods
                                    </h3>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div className="bg-blue-500/10 border border-blue-500/30 p-4 rounded-lg">
                                            <div className="text-center">
                                                <i className="fas fa-mobile-alt text-2xl text-blue-400 mb-2"></i>
                                                <h4 className="font-semibold text-blue-400">Mobile Money</h4>
                                                <p className="text-sm text-muted-foreground">
                                                    Telebirr, M-Birr, CBE Birr
                                                </p>
                                            </div>
                                        </div>
                                        <div className="bg-green-500/10 border border-green-500/30 p-4 rounded-lg">
                                            <div className="text-center">
                                                <i className="fas fa-credit-card text-2xl text-green-400 mb-2"></i>
                                                <h4 className="font-semibold text-green-400">Cards</h4>
                                                <p className="text-sm text-muted-foreground">
                                                    Visa, Mastercard
                                                </p>
                                            </div>
                                        </div>
                                        <div className="bg-purple-500/10 border border-purple-500/30 p-4 rounded-lg">
                                            <div className="text-center">
                                                <i className="fas fa-university text-2xl text-purple-400 mb-2"></i>
                                                <h4 className="font-semibold text-purple-400">Bank Transfer</h4>
                                                <p className="text-sm text-muted-foreground">
                                                    All major Ethiopian banks
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* How it Works */}
                                <div className="mt-8">
                                    <h3 className="font-semibold mb-4">
                                        <i className="fas fa-info-circle mr-2"></i>
                                        How it Works
                                    </h3>
                                    <div className="bg-gray-500/10 p-4 rounded-lg">
                                        <ol className="list-decimal list-inside space-y-2 text-sm text-muted-foreground">
                                            <li>Enter the amount you want to add to your wallet</li>
                                            <li>Click "Pay via Chapa" to initialize secure payment</li>
                                            <li>You'll be redirected to Chapa's secure checkout</li>
                                            <li>Choose your preferred payment method and pay</li>
                                            <li>Your balance will be updated automatically after payment</li>
                                            <li>Return to your account with updated balance</li>
                                        </ol>
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
