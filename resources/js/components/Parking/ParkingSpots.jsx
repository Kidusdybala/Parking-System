import { useState, useEffect } from 'react';
import axios from 'axios';

const ParkingSpots = () => {
    const [spots, setSpots] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all');

    useEffect(() => {
        fetchParkingSpots();
    }, []);

    const fetchParkingSpots = async () => {
        try {
            const response = await axios.get('/api/parking-spots');
            setSpots(response.data.data || []);
        } catch (error) {
            console.error('Error fetching parking spots:', error);
        } finally {
            setLoading(false);
        }
    };

    const filteredSpots = Array.isArray(spots) ? spots.filter(spot => {
        if (filter === 'available') return spot.status === 'available';
        if (filter === 'occupied') return spot.status === 'occupied';
        return true;
    }) : [];

    const getStatusColor = (status) => {
        switch (status) {
            case 'available':
                return 'bg-green-100 text-green-800';
            case 'occupied':
                return 'bg-red-100 text-red-800';
            case 'reserved':
                return 'bg-yellow-100 text-yellow-800';
            default:
                return 'bg-gray-100 text-gray-800';
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
                <h1 className="text-2xl font-bold text-gray-900">Parking Spots</h1>
                <p className="mt-1 text-sm text-gray-600">
                    View and manage parking spots
                </p>
            </div>

            {/* Filter Buttons */}
            <div className="mb-6">
                <div className="flex space-x-4">
                    <button
                        onClick={() => setFilter('all')}
                        className={`px-4 py-2 rounded-md text-sm font-medium ${
                            filter === 'all'
                                ? 'bg-blue-600 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                    >
                        All Spots
                    </button>
                    <button
                        onClick={() => setFilter('available')}
                        className={`px-4 py-2 rounded-md text-sm font-medium ${
                            filter === 'available'
                                ? 'bg-green-600 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                    >
                        Available
                    </button>
                    <button
                        onClick={() => setFilter('occupied')}
                        className={`px-4 py-2 rounded-md text-sm font-medium ${
                            filter === 'occupied'
                                ? 'bg-red-600 text-white'
                                : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                        }`}
                    >
                        Occupied
                    </button>
                </div>
            </div>

            {/* Parking Spots Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {filteredSpots.map((spot) => (
                    <div key={spot.id} className="bg-white shadow rounded-lg p-6">
                        <div className="flex items-center justify-between mb-4">
                            <h3 className="text-lg font-medium text-gray-900">
                                Spot #{spot.id}
                            </h3>
                            <span className={`px-2 py-1 text-xs rounded-full ${getStatusColor(spot.status)}`}>
                                {spot.status}
                            </span>
                        </div>
                        
                        <div className="space-y-2 text-sm text-gray-600">
                            <p><strong>Location:</strong> {spot.location || 'N/A'}</p>
                            <p><strong>Type:</strong> {spot.type || 'Standard'}</p>
                            <p><strong>Rate:</strong> ${spot.hourly_rate || 0}/hour</p>
                        </div>

                        {spot.status === 'available' && (
                            <button className="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                Reserve Spot
                            </button>
                        )}
                    </div>
                ))}
            </div>

            {filteredSpots.length === 0 && (
                <div className="text-center py-12">
                    <i className="fas fa-parking text-4xl text-gray-400 mb-4"></i>
                    <p className="text-gray-500">No parking spots found</p>
                </div>
            )}
        </div>
    );
};

export default ParkingSpots;
