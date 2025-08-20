import React, { useState, useEffect } from 'react';
import { useApi } from '../hooks';
import { Card, Button, Modal, Input, Alert } from '../components/ui';
import { formatCurrency, getStatusColor, isAdmin } from '../utils';
import { useAuth } from '../contexts/AuthContext';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const AdminPage = () => {
    const { user } = useAuth();
    const { get, post, put, delete: del, loading } = useApi();
    const [activeTab, setActiveTab] = useState('overview');
    const [stats, setStats] = useState({
        totalUsers: 0,
        totalSpots: 0,
        totalReservations: 0,
        totalRevenue: 0
    });
    const [users, setUsers] = useState([]);
    const [spots, setSpots] = useState([]);
    const [reservations, setReservations] = useState([]);
    const [showModal, setShowModal] = useState(false);
    const [modalType, setModalType] = useState('');
    const [selectedItem, setSelectedItem] = useState(null);

    const tabs = [
        { id: 'overview', name: 'Overview', icon: 'fas fa-tachometer-alt' },
        { id: 'users', name: 'Users', icon: 'fas fa-users' },
        { id: 'spots', name: 'Parking Spots', icon: 'fas fa-parking' },
        { id: 'reservations', name: 'Reservations', icon: 'fas fa-calendar-check' }
    ];

    useEffect(() => {
        if (!isAdmin(user)) return;
        fetchAdminData();
    }, [user]);

    const fetchAdminData = async () => {
        try {
            const [usersResult, spotsResult, reservationsResult] = await Promise.all([
                get('/api/users'),
                get('/api/parking-spots'),
                get('/api/reservations/all')
            ]);

            if (usersResult.success) {
                setUsers(usersResult.data.data || []);
                setStats(prev => ({ ...prev, totalUsers: usersResult.data.data?.length || 0 }));
            }

            if (spotsResult.success) {
                setSpots(spotsResult.data.data || []);
                setStats(prev => ({ ...prev, totalSpots: spotsResult.data.data?.length || 0 }));
            }

            if (reservationsResult.success) {
                const reservationData = reservationsResult.data.data || [];
                setReservations(reservationData);
                const revenue = reservationData.reduce((sum, r) => sum + (r.total_cost || 0), 0);
                setStats(prev => ({ 
                    ...prev, 
                    totalReservations: reservationData.length,
                    totalRevenue: revenue
                }));
            }
        } catch (error) {
            console.error('Error fetching admin data:', error);
        }
    };

    const handleOpenModal = (type, item = null) => {
        setModalType(type);
        setSelectedItem(item);
        setShowModal(true);
    };

    const handleCloseModal = () => {
        setShowModal(false);
        setModalType('');
        setSelectedItem(null);
    };

    if (!isAdmin(user)) {
        return (
            <div className="text-center py-12">
                <Alert type="error">
                    Access denied. Admin privileges required.
                </Alert>
            </div>
        );
    }

    if (loading && !users.length && !spots.length) {
        return <LoadingSpinner message="Loading admin dashboard..." />;
    }

    const StatCard = ({ title, value, icon, color, onClick }) => (
        <Card hover onClick={onClick} className="cursor-pointer">
            <Card.Content>
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
                                {typeof value === 'number' && title.includes('Revenue') 
                                    ? formatCurrency(value) 
                                    : value
                                }
                            </dd>
                        </dl>
                    </div>
                </div>
            </Card.Content>
        </Card>
    );

    return (
        <div className="space-y-6">
            {/* Header */}
            <div>
                <h1 className="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p className="mt-1 text-sm text-gray-600">
                    System overview and management tools
                </p>
            </div>

            {/* Stats Grid */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard
                    title="Total Users"
                    value={stats.totalUsers}
                    icon="fas fa-users"
                    color="text-blue-600"
                    onClick={() => setActiveTab('users')}
                />
                <StatCard
                    title="Parking Spots"
                    value={stats.totalSpots}
                    icon="fas fa-parking"
                    color="text-green-600"
                    onClick={() => setActiveTab('spots')}
                />
                <StatCard
                    title="Total Reservations"
                    value={stats.totalReservations}
                    icon="fas fa-calendar-check"
                    color="text-purple-600"
                    onClick={() => setActiveTab('reservations')}
                />
                <StatCard
                    title="Total Revenue"
                    value={stats.totalRevenue}
                    icon="fas fa-dollar-sign"
                    color="text-yellow-600"
                />
            </div>

            {/* Tab Navigation */}
            <Card>
                <Card.Content>
                    <div className="flex flex-wrap gap-2">
                        {tabs.map((tab) => (
                            <Button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                variant={activeTab === tab.id ? 'primary' : 'outline'}
                                icon={tab.icon}
                                size="small"
                            >
                                {tab.name}
                            </Button>
                        ))}
                    </div>
                </Card.Content>
            </Card>

            {/* Tab Content */}
            <div>
                {activeTab === 'overview' && (
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <Card>
                            <Card.Header>
                                <Card.Title>Quick Actions</Card.Title>
                            </Card.Header>
                            <Card.Content>
                                <div className="space-y-3">
                                    <Button
                                        onClick={() => handleOpenModal('create-spot')}
                                        variant="primary"
                                        icon="fas fa-plus"
                                        className="w-full justify-start"
                                    >
                                        Add New Parking Spot
                                    </Button>
                                    <Button
                                        onClick={() => handleOpenModal('create-user')}
                                        variant="success"
                                        icon="fas fa-user-plus"
                                        className="w-full justify-start"
                                    >
                                        Add New User
                                    </Button>
                                    <Button
                                        onClick={fetchAdminData}
                                        variant="outline"
                                        icon="fas fa-sync-alt"
                                        className="w-full justify-start"
                                    >
                                        Refresh Data
                                    </Button>
                                </div>
                            </Card.Content>
                        </Card>

                        <Card>
                            <Card.Header>
                                <Card.Title>Recent Activity</Card.Title>
                            </Card.Header>
                            <Card.Content>
                                <div className="text-center py-8 text-gray-500">
                                    <i className="fas fa-clock text-3xl mb-2"></i>
                                    <p>Recent activity will appear here</p>
                                </div>
                            </Card.Content>
                        </Card>
                    </div>
                )}

                {activeTab === 'users' && (
                    <Card>
                        <Card.Header>
                            <div className="flex justify-between items-center">
                                <Card.Title>User Management</Card.Title>
                                <Button
                                    onClick={() => handleOpenModal('create-user')}
                                    variant="primary"
                                    icon="fas fa-user-plus"
                                    size="small"
                                >
                                    Add User
                                </Button>
                            </div>
                        </Card.Header>
                        <Card.Content>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Email
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Balance
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Role
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {users.map((user) => (
                                            <tr key={user.id}>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {user.name}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-500">
                                                        {user.email}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-900">
                                                        {formatCurrency(user.balance || 0)}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                                        {user.role === 3 ? 'Admin' : user.role === 2 ? 'Moderator' : 'User'}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <Button
                                                        onClick={() => handleOpenModal('edit-user', user)}
                                                        variant="outline"
                                                        size="small"
                                                        icon="fas fa-edit"
                                                    >
                                                        Edit
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </Card.Content>
                    </Card>
                )}

                {activeTab === 'spots' && (
                    <Card>
                        <Card.Header>
                            <div className="flex justify-between items-center">
                                <Card.Title>Parking Spot Management</Card.Title>
                                <Button
                                    onClick={() => handleOpenModal('create-spot')}
                                    variant="primary"
                                    icon="fas fa-plus"
                                    size="small"
                                >
                                    Add Spot
                                </Button>
                            </div>
                        </Card.Header>
                        <Card.Content>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                {spots.map((spot) => (
                                    <Card key={spot.id} hover>
                                        <Card.Content>
                                            <div className="flex justify-between items-start mb-2">
                                                <h4 className="font-medium">Spot #{spot.id}</h4>
                                                <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(spot.status)}`}>
                                                    {spot.status}
                                                </span>
                                            </div>
                                            <div className="text-sm text-gray-600 space-y-1">
                                                <p><strong>Location:</strong> {spot.location || 'N/A'}</p>
                                                <p><strong>Type:</strong> {spot.type || 'Standard'}</p>
                                                <p><strong>Rate:</strong> {formatCurrency(spot.hourly_rate || 0)}/hour</p>
                                            </div>
                                            <div className="mt-3 flex space-x-2">
                                                <Button
                                                    onClick={() => handleOpenModal('edit-spot', spot)}
                                                    variant="outline"
                                                    size="small"
                                                    icon="fas fa-edit"
                                                >
                                                    Edit
                                                </Button>
                                                <Button
                                                    variant="danger"
                                                    size="small"
                                                    icon="fas fa-trash"
                                                >
                                                    Delete
                                                </Button>
                                            </div>
                                        </Card.Content>
                                    </Card>
                                ))}
                            </div>
                        </Card.Content>
                    </Card>
                )}

                {activeTab === 'reservations' && (
                    <Card>
                        <Card.Header>
                            <Card.Title>Reservation Management</Card.Title>
                        </Card.Header>
                        <Card.Content>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Reservation ID
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                User
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Spot
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Cost
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {reservations.slice(0, 10).map((reservation) => (
                                            <tr key={reservation.id}>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    #{reservation.id}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {reservation.user?.name || 'N/A'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    Spot #{reservation.parking_spot_id}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(reservation.status)}`}>
                                                        {reservation.status}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {formatCurrency(reservation.total_cost || 0)}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <Button
                                                        variant="outline"
                                                        size="small"
                                                        icon="fas fa-eye"
                                                    >
                                                        View
                                                    </Button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </Card.Content>
                    </Card>
                )}
            </div>

            {/* Modal for Create/Edit operations */}
            <Modal
                isOpen={showModal}
                onClose={handleCloseModal}
                title={
                    modalType === 'create-user' ? 'Add New User' :
                    modalType === 'edit-user' ? 'Edit User' :
                    modalType === 'create-spot' ? 'Add New Parking Spot' :
                    modalType === 'edit-spot' ? 'Edit Parking Spot' : ''
                }
                size="medium"
            >
                <div className="space-y-4">
                    <p className="text-gray-600">
                        Modal content for {modalType} will be implemented here.
                    </p>
                    <div className="flex justify-end space-x-3">
                        <Button onClick={handleCloseModal} variant="outline">
                            Cancel
                        </Button>
                        <Button variant="primary">
                            Save
                        </Button>
                    </div>
                </div>
            </Modal>
        </div>
    );
};

export default AdminPage;