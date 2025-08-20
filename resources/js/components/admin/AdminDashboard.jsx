import { useState, useEffect } from 'react';
import axios from 'axios';

const AdminDashboard = () => {
    const [stats, setStats] = useState({
        totalUsers: 0,
        totalSpots: 0,
        totalReservations: 0,
        totalRevenue: 0
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchAdminStats();
    }, []);

    const fetchAdminStats = async () => {
        try {
            const [usersResponse, spotsResponse, reservationsResponse] = await Promise.all([
                axios.get('/api/users/statistics'),
                axios.get('/api/parking-spots'),
                axios.get('/api/reservations/statistics')
            ]);

            setStats({
                totalUsers: usersResponse.data.total || 0,
                totalSpots: spotsResponse.data.data?.length || 0,
                totalReservations: reservationsResponse.data.total || 0,
                totalRevenue: reservationsResponse.data.revenue || 0
            });
        } catch (error) {
            console.error('Error fetching admin stats:', error);
        } finally {
            setLoading(false);
        }
    };

    const StatCard = ({ title, value, icon, color }) => (
        <div className="bg-white overflow-hidden shadow rounded-lg">
            <div className="p-5">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <i className={`${icon} text-2xl ${color}`}></i>
                    </div>
                    <div className="ml-5 w-0 flex-1">
                        <dl>
                            <dt className="text-sm font-medium text-gray-500 truncate">
                                {title}
                            </dt>
                            <dd className="text-lg font-medium text-gray-900">
                                {value}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    );

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-4 border-blue-200 border-t-blue-600"></div>
            </div>
        );
    }

    return (
        <div>
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p className="mt-1 text-sm text-gray-600">
                    System overview and management tools
                </p>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <StatCard
                    title="Total Users"
                    value={stats.totalUsers}
                    icon="fas fa-users"
                    color="text-blue-600"
                />
                <StatCard
                    title="Parking Spots"
                    value={stats.totalSpots}
                    icon="fas fa-parking"
                    color="text-green-600"
                />
                <StatCard
                    title="Total Reservations"
                    value={stats.totalReservations}
                    icon="fas fa-calendar-check"
                    color="text-purple-600"
                />
                <StatCard
                    title="Total Revenue"
                    value={`$${stats.totalRevenue}`}
                    icon="fas fa-dollar-sign"
                    color="text-yellow-600"
                />
            </div>

            {/* Admin Actions */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div className="bg-white shadow rounded-lg p-6">
                    <h3 className="text-lg font-medium text-gray-900 mb-4">User Management</h3>
                    <div className="space-y-3">
                        <button className="w-full flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i className="fas fa-users text-blue-600 mr-3"></i>
                            <span className="text-blue-900 font-medium">View All Users</span>
                        </button>
                        <button className="w-full flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i className="fas fa-user-plus text-green-600 mr-3"></i>
                            <span className="text-green-900 font-medium">Add New User</span>
                        </button>
                        <button className="w-full flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <i className="fas fa-chart-bar text-purple-600 mr-3"></i>
                            <span className="text-purple-900 font-medium">User Statistics</span>
                        </button>
                    </div>
                </div>

                <div className="bg-white shadow rounded-lg p-6">
                    <h3 className="text-lg font-medium text-gray-900 mb-4">Parking Management</h3>
                    <div className="space-y-3">
                        <button className="w-full flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i className="fas fa-parking text-blue-600 mr-3"></i>
                            <span className="text-blue-900 font-medium">Manage Parking Spots</span>
                        </button>
                        <button className="w-full flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <i className="fas fa-plus text-green-600 mr-3"></i>
                            <span className="text-green-900 font-medium">Add New Spot</span>
                        </button>
                        <button className="w-full flex items-center p-3 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                            <i className="fas fa-calendar-alt text-orange-600 mr-3"></i>
                            <span className="text-orange-900 font-medium">View All Reservations</span>
                        </button>
                    </div>
                </div>
            </div>

            {/* Recent Activity */}
            <div className="mt-8 bg-white shadow rounded-lg p-6">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                <div className="text-center py-8">
                    <i className="fas fa-clock text-4xl text-gray-400 mb-4"></i>
                    <p className="text-gray-500">Recent activity will appear here</p>
                </div>
            </div>
        </div>
    );
};

export default AdminDashboard;
