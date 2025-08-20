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
            const spotsData = result.data?.data || result.data || [];
            // Ensure we always have an array
            setSpots(Array.isArray(spotsData) ? spotsData : []);
        } else {
            // On error, ensure spots is still an array
            setSpots([]);
        }
    }, [get]);

    const fetchAvailableSpots = useCallback(async () => {
        const result = await get('/api/parking-spots/available/list');
        if (result.success) {
            const spotsData = result.data?.data || result.data || [];
            return Array.isArray(spotsData) ? spotsData : [];
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
        // Ensure spots is always an array before filtering
        const spotsArray = Array.isArray(spots) ? spots : [];
        let filtered = spotsArray;
        
        switch (filter) {
            case 'available':
                filtered = spotsArray.filter(spot => spot.status === 'available');
                break;
            case 'occupied':
                filtered = spotsArray.filter(spot => spot.status === 'occupied');
                break;
            case 'reserved':
                filtered = spotsArray.filter(spot => spot.status === 'reserved');
                break;
            default:
                filtered = spotsArray;
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