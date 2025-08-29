import { useMemo, useState, useCallback, useEffect } from 'react';
import { useReservations } from './useReservations';

export const useReservationForm = ({ initialSpot, onSuccess }) => {
    const { createReservation } = useReservations();
    const [form, setForm] = useState(() => ({
        parking_spot_id: initialSpot?.id ?? null,
        start_time: new Date().toISOString().slice(0, 16),
        end_time: new Date(Date.now() + 2 * 60 * 60 * 1000).toISOString().slice(0, 16)
    }));
    const [submitting, setSubmitting] = useState(false);

    const hours = useMemo(() => {
        const start = form.start_time ? new Date(form.start_time) : null;
        const end = form.end_time ? new Date(form.end_time) : null;
        if (!start || !end) return 0;
        const diff = Math.max(0, end - start);
        return diff / 36e5;
    }, [form.start_time, form.end_time]);

    const totalCost = useMemo(() => {
        if (!initialSpot) return 0;
        return Number((hours * parseFloat(initialSpot.hourly_rate || 0)).toFixed(2));
    }, [hours, initialSpot]);

    useEffect(() => {
        if (!initialSpot) return;
        setForm(prev => ({
            ...prev,
            parking_spot_id: initialSpot.id
        }));
    }, [initialSpot]);

    const updateField = useCallback((key, value) => {
        setForm(prev => ({ ...prev, [key]: value }));
    }, []);

    const submit = useCallback(async () => {
        setSubmitting(true);
        const result = await createReservation(form);
        setSubmitting(false);
        if (result.success) {
            onSuccess?.();
        }
        return result;
    }, [createReservation, form, onSuccess]);

    return {
        form,
        updateField,
        submit,
        submitting,
        hours,
        totalCost
    };
};


