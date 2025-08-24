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

// Add response interceptor for token refresh (with loop prevention)
let isRefreshing = false;
let failedQueue = [];

const processQueue = (error, token = null) => {
    failedQueue.forEach(prom => {
        if (error) {
            prom.reject(error);
        } else {
            prom.resolve(token);
        }
    });
    
    failedQueue = [];
};

axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        // Don't retry refresh endpoint or already retried requests
        if (error.config?.url?.includes('/api/auth/refresh') || originalRequest._retry) {
            return Promise.reject(error);
        }

        if (error.response?.status === 401 && !originalRequest._retry) {
            if (isRefreshing) {
                // If already refreshing, queue this request
                return new Promise((resolve, reject) => {
                    failedQueue.push({ resolve, reject });
                }).then(token => {
                    originalRequest.headers.Authorization = `Bearer ${token}`;
                    return axios(originalRequest);
                }).catch(err => {
                    return Promise.reject(err);
                });
            }

            originalRequest._retry = true;
            isRefreshing = true;

            try {
                // Create a new axios instance without interceptors for refresh
                const refreshAxios = axios.create({
                    baseURL: axios.defaults.baseURL,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${Cookies.get('auth_token')}`
                    }
                });

                const response = await refreshAxios.post('/api/auth/refresh');

                if (response.data.success) {
                    const newToken = response.data.token;
                    Cookies.set('auth_token', newToken, { expires: 7 });
                    
                    // Update default header
                    axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
                    
                    processQueue(null, newToken);

                    // Retry original request with new token
                    originalRequest.headers.Authorization = `Bearer ${newToken}`;
                    return axios(originalRequest);
                } else {
                    throw new Error('Token refresh failed');
                }
            } catch (refreshError) {
                console.error('Token refresh failed:', refreshError);
                processQueue(refreshError, null);
                
                // Clear auth data and redirect to login
                Cookies.remove('auth_token');
                delete axios.defaults.headers.common['Authorization'];
                
                // Only redirect if we're not already on login page
                if (!window.location.pathname.includes('/login')) {
                    window.location.href = '/login';
                }
                
                return Promise.reject(refreshError);
            } finally {
                isRefreshing = false;
            }
        }

        return Promise.reject(error);
    }
);
