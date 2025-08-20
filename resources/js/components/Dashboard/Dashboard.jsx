import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import axios from 'axios';

const Dashboard = () => {
    const { user } = useAuth();
    const [stats, setStats] = useState({
        totalSpots: 0,
        availableSpots: 0,
        myReservations: 0,
        activeReservations: 0
    });
    const [recentReservations, setRecentReservations] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            const [spotsResponse, reservationsResponse] = await Promise.all([
                axios.get('/api/parking-spots'),
                axios.get('/api/reservations')
            ]);

            const spots = spotsResponse.data.data || [];
            const reservations = reservationsResponse.data.data || [];

            setStats({
                totalSpots: spots.length,
                availableSpots: spots.filter(spot => spot.status === 'available').length,
                myReservations: reservations.length,
                activeReservations: reservations.filter(r => r.status === 'active').length
            });

            setRecentReservations(reservations.slice(0, 5));
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        } finally {
            setLoading(false);
        }
    };

    const StatCard = ({ title, value, icon, color, link }) => (
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
            {link && (
                <div className="bg-gray-50 px-5 py-3">
                    <div className="text-sm">
                        <Link to={link} className="font-medium text-blue-700 hover:text-blue-900">
                            View all
                        </Link>
                    </div>
                </div>
            )}
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
                <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p className="mt-1 text-sm text-gray-600">
                    Welcome to your parking management dashboard
                </p>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <StatCard
                    title="Total Parking Spots"
                    value={stats.totalSpots}
                    icon="fas fa-parking"
                    color="text-blue-600"
                    link="/parking"
                />
                <StatCard
                    title="Available Spots"
                    value={stats.availableSpots}
                    icon="fas fa-check-circle"
                    color="text-green-600"
                    link="/parking"
                />
                <StatCard
                    title="My Reservations"
                    value={stats.myReservations}
                    icon="fas fa-calendar-check"
                    color="text-purple-600"
                    link="/reservations"
                />
                <StatCard
                    title="Active Reservations"
                    value={stats.activeReservations}
                    icon="fas fa-clock"
                    color="text-orange-600"
                    link="/reservations"
                />
            </div>

            {/* Quick Actions */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <div className="bg-white shadow rounded-lg p-6">
                    <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div className="space-y-3">
                        <Link
                            to="/parking"
                            className="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                        >
                            <i className="fas fa-search text-blue-600 mr-3"></i>
                            <span className="text-blue-900 font-medium">Find Parking Spot</span>
                        </Link>
                        <Link
                            to="/reservations"
                            className="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
                        >
                            <i className="fas fa-plus text-green-600 mr-3"></i>
                            <span className="text-green-900 font-medium">Make Reservation</span>
                        </Link>
                        <Link
                            to="/profile"
                            className="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
                        >
                            <i className="fas fa-wallet text-purple-600 mr-3"></i>
                            <span className="text-purple-900 font-medium">Add Balance</span>
                        </Link>
                    </div>
                </div>

                {/* Recent Reservations */}
                <div className="bg-white shadow rounded-lg p-6">
                    <h3 className="text-lg font-medium text-gray-900 mb-4">Recent Reservations</h3>
                    {recentReservations.length > 0 ? (
                        <div className="space-y-3">
                            {recentReservations.map((reservation) => (
                                <div key={reservation.id} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p className="text-sm font-medium text-gray-900">
                                            Spot #{reservation.parking_spot_id}
                                        </p>
                                        <p className="text-xs text-gray-500">
                                            {new Date(reservation.start_time).toLocaleDateString()}
                                        </p>
                                    </div>
                                    <span className={`px-2 py-1 text-xs rounded-full ${
                                        reservation.status === 'active' 
                                            ? 'bg-green-100 text-green-800'
                                            : reservation.status === 'completed'
                                            ? 'bg-blue-100 text-blue-800'
                                            : 'bg-gray-100 text-gray-800'
                                    }`}>
                                        {reservation.status}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-gray-500 text-sm">No recent reservations</p>
                    )}
                </div>
            </div>

            {/* Account Info */}
            <div className="bg-white shadow rounded-lg p-6">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Account Information</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p className="text-sm text-gray-500">Name</p>
                        <p className="font-medium">{user?.name}</p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-500">Email</p>
                        <p className="font-medium">{user?.email}</p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-500">Account Balance</p>
                        <p className="font-medium text-green-600">${user?.balance || 0}</p>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;