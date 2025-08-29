import axios from './api';
import { API_ENDPOINTS } from '../utils/constants';

class AuthService {
    async login(email, password) {
        try {
            const response = await axios.post(API_ENDPOINTS.AUTH.LOGIN, {
                email,
                password
            });
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Login failed',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    async register(userData) {
        try {
            const response = await axios.post(API_ENDPOINTS.AUTH.REGISTER, userData);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Registration failed',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    async logout() {
        try {
            await axios.post(API_ENDPOINTS.AUTH.LOGOUT);
            return { success: true };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Logout failed' };
        }
    }

    async getMe() {
        try {
            const response = await axios.get(API_ENDPOINTS.AUTH.ME);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Failed to get user data' };
        }
    }

    async refreshToken() {
        try {
            const response = await axios.post(API_ENDPOINTS.AUTH.REFRESH);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Token refresh failed' };
        }
    }

    async changePassword(passwordData) {
        try {
            const response = await axios.post(API_ENDPOINTS.AUTH.CHANGE_PASSWORD, passwordData);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Password change failed',
                errors: error.response?.data?.errors || {}
            };
        }
    }
}

export default new AuthService();