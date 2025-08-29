import { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';
import ChatWidget from '../Chat/ChatWidget';

const Layout = ({ children }) => {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [userDropdownOpen, setUserDropdownOpen] = useState(false);
    const { user, logout } = useAuth();
    const location = useLocation();

    const handleLogout = async () => {
        await logout();
    };

    const toggleMobileMenu = () => {
        setMobileMenuOpen(!mobileMenuOpen);
    };

    return (
        <div className="min-h-screen flex flex-col bg-parkBlue-900">
            {/* Noise Background */}
            <div className="fixed inset-0 noise-bg pointer-events-none"></div>
            
            {/* Header/Navigation */}
            <header className="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
                <div className="container flex items-center justify-between h-16">
                    <div className="flex items-center gap-2">
                        <Link to="/dashboard">
                            <i className="fas fa-parking text-primary text-2xl"></i>
                            <span className="font-bold text-xl">Miki<span className="text-primary">Park</span></span>
                        </Link>
                    </div>
                    
                    {/* Desktop Navigation */}
                    <div className="hidden md:flex items-center space-x-4">
                        <span className="text-muted-foreground">
                            Welcome, <span className="text-foreground">{user?.name || 'User'}</span>
                        </span>
                        <div className="relative group">
                            <button 
                                className="h-8 w-8 rounded-full bg-parkBlue-700 flex items-center justify-center hover:bg-primary/80 transition-colors"
                                onClick={() => setUserDropdownOpen(!userDropdownOpen)}
                            >
                                <i className="fas fa-user"></i>
                            </button>
                            <div className={`absolute right-0 mt-2 w-48 py-2 bg-card rounded-md shadow-lg transition-all z-50 ${
                                userDropdownOpen ? 'opacity-100 visible' : 'opacity-0 invisible'
                            }`}>
                                {user?.role === 3 && (
                                    <Link 
                                        to="/admin" 
                                        className="block px-4 py-2 hover:bg-accent text-sm text-primary"
                                        onClick={() => setUserDropdownOpen(false)}
                                    >
                                        <i className="fas fa-shield-alt mr-2"></i> Admin Panel
                                    </Link>
                                )}
                                <Link 
                                    to="/profile" 
                                    className="block px-4 py-2 hover:bg-accent text-sm"
                                    onClick={() => setUserDropdownOpen(false)}
                                >
                                    <i className="fas fa-user-circle mr-2"></i> Profile
                                </Link>
                                <Link 
                                    to="/profile" 
                                    className="block px-4 py-2 hover:bg-accent text-sm"
                                    onClick={() => setUserDropdownOpen(false)}
                                >
                                    <i className="fas fa-cog mr-2"></i> Settings
                                </Link>
                                <div className="border-t border-white/10 my-1"></div>
                                <button 
                                    onClick={handleLogout}
                                    className="block w-full text-left px-4 py-2 hover:bg-accent text-sm text-parkRed"
                                >
                                    <i className="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {/* Mobile Menu Button */}
                    <button 
                        className="md:hidden p-2 rounded-md hover:bg-accent" 
                        onClick={toggleMobileMenu}
                    >
                        <i className="fas fa-bars"></i>
                    </button>
                </div>
                
                {/* Mobile Navigation Menu */}
                <div className={`md:hidden ${mobileMenuOpen ? '' : 'hidden'}`}>
                    <div className="container py-2 space-y-1 border-t border-white/10">
                        <div className="flex items-center justify-between py-2">
                            <span className="text-muted-foreground">
                                Welcome, <span className="text-foreground">{user?.name || 'User'}</span>
                            </span>
                        </div>
                        <Link 
                            to="/profile" 
                            className="nav-link block"
                            onClick={() => setMobileMenuOpen(false)}
                        >
                            <i className="fas fa-user-circle mr-2"></i> Profile
                        </Link>
                        <Link 
                            to="/profile" 
                            className="nav-link block"
                            onClick={() => setMobileMenuOpen(false)}
                        >
                            <i className="fas fa-cog mr-2"></i> Settings
                        </Link>
                        <button 
                            onClick={handleLogout}
                            className="nav-link block text-parkRed w-full text-left"
                        >
                            <i className="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <main className="flex-1 min-h-0">
                <div className="h-full container py-6">
                    {children}
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

            {/* Chat Widget */}
            <ChatWidget />
        </div>
    );
};

export default Layout;
