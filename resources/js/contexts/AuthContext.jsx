import React, { createContext, useContext, useState, useEffect } from 'react';
import axios from 'axios';
import Cookies from 'js-cookie';

const AuthContext = createContext();

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [token, setToken] = useState(Cookies.get('auth_token'));

    // Configure axios defaults
    useEffect(() => {
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        } else {
            delete axios.defaults.headers.common['Authorization'];
        }
    }, [token]);

    // Check if user is authenticated on app load
    useEffect(() => {
        const checkAuth = async () => {
            const savedToken = Cookies.get('auth_token');
            if (savedToken) {
                try {
                    axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
                    const response = await axios.get('/api/auth/me');
                    if (response.data.success) {
                        setUser(response.data.user);
                        setToken(savedToken);
                    } else {
                        // Token is invalid
                        Cookies.remove('auth_token');
                        delete axios.defaults.headers.common['Authorization'];
                    }
                } catch (error) {
                    console.error('Auth check failed:', error);
                    Cookies.remove('auth_token');
                    delete axios.defaults.headers.common['Authorization'];
                }
            }
            setLoading(false);
        };

        checkAuth();
    }, []);

    const login = async (email, password) => {
        try {
            const response = await axios.post('/api/auth/login', {
                email,
                password
            });

            if (response.data.success) {
                const { user, token } = response.data;
                setUser(user);
                setToken(token);
                
                // Store token in cookie (expires in 7 days)
                Cookies.set('auth_token', token, { expires: 7 });
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                return { success: true };
            } else {
                return { success: false, message: response.data.message };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { 
                success: false, 
                message: error.response?.data?.message || 'Login failed' 
            };
        }
    };

    const register = async (name, email, password, password_confirmation) => {
        try {
            const response = await axios.post('/api/auth/register', {
                name,
                email,
                password,
                password_confirmation
            });

            if (response.data.success) {
                const { user, token } = response.data;
                setUser(user);
                setToken(token);
                
                // Store token in cookie (expires in 7 days)
                Cookies.set('auth_token', token, { expires: 7 });
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                
                return { success: true };
            } else {
                return { success: false, message: response.data.message };
            }
        } catch (error) {
            console.error('Registration error:', error);
            return { 
                success: false, 
                message: error.response?.data?.message || 'Registration failed',
                errors: error.response?.data?.errors || {}
            };
        }
    };

    const logout = async () => {
        try {
            if (token) {
                await axios.post('/api/auth/logout');
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            setUser(null);
            setToken(null);
            Cookies.remove('auth_token');
            delete axios.defaults.headers.common['Authorization'];
        }
    };

    const refreshToken = async () => {
        try {
            const response = await axios.post('/api/auth/refresh');
            if (response.data.success) {
                const newToken = response.data.token;
                setToken(newToken);
                Cookies.set('auth_token', newToken, { expires: 7 });
                axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
                return true;
            }
        } catch (error) {
            console.error('Token refresh failed:', error);
            logout();
            return false;
        }
    };

    const updateUser = (updatedUser) => {
        setUser(updatedUser);
    };

    const value = {
        user,
        token,
        loading,
        login,
        register,
        logout,
        refreshToken,
        updateUser
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};