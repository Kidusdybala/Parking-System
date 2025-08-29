
import { Link } from 'react-router-dom';

const HomePage = () => {
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
                        <Link to="/" className="nav-link active">Home</Link>
                        <Link to="/login" className="nav-link">Login</Link>
                        <Link to="/register" className="nav-link">Register</Link>
                    </nav>

                    {/* Mobile Navigation Button */}
                    <button className="md:hidden p-2 rounded-md hover:bg-accent">
                        <i className="fas fa-bars"></i>
                    </button>
                </div>
            </header>

            {/* Hero Section */}
            <main className="flex-1 flex items-center justify-center py-12 px-4">
                <div className="text-center max-w-4xl mx-auto">
                    <div className="glass-card p-12 relative overflow-hidden">
                        <div className="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                        <div className="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

                        <div className="relative z-10">
                            <div className="mb-8">
                                <i className="fas fa-parking text-primary text-6xl mb-6"></i>
                                <h1 className="text-4xl md:text-5xl font-bold mb-4 text-foreground">
                                    Welcome to <span className="text-primary">MikiPark</span>
                                </h1>
                                <p className="text-xl text-muted-foreground mb-8">
                                    Smart parking management system for the modern world.
                                    Find, reserve, and manage your parking spots with ease.
                                </p>
                            </div>

                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <Link 
                                    to="/register" 
                                    className="btn btn-primary px-8 py-3 text-lg"
                                >
                                    Get Started
                                    <i className="fas fa-arrow-right ml-2"></i>
                                </Link>
                                <Link 
                                    to="/login" 
                                    className="btn btn-outline px-8 py-3 text-lg"
                                >
                                    Sign In
                                    <i className="fas fa-sign-in-alt ml-2"></i>
                                </Link>
                            </div>

                            <div className="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div className="text-center">
                                    <div className="h-16 w-16 bg-primary/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <span className="text-primary text-2xl font-bold">P</span>
                                    </div>
                                    <h3 className="font-bold mb-2 text-foreground">Find Parking</h3>
                                    <p className="text-muted-foreground text-sm">
                                        Easily locate available parking spots in real-time
                                    </p>
                                </div>
                                <div className="text-center">
                                    <div className="h-16 w-16 bg-primary/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <span className="text-primary text-2xl font-bold">R</span>
                                    </div>
                                    <h3 className="font-bold mb-2 text-foreground">Reserve Spots</h3>
                                    <p className="text-muted-foreground text-sm">
                                        Book your parking spot in advance and save time
                                    </p>
                                </div>
                                <div className="text-center">
                                    <div className="h-16 w-16 bg-primary/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                                        <span className="text-primary text-2xl font-bold">S</span>
                                    </div>
                                    <h3 className="font-bold mb-2 text-foreground">Smart Management</h3>
                                    <p className="text-muted-foreground text-sm">
                                        Manage all your parking activities from one place
                                    </p>
                                </div>
                            </div>
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

export default HomePage;
