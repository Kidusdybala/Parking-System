import { createContext, useContext, useState, useEffect } from 'react';
import axios from '../services/api';
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
                    setToken(savedToken);
                    axios.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`;
                    const response = await axios.get('/api/auth/me');
                    if (response.data.success && response.data.user) {
                        // Ensure user object has all required properties
                        const userData = {
                            id: response.data.user.id || 0,
                            name: response.data.user.name || '',
                            email: response.data.user.email || '',
                            role: response.data.user.role || 1,
                            balance: response.data.user.balance || 0,
                            ...response.data.user
                        };
                        setUser(userData);
                    } else {
                        // Token is invalid
                        setUser(null);
                        setToken(null);
                        Cookies.remove('auth_token');
                        delete axios.defaults.headers.common['Authorization'];
                    }
                } catch (error) {
                    console.error('Auth check failed:', error);
                    // Don't clear token here - let the interceptor handle it
                    setUser(null);
                    setLoading(false);
                    return;
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
                return { 
                    success: false, 
                    message: response.data.message, 
                    requiresVerification: response.data.requires_verification, 
                    email: response.data.email 
                };
            }
        } catch (error) {
            console.error('Login error:', error);
            return { 
                success: false, 
                message: error.response?.data?.message || 'Login failed',
                requiresVerification: error.response?.data?.requires_verification,
                email: error.response?.data?.email
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
                // After register, require email verification; don't auto-login
                setUser(null);
                setToken(null);
                Cookies.remove('auth_token');
                return { success: true, needsVerification: true, email };
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

    // Token refresh is now handled by axios interceptor in bootstrap.js
    const refreshToken = async () => {
        console.warn('refreshToken is deprecated - handled by axios interceptor');
        return false;
    };

    const updateUser = (updatedUser) => {
        if (updatedUser && typeof updatedUser === 'object') {
            // Ensure user object has all required properties
            const userData = {
                id: updatedUser.id || 0,
                name: updatedUser.name || '',
                email: updatedUser.email || '',
                role: updatedUser.role || 1,
                balance: updatedUser.balance || 0,
                ...updatedUser
            };
            setUser(userData);
        } else {
            console.warn('updateUser called with invalid data:', updatedUser);
        }
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
