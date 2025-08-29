import { useState } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';

const RegisterPage = () => {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const { register } = useAuth();
    const [info, setInfo] = useState('');
    const navigateToVerify = (email) => {
        // Redirect to verify page with email param
        window.location.href = `/verify-email?email=${encodeURIComponent(email)}`;
    };

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

        const result = await register(
            formData.name,
            formData.email,
            formData.password,
            formData.password_confirmation
        );
        
        if (!result.success) {
            setErrors(result.errors || { general: result.message });
        } else if (result.needsVerification) {
            setInfo('A verification code has been sent to your email. Please verify your email to continue.');
            navigateToVerify(formData.email);
        }
        
        setLoading(false);
    };

    const togglePassword = () => {
        setShowPassword(!showPassword);
    };

    const toggleConfirmPassword = () => {
        setShowConfirmPassword(!showConfirmPassword);
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
                        <Link to="/register" className="nav-link active">Register</Link>
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
                            <h1 className="text-2xl font-bold">Create Your Account</h1>
                            <p className="text-muted-foreground mt-2">Join MikiPark and start parking smarter</p>
                        </div>

                        {/* Error / Info Display */}
                        {errors.general && (
                            <div className="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-md">
                                <p className="text-red-400 text-sm">{errors.general}</p>
                            </div>
                        )}
                        {info && (
                            <div className="mb-4 p-3 bg-blue-500/10 border border-blue-500/20 rounded-md">
                                <p className="text-blue-400 text-sm">{info}</p>
                            </div>
                        )}

                        {/* Register Form */}
                        <form onSubmit={handleSubmit}>
                            <div className="space-y-4">
                                <div>
                                    <label htmlFor="name" className="block text-sm font-medium mb-1">Full Name</label>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name"
                                        className="input w-full" 
                                        placeholder="Enter your full name" 
                                        value={formData.name}
                                        onChange={handleChange}
                                        required 
                                        autoFocus 
                                        autoComplete="name"
                                    />
                                    {errors.name && (
                                        <p className="text-red-500 text-xs mt-1">{Array.isArray(errors.name) ? errors.name[0] : errors.name}</p>
                                    )}
                                </div>

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
                                        autoComplete="email"
                                    />
                                    {errors.email && (
                                        <p className="text-red-500 text-xs mt-1">{Array.isArray(errors.email) ? errors.email[0] : errors.email}</p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="password" className="block text-sm font-medium mb-1">Password</label>
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
                                            autoComplete="new-password"
                                        />
                                        <button 
                                            type="button" 
                                            className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"
                                            onClick={togglePassword}
                                        >
                                            <i className={showPassword ? "far fa-eye-slash" : "far fa-eye"}></i>
                                        </button>
                                    </div>
                                    {errors.password && (
                                        <p className="text-red-500 text-xs mt-1">{Array.isArray(errors.password) ? errors.password[0] : errors.password}</p>
                                    )}
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
                                            onClick={toggleConfirmPassword}
                                        >
                                            <i className={showConfirmPassword ? "far fa-eye-slash" : "far fa-eye"}></i>
                                        </button>
                                    </div>
                                    {errors.password_confirmation && (
                                        <p className="text-red-500 text-xs mt-1">{Array.isArray(errors.password_confirmation) ? errors.password_confirmation[0] : errors.password_confirmation}</p>
                                    )}
                                </div>

                                <button 
                                    type="submit" 
                                    className="btn btn-primary w-full"
                                    disabled={loading}
                                >
                                    {loading ? 'Creating Account...' : 'Create Account'}
                                    <i className="fas fa-user-plus ml-2"></i>
                                </button>
                            </div>
                        </form>

                        <div className="mt-6 text-center">
                            <p className="text-muted-foreground">
                                Already have an account?
                                <Link to="/login" className="text-primary hover:underline ml-1">Sign in</Link>
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

export default RegisterPage;
