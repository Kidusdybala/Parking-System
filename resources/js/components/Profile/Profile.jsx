import React, { useState } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import axios from 'axios';

const Profile = () => {
    const { user, updateUser } = useAuth();
    const [activeTab, setActiveTab] = useState('profile');
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');

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

    const handleProfileUpdate = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');

        try {
            const response = await axios.put(`/api/users/${user.id}`, profileData);
            if (response.data.success) {
                updateUser({ ...user, ...profileData });
                setMessage('Profile updated successfully!');
            }
        } catch (error) {
            setMessage('Failed to update profile');
        } finally {
            setLoading(false);
        }
    };

    const handlePasswordChange = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');

        try {
            const response = await axios.post('/api/auth/change-password', passwordData);
            if (response.data.success) {
                setMessage('Password changed successfully!');
                setPasswordData({
                    current_password: '',
                    new_password: '',
                    new_password_confirmation: ''
                });
            }
        } catch (error) {
            setMessage(error.response?.data?.message || 'Failed to change password');
        } finally {
            setLoading(false);
        }
    };

    const handleAddBalance = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');

        try {
            const response = await axios.post(`/api/users/${user.id}/add-balance`, {
                amount: parseFloat(balanceAmount)
            });
            if (response.data.success) {
                updateUser({ ...user, balance: response.data.user.balance });
                setMessage(`Successfully added $${balanceAmount} to your balance!`);
                setBalanceAmount('');
            }
        } catch (error) {
            setMessage('Failed to add balance');
        } finally {
            setLoading(false);
        }
    };

    const tabs = [
        { id: 'profile', name: 'Profile Information', icon: 'fas fa-user' },
        { id: 'password', name: 'Change Password', icon: 'fas fa-lock' },
        { id: 'balance', name: 'Add Balance', icon: 'fas fa-wallet' }
    ];

    return (
        <div>
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Profile Settings</h1>
                <p className="mt-1 text-sm text-gray-600">
                    Manage your account settings and preferences
                </p>
            </div>

            {message && (
                <div className={`mb-6 p-4 rounded-md ${
                    message.includes('success') || message.includes('Successfully')
                        ? 'bg-green-50 text-green-700 border border-green-200'
                        : 'bg-red-50 text-red-700 border border-red-200'
                }`}>
                    {message}
                </div>
            )}

            <div className="bg-white shadow rounded-lg">
                {/* Tab Navigation */}
                <div className="border-b border-gray-200">
                    <nav className="-mb-px flex space-x-8 px-6">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={`py-4 px-1 border-b-2 font-medium text-sm ${
                                    activeTab === tab.id
                                        ? 'border-blue-500 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                                }`}
                            >
                                <i className={`${tab.icon} mr-2`}></i>
                                {tab.name}
                            </button>
                        ))}
                    </nav>
                </div>

                {/* Tab Content */}
                <div className="p-6">
                    {activeTab === 'profile' && (
                        <form onSubmit={handleProfileUpdate} className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    value={profileData.name}
                                    onChange={(e) => setProfileData({...profileData, name: e.target.value})}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    value={profileData.email}
                                    onChange={(e) => setProfileData({...profileData, email: e.target.value})}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Current Balance
                                </label>
                                <p className="mt-1 text-lg font-semibold text-green-600">
                                    ${user?.balance || 0}
                                </p>
                            </div>
                            <button
                                type="submit"
                                disabled={loading}
                                className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                            >
                                {loading ? 'Updating...' : 'Update Profile'}
                            </button>
                        </form>
                    )}

                    {activeTab === 'password' && (
                        <form onSubmit={handlePasswordChange} className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Current Password
                                </label>
                                <input
                                    type="password"
                                    value={passwordData.current_password}
                                    onChange={(e) => setPasswordData({...passwordData, current_password: e.target.value})}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    New Password
                                </label>
                                <input
                                    type="password"
                                    value={passwordData.new_password}
                                    onChange={(e) => setPasswordData({...passwordData, new_password: e.target.value})}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Confirm New Password
                                </label>
                                <input
                                    type="password"
                                    value={passwordData.new_password_confirmation}
                                    onChange={(e) => setPasswordData({...passwordData, new_password_confirmation: e.target.value})}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={loading}
                                className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
                            >
                                {loading ? 'Changing...' : 'Change Password'}
                            </button>
                        </form>
                    )}

                    {activeTab === 'balance' && (
                        <form onSubmit={handleAddBalance} className="space-y-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Current Balance
                                </label>
                                <p className="mt-1 text-2xl font-bold text-green-600">
                                    ${user?.balance || 0}
                                </p>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700">
                                    Amount to Add
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="1"
                                    value={balanceAmount}
                                    onChange={(e) => setBalanceAmount(e.target.value)}
                                    className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Enter amount"
                                    required
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={loading || !balanceAmount}
                                className="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 disabled:opacity-50"
                            >
                                {loading ? 'Adding...' : 'Add Balance'}
                            </button>
                        </form>
                    )}
                </div>
            </div>
        </div>
    );
};

export default Profile;