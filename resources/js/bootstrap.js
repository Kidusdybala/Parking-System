import axios from 'axios';
import Cookies from 'js-cookie';

// Set up axios defaults
window.axios = axios;

// Configure base URL for API requests
// In development, API runs on Laravel server (usually port 8000)
// In production, it should be the same domain
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000';
axios.defaults.baseURL = API_BASE_URL;

console.log('API Base URL:', API_BASE_URL);

// Set default headers
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Add request interceptor to include auth token
axios.interceptors.request.use(
    (config) => {
        const token = Cookies.get('auth_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Add response interceptor for token refresh
axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                const response = await axios.post('/api/auth/refresh');

                if (response.data.success) {
                    const newToken = response.data.token;
                    Cookies.set('auth_token', newToken, { expires: 7 });

                    // Retry original request with new token
                    originalRequest.headers.Authorization = `Bearer ${newToken}`;
                    return axios(originalRequest);
                }
            } catch (refreshError) {
                // Refresh failed, redirect to login
                Cookies.remove('auth_token');
                window.location.href = '/login';
            }
        }

        return Promise.reject(error);
    }
);
