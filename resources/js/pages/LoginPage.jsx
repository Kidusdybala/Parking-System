import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import Button from '../components/ui/Button';
import Input from '../components/ui/Input';
import Alert from '../components/ui/Alert';

const LoginPage = () => {
    const [formData, setFormData] = useState({
        email: '',
        password: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const { login } = useAuth();

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Clear error when user starts typing
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: ''
            }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        const result = await login(formData.email, formData.password);
        
        if (!result.success) {
            setErrors({ general: result.message });
        }
        
        setLoading(false);
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div className="max-w-md w-full space-y-8">
                <div>
                    <div className="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                        <i className="fas fa-parking text-blue-600 text-xl"></i>
                    </div>
                    <h2 className="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        Sign in to MikiPark
                    </h2>
                    <p className="mt-2 text-center text-sm text-gray-600">
                        Or{' '}
                        <Link
                            to="/register"
                            className="font-medium text-blue-600 hover:text-blue-500"
                        >
                            create a new account
                        </Link>
                    </p>
                </div>
                
                <form className="mt-8 space-y-6" onSubmit={handleSubmit}>
                    {errors.general && (
                        <Alert type="error">
                            {errors.general}
                        </Alert>
                    )}
                    
                    <div className="space-y-4">
                        <Input
                            label="Email Address"
                            name="email"
                            type="email"
                            autoComplete="email"
                            required
                            placeholder="Enter your email address"
                            value={formData.email}
                            onChange={handleChange}
                            icon="fas fa-envelope"
                        />

                        <Input
                            label="Password"
                            name="password"
                            type="password"
                            autoComplete="current-password"
                            required
                            placeholder="Enter your password"
                            value={formData.password}
                            onChange={handleChange}
                            icon="fas fa-lock"
                        />
                    </div>

                    <div>
                        <Button
                            type="submit"
                            variant="primary"
                            size="large"
                            loading={loading}
                            className="w-full"
                            icon="fas fa-sign-in-alt"
                        >
                            {loading ? 'Signing in...' : 'Sign in'}
                        </Button>
                    </div>

                    <div className="text-center">
                        <Link
                            to="/forgot-password"
                            className="text-sm text-blue-600 hover:text-blue-500"
                        >
                            Forgot your password?
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default LoginPage;