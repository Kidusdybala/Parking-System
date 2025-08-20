import React, { useState, useEffect } from 'react';
import axios from 'axios';

const Reservations = () => {
    const [reservations, setReservations] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchReservations();
    }, []);

    const fetchReservations = async () => {
        try {
            const response = await axios.get('/api/reservations');
            setReservations(response.data.data || []);
        } catch (error) {
            console.error('Error fetching reservations:', error);
        } finally {
            setLoading(false);
        }
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'completed':
                return 'bg-blue-100 text-blue-800';
            case 'cancelled':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const handleCancelReservation = async (reservationId) => {
        if (!confirm('Are you sure you want to cancel this reservation?')) {
            return;
        }

        try {
            await axios.post(`/api/reservations/${reservationId}/cancel`);
            fetchReservations(); // Refresh the list
        } catch (error) {
            console.error('Error cancelling reservation:', error);
            alert('Failed to cancel reservation');
        }
    };

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
                <h1 className="text-2xl font-bold text-gray-900">My Reservations</h1>
                <p className="mt-1 text-sm text-gray-600">
                    View and manage your parking reservations
                </p>
            </div>

            {reservations.length > 0 ? (
                <div className="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul className="divide-y divide-gray-200">
                        {reservations.map((reservation) => (
                            <li key={reservation.id}>
                                <div className="px-4 py-4 sm:px-6">
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center">
                                            <div className="flex-shrink-0">
                                                <i className="fas fa-parking text-2xl text-blue-600"></i>
                                            </div>
                                            <div className="ml-4">
                                                <div className="flex items-center">
                                                    <p className="text-sm font-medium text-blue-600 truncate">
                                                        Parking Spot #{reservation.parking_spot_id}
                                                    </p>
                                                    <span className={`ml-2 px-2 py-1 text-xs rounded-full ${getStatusColor(reservation.status)}`}>
                                                        {reservation.status}
                                                    </span>
                                                </div>
                                                <div className="mt-2 flex items-center text-sm text-gray-500">
                                                    <i className="fas fa-calendar mr-1"></i>
                                                    <p>
                                                        {new Date(reservation.start_time).toLocaleDateString()} - {' '}
                                                        {new Date(reservation.end_time).toLocaleDateString()}
                                                    </p>
                                                </div>
                                                <div className="mt-1 flex items-center text-sm text-gray-500">
                                                    <i className="fas fa-clock mr-1"></i>
                                                    <p>
                                                        {new Date(reservation.start_time).toLocaleTimeString()} - {' '}
                                                        {new Date(reservation.end_time).toLocaleTimeString()}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex items-center space-x-2">
                                            <div className="text-right">
                                                <p className="text-sm font-medium text-gray-900">
                                                    ${reservation.total_cost || 0}
                                                </p>
                                                <p className="text-xs text-gray-500">Total Cost</p>
                                            </div>
                                            {reservation.status === 'active' && (
                                                <button
                                                    onClick={() => handleCancelReservation(reservation.id)}
                                                    className="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700"
                                                >
                                                    Cancel
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        ))}
                    </ul>
                </div>
            ) : (
                <div className="text-center py-12">
                    <i className="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <p className="text-gray-500 mb-4">No reservations found</p>
                    <button className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Make a Reservation
                    </button>
                </div>
            )}
        </div>
    );
};

export default Reservations;