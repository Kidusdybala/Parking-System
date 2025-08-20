import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import DashboardPage from './pages/DashboardPage';
import ParkingPage from './pages/ParkingPage';
import ReservationsPage from './pages/ReservationsPage';
import ProfilePage from './pages/ProfilePage';
import AdminPage from './pages/AdminPage';
import Layout from './components/Layout/Layout';
import LoadingSpinner from './components/Common/LoadingSpinner';

// Protected Route Component
const ProtectedRoute = ({ children, adminOnly = false }) => {
    const { user, loading } = useAuth();
    
    if (loading) {
        return <LoadingSpinner />;
    }
    
    if (!user) {
        return <Navigate to="/login" replace />;
    }
    
    if (adminOnly && user.role !== 3) {
        return <Navigate to="/dashboard" replace />;
    }
    
    return children;
};

// Public Route Component (redirect if authenticated)
const PublicRoute = ({ children }) => {
    const { user, loading } = useAuth();
    
    if (loading) {
        return <LoadingSpinner />;
    }
    
    if (user) {
        return <Navigate to="/dashboard" replace />;
    }
    
    return children;
};

function App() {
    return (
        <AuthProvider>
            <Router>
                <div className="min-h-screen bg-gray-50">
                    <Routes>
                        {/* Public Routes */}
                        <Route 
                            path="/login" 
                            element={
                                <PublicRoute>
                                    <LoginPage />
                                </PublicRoute>
                            } 
                        />
                        <Route 
                            path="/register" 
                            element={
                                <PublicRoute>
                                    <RegisterPage />
                                </PublicRoute>
                            } 
                        />
                        
                        {/* Protected Routes */}
                        <Route 
                            path="/dashboard" 
                            element={
                                <ProtectedRoute>
                                    <Layout>
                                        <DashboardPage />
                                    </Layout>
                                </ProtectedRoute>
                            } 
                        />
                        <Route 
                            path="/parking" 
                            element={
                                <ProtectedRoute>
                                    <Layout>
                                        <ParkingPage />
                                    </Layout>
                                </ProtectedRoute>
                            } 
                        />
                        <Route 
                            path="/reservations" 
                            element={
                                <ProtectedRoute>
                                    <Layout>
                                        <ReservationsPage />
                                    </Layout>
                                </ProtectedRoute>
                            } 
                        />
                        <Route 
                            path="/profile" 
                            element={
                                <ProtectedRoute>
                                    <Layout>
                                        <ProfilePage />
                                    </Layout>
                                </ProtectedRoute>
                            } 
                        />
                        
                        {/* Admin Routes */}
                        <Route 
                            path="/admin" 
                            element={
                                <ProtectedRoute adminOnly={true}>
                                    <Layout>
                                        <AdminPage />
                                    </Layout>
                                </ProtectedRoute>
                            } 
                        />
                        
                        {/* Default redirect */}
                        <Route path="/" element={<Navigate to="/dashboard" replace />} />
                        <Route path="*" element={<Navigate to="/dashboard" replace />} />
                    </Routes>
                </div>
            </Router>
        </AuthProvider>
    );
}

export default App;