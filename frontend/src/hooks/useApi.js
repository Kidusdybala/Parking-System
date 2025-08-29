import { useState, useCallback } from 'react';
import axios from 'axios';

export const useApi = () => {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const request = useCallback(async (config) => {
        setLoading(true);
        setError(null);
        
        try {
            // Ensure credentials are included with the request
            const requestConfig = {
                ...config,
                withCredentials: true,
                headers: {
                    ...config.headers,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            };
            
            const response = await axios(requestConfig);
            return { data: response.data, success: true };
        } catch (err) {
            const errorMessage = err.response?.data?.message || err.message || 'An error occurred';
            setError(errorMessage);
            return { 
                data: null, 
                success: false, 
                error: errorMessage,
                errors: err.response?.data?.errors || {}
            };
        } finally {
            setLoading(false);
        }
    }, []);

    const get = useCallback((url, config = {}) => {
        return request({ method: 'GET', url, ...config });
    }, [request]);

    const post = useCallback((url, data, config = {}) => {
        return request({ method: 'POST', url, data, ...config });
    }, [request]);

    const put = useCallback((url, data, config = {}) => {
        return request({ method: 'PUT', url, data, ...config });
    }, [request]);

    const del = useCallback((url, config = {}) => {
        return request({ method: 'DELETE', url, ...config });
    }, [request]);

    return {
        loading,
        error,
        get,
        post,
        put,
        delete: del,
        request
    };
};