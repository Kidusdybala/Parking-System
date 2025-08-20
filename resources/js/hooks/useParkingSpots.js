import { useState, useEffect, useCallback } from 'react';
import { useApi } from './useApi';

export const useParkingSpots = () => {
    const [spots, setSpots] = useState([]);
    const [filteredSpots, setFilteredSpots] = useState([]);
    const [filter, setFilter] = useState('all');
    const { get, loading, error } = useApi();

    const fetchSpots = useCallback(async () => {
        const result = await get('/api/parking-spots');
        if (result.success) {
            setSpots(result.data.data || []);
        }
    }, [get]);

    const fetchAvailableSpots = useCallback(async () => {
        const result = await get('/api/parking-spots/available/list');
        if (result.success) {
            return result.data.data || [];
        }
        return [];
    }, [get]);

    const getSpotById = useCallback(async (id) => {
        const result = await get(`/api/parking-spots/${id}`);
        if (result.success) {
            return result.data.data;
        }
        return null;
    }, [get]);

    useEffect(() => {
        fetchSpots();
    }, [fetchSpots]);

    useEffect(() => {
        let filtered = spots;
        
        switch (filter) {
            case 'available':
                filtered = spots.filter(spot => spot.status === 'available');
                break;
            case 'occupied':
                filtered = spots.filter(spot => spot.status === 'occupied');
                break;
            case 'reserved':
                filtered = spots.filter(spot => spot.status === 'reserved');
                break;
            default:
                filtered = spots;
        }
        
        setFilteredSpots(filtered);
    }, [spots, filter]);

    const applyFilter = useCallback((newFilter) => {
        setFilter(newFilter);
    }, []);

    const refreshSpots = useCallback(() => {
        fetchSpots();
    }, [fetchSpots]);

    return {
        spots: filteredSpots,
        allSpots: spots,
        filter,
        loading,
        error,
        applyFilter,
        refreshSpots,
        fetchAvailableSpots,
        getSpotById
    };
};