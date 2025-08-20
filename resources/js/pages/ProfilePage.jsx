import React, { useState } from 'react';
import { useAuth } from '../contexts/AuthContext';
import { useApi } from '../hooks';
import { Card, Button, Input, Alert } from '../components/ui';
import { formatCurrency } from '../utils';

const ProfilePage = () => {
    const { user, updateUser } = useAuth();
    const { put, post, loading } = useApi();
    const [activeTab, setActiveTab] = useState('profile');
    const [message, setMessage] = useState({ type: '', content: '' });

    const [profileData, setProfileData] = useState({
        name: user?.name || '',
        email: user?.email || ''
    });

    const [passwordData, setPasswordData] = useState({
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
    });

    const [balanceAmount, setBalanceAmount] = useState('');

    const tabs = [
        { id: 'profile', name: 'Profile Information', icon: 'fas fa-user' },
        { id: 'password', name: 'Change Password', icon: 'fas fa-lock' },
        { id: 'balance', name: 'Manage Balance', icon: 'fas fa-wallet' },
        { id: 'preferences', name: 'Preferences', icon: 'fas fa-cog' }
    ];

    const showMessage = (type, content) => {
        setMessage({ type, content });
        setTimeout(() => setMessage({ type: '', content: '' }), 5000);
    };

    const handleProfileUpdate = async (e) => {
        e.preventDefault();
        const result = await put(`/api/users/${user.id}`, profileData);
        
        if (result.success) {
            updateUser({ ...user, ...profileData });
            showMessage('success', 'Profile updated successfully!');
        } else {
            showMessage('error', result.error || 'Failed to update profile');
        }
    };

    const handlePasswordChange = async (e) => {
        e.preventDefault();
        const result = await post('/api/auth/change-password', passwordData);
        
        if (result.success) {
            setPasswordData({
                current_password: '',
                new_password: '',
                new_password_confirmation: ''
            });
            showMessage('success', 'Password changed successfully!');
        } else {
            showMessage('error', result.error || 'Failed to change password');
        }
    };

    const handleAddBalance = async (e) => {
        e.preventDefault();
        const result = await post(`/api/users/${user.id}/add-balance`, {
            amount: parseFloat(balanceAmount)
        });
        
        if (result.success) {
            updateUser({ ...user, balance: result.data.user.balance });
            setBalanceAmount('');
            showMessage('success', `Successfully added ${formatCurrency(balanceAmount)} to your balance!`);
        } else {
            showMessage('error', result.error || 'Failed to add balance');
        }
    };

    const handleInputChange = (setter) => (e) => {
        const { name, value } = e.target;
        setter(prev => ({ ...prev, [name]: value }));
    };

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Profile Settings</h1>
                <p className="mt-1 text-sm text-gray-600">
                    Manage your account settings and preferences
                </p>
            </div>

            {/* Message Alert */}
            {message.content && (
                <Alert 
                    type={message.type} 
                    onClose={() => setMessage({ type: '', content: '' })}
                >
                    {message.content}
                </Alert>
            )}

            {/* Profile Summary Card */}
            <Card>
                <Card.Content>
                    <div className="flex items-center space-x-4">
                        <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <i className="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                        <div className="flex-1">
                            <h3 className="text-lg font-medium text-gray-900">{user?.name}</h3>
                            <p className="text-sm text-gray-500">{user?.email}</p>
                            <div className="flex items-center mt-2">
                                <span className="text-sm text-gray-500 mr-2">Balance:</span>
                                <span className="text-lg font-semibold text-green-600">
                                    {formatCurrency(user?.balance || 0)}
                                </span>
                            </div>
                        </div>
                        <div className="text-right">
                            <div className="text-sm text-gray-500">Member since</div>
                            <div className="text-sm font-medium">
                                {user?.created_at ? new Date(user.created_at).toLocaleDateString() : 'N/A'}
                            </div>
                        </div>
                    </div>
                </Card.Content>
            </Card>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Tab Navigation */}
                <div className="lg:col-span-1">
                    <Card>
                        <Card.Content>
                            <nav className="space-y-1">
                                {tabs.map((tab) => (
                                    <button
                                        key={tab.id}
                                        onClick={() => setActiveTab(tab.id)}
                                        className={`w-full flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors ${
                                            activeTab === tab.id
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                        }`}
                                    >
                                        <i className={`${tab.icon} mr-3`}></i>
                                        {tab.name}
                                    </button>
                                ))}
                            </nav>
                        </Card.Content>
                    </Card>
                </div>

                {/* Tab Content */}
                <div className="lg:col-span-3">
                    <Card>
                        <Card.Header>
                            <Card.Title>
                                {tabs.find(tab => tab.id === activeTab)?.name}
                            </Card.Title>
                        </Card.Header>
                        <Card.Content>
                            {activeTab === 'profile' && (
                                <form onSubmit={handleProfileUpdate} className="space-y-6">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <Input
                                            label="Full Name"
                                            name="name"
                                            value={profileData.name}
                                            onChange={handleInputChange(setProfileData)}
                                            icon="fas fa-user"
                                            required
                                        />
                                        <Input
                                            label="Email Address"
                                            name="email"
                                            type="email"
                                            value={profileData.email}
                                            onChange={handleInputChange(setProfileData)}
                                            icon="fas fa-envelope"
                                            required
                                        />
                                    </div>
                                    <div className="flex justify-end">
                                        <Button
                                            type="submit"
                                            loading={loading}
                                            icon="fas fa-save"
                                        >
                                            Update Profile
                                        </Button>
                                    </div>
                                </form>
                            )}

                            {activeTab === 'password' && (
                                <form onSubmit={handlePasswordChange} className="space-y-6">
                                    <Input
                                        label="Current Password"
                                        name="current_password"
                                        type="password"
                                        value={passwordData.current_password}
                                        onChange={handleInputChange(setPasswordData)}
                                        icon="fas fa-lock"
                                        required
                                    />
                                    <Input
                                        label="New Password"
                                        name="new_password"
                                        type="password"
                                        value={passwordData.new_password}
                                        onChange={handleInputChange(setPasswordData)}
                                        icon="fas fa-key"
                                        helperText="Password must be at least 8 characters long"
                                        required
                                    />
                                    <Input
                                        label="Confirm New Password"
                                        name="new_password_confirmation"
                                        type="password"
                                        value={passwordData.new_password_confirmation}
                                        onChange={handleInputChange(setPasswordData)}
                                        icon="fas fa-key"
                                        required
                                    />
                                    <div className="flex justify-end">
                                        <Button
                                            type="submit"
                                            loading={loading}
                                            icon="fas fa-shield-alt"
                                        >
                                            Change Password
                                        </Button>
                                    </div>
                                </form>
                            )}

                            {activeTab === 'balance' && (
                                <div className="space-y-6">
                                    <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div className="flex items-center">
                                            <i className="fas fa-wallet text-green-600 text-2xl mr-3"></i>
                                            <div>
                                                <h4 className="text-lg font-medium text-green-900">
                                                    Current Balance
                                                </h4>
                                                <p className="text-2xl font-bold text-green-600">
                                                    {formatCurrency(user?.balance || 0)}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <form onSubmit={handleAddBalance} className="space-y-4">
                                        <Input
                                            label="Amount to Add"
                                            type="number"
                                            step="0.01"
                                            min="1"
                                            value={balanceAmount}
                                            onChange={(e) => setBalanceAmount(e.target.value)}
                                            icon="fas fa-dollar-sign"
                                            placeholder="Enter amount"
                                            helperText="Minimum amount: $1.00"
                                            required
                                        />
                                        <div className="flex justify-end">
                                            <Button
                                                type="submit"
                                                variant="success"
                                                loading={loading}
                                                disabled={!balanceAmount}
                                                icon="fas fa-plus"
                                            >
                                                Add Balance
                                            </Button>
                                        </div>
                                    </form>

                                    <div className="border-t pt-6">
                                        <h4 className="text-sm font-medium text-gray-900 mb-3">
                                            Recent Transactions
                                        </h4>
                                        <div className="text-center py-8 text-gray-500">
                                            <i className="fas fa-receipt text-3xl mb-2"></i>
                                            <p>No recent transactions</p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {activeTab === 'preferences' && (
                                <div className="space-y-6">
                                    <div>
                                        <h4 className="text-sm font-medium text-gray-900 mb-3">
                                            Notification Preferences
                                        </h4>
                                        <div className="space-y-3">
                                            <label className="flex items-center">
                                                <input type="checkbox" className="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                                <span className="ml-2 text-sm text-gray-700">Email notifications for reservations</span>
                                            </label>
                                            <label className="flex items-center">
                                                <input type="checkbox" className="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                                <span className="ml-2 text-sm text-gray-700">SMS notifications for parking reminders</span>
                                            </label>
                                            <label className="flex items-center">
                                                <input type="checkbox" className="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                                <span className="ml-2 text-sm text-gray-700">Marketing emails</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <h4 className="text-sm font-medium text-gray-900 mb-3">
                                            Display Preferences
                                        </h4>
                                        <div className="space-y-3">
                                            <div>
                                                <label className="block text-sm text-gray-700 mb-1">Theme</label>
                                                <select className="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                    <option>Light</option>
                                                    <option>Dark</option>
                                                    <option>Auto</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex justify-end">
                                        <Button icon="fas fa-save">
                                            Save Preferences
                                        </Button>
                                    </div>
                                </div>
                            )}
                        </Card.Content>
                    </Card>
                </div>
            </div>
        </div>
    );
};

export default ProfilePage;