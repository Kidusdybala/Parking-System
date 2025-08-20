import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { useApi } from '../hooks/useApi';
import Card from '../components/ui/Card';
import Button from '../components/ui/Button';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const DashboardPage = () => {
    const { user } = useAuth();
    const { get, loading } = useApi();
    const [stats, setStats] = useState({
        totalSpots: 0,
        availableSpots: 0,
        myReservations: 0,
        activeReservations: 0
    });
    const [recentReservations, setRecentReservations] = useState([]);

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            const [spotsResult, reservationsResult] = await Promise.all([
                get('/api/parking-spots'),
                get('/api/reservations')
            ]);

            if (spotsResult.success) {
                const spots = spotsResult.data.data || [];
                setStats(prev => ({
                    ...prev,
                    totalSpots: spots.length,
                    availableSpots: spots.filter(spot => spot.status === 'available').length
                }));
            }

            if (reservationsResult.success) {
                const reservations = reservationsResult.data.data || [];
                setStats(prev => ({
                    ...prev,
                    myReservations: reservations.length,
                    activeReservations: reservations.filter(r => r.status === 'active').length
                }));
                setRecentReservations(reservations.slice(0, 5));
            }
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    };

    const StatCard = ({ title, value, icon, color, link }) => (
        <Card hover className="relative">
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
            {link && (
                <div className="mt-4">
                    <Link 
                        to={link} 
                        className="text-sm font-medium text-blue-600 hover:text-blue-500"
                    >
                        View all →
                    </Link>
                </div>
            )}
        </Card>
    );

    if (loading) {
        return <LoadingSpinner message="Loading dashboard..." />;
    }

    return (
        <div className="space-y-8">
            {/* Welcome Section */}
            <div>
                <h1 className="text-2xl font-bold text-gray-900">
                    Welcome back, {user?.name}! 👋
                </h1>
                <p className="mt-1 text-sm text-gray-600">
                    Here's what's happening with your parking today.
                </p>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
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

            {/* Quick Actions & Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {/* Quick Actions */}
                <Card>
                    <Card.Header>
                        <Card.Title>Quick Actions</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <div className="space-y-3">
                            <Link
                                to="/parking"
                                className="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group"
                            >
                                <i className="fas fa-search text-blue-600 mr-3 group-hover:scale-110 transition-transform"></i>
                                <span className="text-blue-900 font-medium">Find Parking Spot</span>
                            </Link>
                            <Link
                                to="/reservations"
                                className="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group"
                            >
                                <i className="fas fa-plus text-green-600 mr-3 group-hover:scale-110 transition-transform"></i>
                                <span className="text-green-900 font-medium">Make Reservation</span>
                            </Link>
                            <Link
                                to="/profile"
                                className="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group"
                            >
                                <i className="fas fa-wallet text-purple-600 mr-3 group-hover:scale-110 transition-transform"></i>
                                <span className="text-purple-900 font-medium">Add Balance</span>
                            </Link>
                        </div>
                    </Card.Content>
                </Card>

                {/* Recent Reservations */}
                <Card>
                    <Card.Header>
                        <div className="flex items-center justify-between">
                            <Card.Title>Recent Reservations</Card.Title>
                            <Link 
                                to="/reservations"
                                className="text-sm text-blue-600 hover:text-blue-500"
                            >
                                View all
                            </Link>
                        </div>
                    </Card.Header>
                    <Card.Content>
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
                            <div className="text-center py-6">
                                <i className="fas fa-calendar-times text-3xl text-gray-400 mb-2"></i>
                                <p className="text-gray-500 text-sm mb-3">No recent reservations</p>
                                <Button 
                                    as={Link} 
                                    to="/parking" 
                                    variant="primary" 
                                    size="small"
                                    icon="fas fa-plus"
                                >
                                    Make a Reservation
                                </Button>
                            </div>
                        )}
                    </Card.Content>
                </Card>
            </div>

            {/* Account Summary */}
            <Card>
                <Card.Header>
                    <Card.Title>Account Summary</Card.Title>
                </Card.Header>
                <Card.Content>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="text-center">
                            <div className="text-2xl font-bold text-gray-900">{user?.name}</div>
                            <div className="text-sm text-gray-500">Account Holder</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-blue-600">{user?.email}</div>
                            <div className="text-sm text-gray-500">Email Address</div>
                        </div>
                        <div className="text-center">
                            <div className="text-2xl font-bold text-green-600">${user?.balance || 0}</div>
                            <div className="text-sm text-gray-500">Current Balance</div>
                        </div>
                    </div>
                </Card.Content>
            </Card>
        </div>
    );
};

export default DashboardPage;