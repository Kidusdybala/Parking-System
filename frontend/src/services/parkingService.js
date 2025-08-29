import axios from './api';
import { API_ENDPOINTS } from '../utils/constants';

class ParkingService {
    async getAllSpots() {
        try {
            const response = await axios.get(API_ENDPOINTS.PARKING.LIST);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Failed to fetch parking spots' };
        }
    }

    async getSpotById(id) {
        try {
            const response = await axios.get(API_ENDPOINTS.PARKING.SHOW(id));
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Failed to fetch parking spot' };
        }
    }

    async getAvailableSpots() {
        try {
            const response = await axios.get(API_ENDPOINTS.PARKING.AVAILABLE);
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Failed to fetch available spots' };
        }
    }

    async createSpot(spotData) {
        try {
            const response = await axios.post(API_ENDPOINTS.PARKING.CREATE, spotData);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Failed to create parking spot',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    async updateSpot(id, spotData) {
        try {
            const response = await axios.put(API_ENDPOINTS.PARKING.UPDATE(id), spotData);
            return { success: true, data: response.data };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Failed to update parking spot',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    async deleteSpot(id) {
        try {
            const response = await axios.delete(API_ENDPOINTS.PARKING.DELETE(id));
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Failed to delete parking spot' };
        }
    }

    async getRecommendedSpot(userId) {
        try {
            const response = await axios.get(API_ENDPOINTS.PARKING.RECOMMEND(userId));
            return { success: true, data: response.data };
        } catch (error) {
            return { success: false, message: error.response?.data?.message || 'Failed to get recommended spot' };
        }
    }
}

export default new ParkingService();