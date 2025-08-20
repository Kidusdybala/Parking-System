// Auth testing utilities for debugging
import axios from 'axios';

export const testEmailConfiguration = async () => {
    try {
        console.log('Testing email configuration...');
        
        // Test sending a verification code
        const response = await axios.post('/api/auth/forgot-password', {
            email: 'test@example.com'
        });
        
        console.log('Email test response:', response.data);
        return response.data;
    } catch (error) {
        console.error('Email test failed:', error.response?.data || error.message);
        return { success: false, error: error.response?.data || error.message };
    }
};

export const testRegistration = async (userData = {
    name: 'Test User',
    email: 'test@example.com',
    password: 'password123',
    password_confirmation: 'password123'
}) => {
    try {
        console.log('Testing registration...');
        
        const response = await axios.post('/api/auth/register', userData);
        
        console.log('Registration test response:', response.data);
        return response.data;
    } catch (error) {
        console.error('Registration test failed:', error.response?.data || error.message);
        return { success: false, error: error.response?.data || error.message };
    }
};

export const testLogin = async (credentials = {
    email: 'test@example.com',
    password: 'password123'
}) => {
    try {
        console.log('Testing login...');
        
        const response = await axios.post('/api/auth/login', credentials);
        
        console.log('Login test response:', response.data);
        return response.data;
    } catch (error) {
        console.error('Login test failed:', error.response?.data || error.message);
        return { success: false, error: error.response?.data || error.message };
    }
};

export const testEmailVerification = async (email, code) => {
    try {
        console.log('Testing email verification...');
        
        const response = await axios.post('/api/verify-email', {
            email,
            code
        });
        
        console.log('Email verification test response:', response.data);
        return response.data;
    } catch (error) {
        console.error('Email verification test failed:', error.response?.data || error.message);
        return { success: false, error: error.response?.data || error.message };
    }
};

// Add these functions to window object for easy testing in browser console
if (typeof window !== 'undefined') {
    window.authTestHelpers = {
        testEmailConfiguration,
        testRegistration,
        testLogin,
        testEmailVerification
    };
}