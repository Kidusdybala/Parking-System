import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './contexts/AuthContext';
import { ParkingProvider } from './contexts/ParkingContext';
import HomePage from './pages/HomePage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import ForgotPasswordPage from './pages/ForgotPasswordPage';
import DashboardPage from './pages/DashboardPage';
import ParkingPage from './pages/ParkingPage';
import ReservationsPage from './pages/ReservationsPage';
import ProfilePage from './pages/ProfilePage';
import AdminPage from './pages/AdminPage';
import VerifyEmailPage from './pages/VerifyEmailPage';
import ReceiptPage from './pages/ReceiptPage';
import Layout from './components/Layout/Layout';
import LoadingSpinner from './components/Common/LoadingSpinner';
import ErrorBoundary from './components/Common/ErrorBoundary';

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
        <ErrorBoundary>
            <AuthProvider>
                <ParkingProvider>
                    <Router>
                <Routes>
                    {/* Public Routes */}
                    <Route 
                        path="/" 
                        element={
                            <PublicRoute>
                                <HomePage />
                            </PublicRoute>
                        } 
                    />
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
                    <Route 
                        path="/verify-email" 
                        element={
                            <PublicRoute>
                                <VerifyEmailPage />
                            </PublicRoute>
                        } 
                    />
                    <Route 
                        path="/forgot-password" 
                        element={
                            <PublicRoute>
                                <ForgotPasswordPage />
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
                    <Route 
                        path="/receipt" 
                        element={
                            <ProtectedRoute>
                                <Layout>
                                    <ReceiptPage />
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
                    
                    {/* Default redirect for unknown routes */}
                    <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>
                    </Router>
                </ParkingProvider>
            </AuthProvider>
        </ErrorBoundary>
    );
}

export default App;
