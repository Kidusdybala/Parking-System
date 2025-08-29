import { useState, useEffect, useCallback } from 'react';
import { useApi } from './useApi';

export const useReservations = () => {
    const [reservations, setReservations] = useState([]);
    const { get, post, put, loading, error } = useApi();

    const fetchReservations = useCallback(async () => {
        const result = await get('/api/reservations');
        if (result.success) {
            const reservationsData = result.data?.data || result.data || [];
            // Ensure we always have an array
            setReservations(Array.isArray(reservationsData) ? reservationsData : []);
        } else {
            // On error, ensure reservations is still an array
            setReservations([]);
        }
    }, [get]);

    const createReservation = useCallback(async (reservationData) => {
        const result = await post('/api/reservations', reservationData);
        if (result.success) {
            await fetchReservations(); // Refresh the list
        }
        return result;
    }, [post, fetchReservations]);

    const updateReservation = useCallback(async (id, updateData) => {
        const result = await put(`/api/reservations/${id}`, updateData);
        if (result.success) {
            await fetchReservations(); // Refresh the list
        }
        return result;
    }, [put, fetchReservations]);

    const cancelReservation = useCallback(async (id) => {
        const result = await post(`/api/reservations/${id}/cancel`);
        if (result.success) {
            await fetchReservations(); // Refresh the list
        }
        return result;
    }, [post, fetchReservations]);

    const getReservationById = useCallback(async (id) => {
        const result = await get(`/api/reservations/${id}`);
        if (result.success) {
            return result.data.data;
        }
        return null;
    }, [get]);

    useEffect(() => {
        fetchReservations();
    }, [fetchReservations]);

    // Ensure reservations is always an array before filtering
    const reservationsArray = Array.isArray(reservations) ? reservations : [];
    const activeReservations = reservationsArray.filter(r => r.status === 'active');
    const completedReservations = reservationsArray.filter(r => r.status === 'completed');
    const cancelledReservations = reservationsArray.filter(r => r.status === 'cancelled');

    return {
        reservations,
        activeReservations,
        completedReservations,
        cancelledReservations,
        loading,
        error,
        createReservation,
        updateReservation,
        cancelReservation,
        getReservationById,
        refreshReservations: fetchReservations
    };
};