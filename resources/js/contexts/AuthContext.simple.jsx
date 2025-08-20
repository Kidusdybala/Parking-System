import { createContext, useContext, useState } from 'react';

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
    const [loading, setLoading] = useState(false);
    const [token, setToken] = useState(null);

    const login = async (email, password) => {
        // Simplified login for testing
        setUser({ id: 1, name: 'Test User', email, role: 1 });
        return { success: true };
    };

    const logout = () => {
        setUser(null);
        setToken(null);
    };

    const register = async (userData) => {
        return { success: true };
    };

    const value = {
        user,
        token,
        loading,
        login,
        register,
        logout,
        refreshToken: () => {},
        updateUser: (user) => setUser(user)
    };

    return (
        <AuthContext.Provider value={value}>
            {children}
        </AuthContext.Provider>
    );
};