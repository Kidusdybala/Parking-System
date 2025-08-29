import { useState } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';

const ForgotPasswordPage = () => {
    const [step, setStep] = useState('email'); // 'email', 'code', 'password'
    const [formData, setFormData] = useState({
        email: '',
        code: '',
        password: '',
        password_confirmation: ''
    });
    const [loading, setLoading] = useState(false);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        setError('');
    };

    const handleEmailSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setMessage('');

        try {
            const response = await axios.post('/api/auth/forgot-password', {
                email: formData.email
            });

            if (response.data.success) {
                setMessage(response.data.message);
                setStep('code');
            } else {
                setError(response.data.message || 'Failed to send reset code');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to send reset code');
        } finally {
            setLoading(false);
        }
    };

    const handleCodeSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setMessage('');

        try {
            const response = await axios.post('/api/auth/verify-reset-code', {
                email: formData.email,
                code: formData.code
            });

            if (response.data.success) {
                setMessage(response.data.message);
                setStep('password');
            } else {
                setError(response.data.message || 'Invalid reset code');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Invalid reset code');
        } finally {
            setLoading(false);
        }
    };

    const handlePasswordSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setMessage('');

        try {
            const response = await axios.post('/api/auth/reset-password', {
                email: formData.email,
                code: formData.code,
                password: formData.password,
                password_confirmation: formData.password_confirmation
            });

            if (response.data.success) {
                setMessage(response.data.message);
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            } else {
                setError(response.data.message || 'Failed to reset password');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to reset password');
        } finally {
            setLoading(false);
        }
    };

    const handleResendCode = async () => {
        setLoading(true);
        setError('');
        setMessage('');

        try {
            const response = await axios.post('/api/auth/forgot-password', {
                email: formData.email
            });

            if (response.data.success) {
                setMessage('New reset code sent to your email');
            } else {
                setError(response.data.message || 'Failed to resend code');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to resend code');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen flex flex-col">
            {/* Noise Background */}
            <div className="fixed inset-0 noise-bg pointer-events-none"></div>

            {/* Header/Navigation */}
            <header className="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
                <div className="container flex items-center justify-between h-16">
                    <div className="flex items-center gap-2">
                        <Link to="/">
                            <i className="fas fa-parking text-primary text-2xl"></i>
                            <span className="font-bold text-xl">Miki<span className="text-primary">Park</span></span>
                        </Link>
                    </div>

                    {/* Desktop Navigation */}
                    <nav className="hidden md:flex items-center space-x-1">
                        <Link to="/" className="nav-link">Home</Link>
                        <Link to="/login" className="nav-link">Login</Link>
                        <Link to="/register" className="nav-link">Register</Link>
                    </nav>

                    {/* Mobile Navigation Button */}
                    <button className="md:hidden p-2 rounded-md hover:bg-accent">
                        <i className="fas fa-bars"></i>
                    </button>
                </div>
            </header>

            <main className="flex-1 flex items-center justify-center py-12 px-4">
                <div className="glass-card w-full max-w-md p-8 relative overflow-hidden">
                    <div className="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                    <div className="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

                    <div className="relative z-10">
                        <div className="text-center mb-8">
                            <h1 className="text-2xl font-bold">
                                {step === 'email' && 'Reset Your Password'}
                                {step === 'code' && 'Enter Reset Code'}
                                {step === 'password' && 'Set New Password'}
                            </h1>
                            <p className="text-muted-foreground mt-2">
                                {step === 'email' && 'Enter your email to receive a reset code'}
                                {step === 'code' && 'Enter the 6-digit code sent to your email'}
                                {step === 'password' && 'Create a new password for your account'}
                            </p>
                        </div>

                        {/* Progress Steps */}
                        <div className="flex justify-center mb-8">
                            <div className="flex items-center space-x-4">
                                <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
                                    step === 'email' ? 'bg-primary text-white' : 'bg-primary/20 text-primary'
                                }`}>
                                    <i className="fas fa-envelope text-sm"></i>
                                </div>
                                <div className={`w-12 h-0.5 ${step === 'code' || step === 'password' ? 'bg-primary' : 'bg-gray-300'}`}></div>
                                <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
                                    step === 'code' ? 'bg-primary text-white' : 
                                    step === 'password' ? 'bg-primary/20 text-primary' : 'bg-gray-300 text-gray-500'
                                }`}>
                                    <i className="fas fa-key text-sm"></i>
                                </div>
                                <div className={`w-12 h-0.5 ${step === 'password' ? 'bg-primary' : 'bg-gray-300'}`}></div>
                                <div className={`w-8 h-8 rounded-full flex items-center justify-center ${
                                    step === 'password' ? 'bg-primary text-white' : 'bg-gray-300 text-gray-500'
                                }`}>
                                    <i className="fas fa-lock text-sm"></i>
                                </div>
                            </div>
                        </div>

                        {/* Messages */}
                        {error && (
                            <div className="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-md">
                                <p className="text-red-400 text-sm">{error}</p>
                            </div>
                        )}
                        {message && (
                            <div className="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-md">
                                <p className="text-green-400 text-sm">{message}</p>
                            </div>
                        )}

                        {/* Step 1: Email */}
                        {step === 'email' && (
                            <form onSubmit={handleEmailSubmit}>
                                <div className="space-y-4">
                                    <div>
                                        <label htmlFor="email" className="block text-sm font-medium mb-1">Email Address</label>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email"
                                            className="input w-full" 
                                            placeholder="user@gmail.com" 
                                            value={formData.email}
                                            onChange={handleChange}
                                            required 
                                            autoFocus 
                                            autoComplete="email"
                                        />
                                    </div>
                                    <button 
                                        type="submit" 
                                        className="btn btn-primary w-full"
                                        disabled={loading}
                                    >
                                        {loading ? 'Sending...' : 'Send Reset Code'}
                                        <i className="fas fa-paper-plane ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        )}

                        {/* Step 2: Code */}
                        {step === 'code' && (
                            <form onSubmit={handleCodeSubmit}>
                                <div className="space-y-4">
                                    <div>
                                        <label htmlFor="code" className="block text-sm font-medium mb-1">Reset Code</label>
                                        <input 
                                            type="text" 
                                            id="code" 
                                            name="code"
                                            className="input w-full tracking-widest text-center" 
                                            placeholder="123456" 
                                            value={formData.code}
                                            onChange={(e) => setFormData(prev => ({
                                                ...prev,
                                                code: e.target.value.replace(/[^0-9]/g, '').slice(0, 6)
                                            }))}
                                            required 
                                            inputMode="numeric"
                                            autoFocus
                                        />
                                    </div>
                                    <button 
                                        type="submit" 
                                        className="btn btn-primary w-full"
                                        disabled={loading}
                                    >
                                        {loading ? 'Verifying...' : 'Verify Code'}
                                        <i className="fas fa-check ml-2"></i>
                                    </button>
                                    <button 
                                        type="button" 
                                        className="btn btn-outline w-full"
                                        onClick={handleResendCode}
                                        disabled={loading}
                                    >
                                        Resend Code
                                    </button>
                                </div>
                            </form>
                        )}

                        {/* Step 3: New Password */}
                        {step === 'password' && (
                            <form onSubmit={handlePasswordSubmit}>
                                <div className="space-y-4">
                                    <div>
                                        <label htmlFor="password" className="block text-sm font-medium mb-1">New Password</label>
                                        <div className="relative">
                                            <input 
                                                type={showPassword ? "text" : "password"} 
                                                id="password" 
                                                name="password"
                                                className="input w-full pr-10" 
                                                placeholder="••••••••" 
                                                value={formData.password}
                                                onChange={handleChange}
                                                required 
                                                autoFocus
                                                autoComplete="new-password"
                                            />
                                            <button 
                                                type="button" 
                                                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                                                onClick={() => setShowPassword(!showPassword)}
                                            >
                                                <i className={showPassword ? "far fa-eye-slash" : "far fa-eye"}></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label htmlFor="password_confirmation" className="block text-sm font-medium mb-1">Confirm Password</label>
                                        <div className="relative">
                                            <input 
                                                type={showConfirmPassword ? "text" : "password"} 
                                                id="password_confirmation" 
                                                name="password_confirmation"
                                                className="input w-full pr-10" 
                                                placeholder="••••••••" 
                                                value={formData.password_confirmation}
                                                onChange={handleChange}
                                                required 
                                                autoComplete="new-password"
                                            />
                                            <button 
                                                type="button" 
                                                className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                                                onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                            >
                                                <i className={showConfirmPassword ? "far fa-eye-slash" : "far fa-eye"}></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button 
                                        type="submit" 
                                        className="btn btn-primary w-full"
                                        disabled={loading}
                                    >
                                        {loading ? 'Resetting...' : 'Reset Password'}
                                        <i className="fas fa-save ml-2"></i>
                                    </button>
                                </div>
                            </form>
                        )}

                        <div className="mt-6 text-center">
                            <p className="text-muted-foreground">
                                Remember your password?
                                <Link to="/login" className="text-primary hover:underline ml-1">Back to Login</Link>
                            </p>
                        </div>
                    </div>
                </div>
            </main>

            {/* Footer */}
            <footer className="border-t border-white/10 py-6">
                <div className="container">
                    <div className="flex flex-col md:flex-row justify-between items-center">
                        <div className="flex items-center gap-2 mb-4 md:mb-0">
                            <i className="fas fa-parking text-primary text-xl"></i>
                            <span className="font-bold">Smart<span className="text-primary">Park</span></span>
                        </div>

                        <p className="text-muted-foreground text-sm">
                            © {new Date().getFullYear()} SmartPark. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default ForgotPasswordPage;