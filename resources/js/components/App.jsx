import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from '../contexts/AuthContext';
import { useAuth } from '../contexts/AuthContext';

// Import components
import Login from './auth/Login';
import Register from './auth/Register';
import UserDashboard from './user/UserDashboard';
import AdminDashboard from './admin/AdminDashboard';
import LandingPage from './LandingPage';
import ProtectedRoute from './ProtectedRoute';
import TestComponent from './TestComponent';

function AppRoutes() {
    const { user, loading } = useAuth();

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-parkBlue-900">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary"></div>
                    <p className="mt-4 text-white">Loading...</p>
                </div>
            </div>
        );
    }

    return (
        <Routes>
            {/* Test Route */}
            <Route path="/test" element={<TestComponent />} />
            
            {/* Public Routes */}
            <Route path="/" element={<LandingPage />} />
            <Route 
                path="/login" 
                element={user ? <Navigate to="/dashboard" replace /> : <Login />} 
            />
            <Route 
                path="/register" 
                element={user ? <Navigate to="/dashboard" replace /> : <Register />} 
            />

            {/* Protected Routes */}
            <Route 
                path="/dashboard" 
                element={
                    <ProtectedRoute>
                        {user?.role === 'admin' ? <AdminDashboard /> : <UserDashboard />}
                    </ProtectedRoute>
                } 
            />

            {/* Catch all route */}
            <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
    );
}

function App() {
    return (
        <AuthProvider>
            <Router>
                <div className="min-h-screen bg-parkBlue-900 text-white">
                    <AppRoutes />
                </div>
            </Router>
        </AuthProvider>
    );
}

export default App;