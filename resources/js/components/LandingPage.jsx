import React from 'react';
import { Link } from 'react-router-dom';

const LandingPage = () => {
    return (
        <div className="min-h-screen flex flex-col">
            {/* Noise Background */}
            <div className="fixed inset-0 noise-bg pointer-events-none"></div>

            {/* Header/Navigation */}
            <header className="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
                <div className="container mx-auto px-4 flex items-center justify-between h-16">
                    <div className="flex items-center gap-2">
                        <i className="fas fa-parking text-primary text-2xl"></i>
                        <span className="font-bold text-xl">Miki<span className="text-primary">Park</span></span>
                    </div>

                    {/* Desktop Navigation */}
                    <nav className="hidden md:flex items-center space-x-1">
                        <Link to="/login" className="nav-link px-4 py-2 rounded-md hover:bg-accent transition-colors">
                            Login
                        </Link>
                        <Link to="/register" className="nav-link px-4 py-2 rounded-md hover:bg-accent transition-colors">
                            Register
                        </Link>
                    </nav>

                    {/* Mobile Navigation Button */}
                    <button className="md:hidden p-2 rounded-md hover:bg-accent" id="mobile-menu-button">
                        <i className="fas fa-bars"></i>
                    </button>
                </div>
            </header>

            <main className="flex-1">
                {/* Hero Section */}
                <section className="relative py-20 overflow-hidden">
                    <div className="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div className="relative w-full max-w-xl">
                            <h1 className="text-4xl md:text-5xl font-bold mb-4">
                                Modern Parking <span className="text-primary">Simplified</span>
                            </h1>
                            <p className="text-muted-foreground text-lg mb-8">
                                Find and reserve parking spots in real-time. Save time, reduce stress, and never worry about parking again.
                            </p>
                            <div className="flex flex-wrap gap-4">
                                <Link to="/register" className="btn btn-primary inline-flex items-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                                    Get Started
                                    <i className="fas fa-arrow-right ml-2"></i>
                                </Link>
                                <a href="#how-it-works" className="btn btn-outline inline-flex items-center px-6 py-3 border border-white/20 rounded-lg hover:bg-white/10 transition-colors">
                                    How It Works
                                </a>
                            </div>
                        </div>
                        <div className="relative w-full max-w-2xl">
                            <div className="rounded-xl relative z-10">
                                <img src="/frontend/assets/images/inside-parking.jpg" alt="Smart Parking Illustration" className="rounded-lg w-full h-auto shadow-lg" />
                            </div>
                            <div className="absolute -bottom-6 -right-6 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
                            <div className="absolute -top-6 -left-6 w-32 h-32 bg-parkBlue-500/20 rounded-full blur-3xl"></div>
                        </div>
                    </div>

                    {/* Stats Section */}
                    <div className="container mx-auto px-4 mt-20">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="glass-card flex flex-col items-center p-6 text-center bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                <div className="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center mb-4">
                                    <i className="fas fa-clock text-primary"></i>
                                </div>
                                <h3 className="text-2xl font-bold mb-2">60%</h3>
                                <p className="text-muted-foreground">Less time spent finding parking</p>
                            </div>

                            <div className="glass-card flex flex-col items-center p-6 text-center bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                <div className="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center mb-4">
                                    <i className="fas fa-car text-primary"></i>
                                </div>
                                <h3 className="text-2xl font-bold mb-2">500+</h3>
                                <p className="text-muted-foreground">Available parking spots</p>
                            </div>

                            <div className="glass-card flex flex-col items-center p-6 text-center bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                <div className="w-12 h-12 bg-primary/20 rounded-full flex items-center justify-center mb-4">
                                    <i className="fas fa-smile text-primary"></i>
                                </div>
                                <h3 className="text-2xl font-bold mb-2">98%</h3>
                                <p className="text-muted-foreground">Customer satisfaction</p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* How It Works Section */}
                <section id="how-it-works" className="py-20 bg-gradient-to-b from-parkBlue-900 to-parkBlue-800">
                    <div className="container mx-auto px-4">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl md:text-4xl font-bold mb-4">How It Works</h2>
                            <p className="text-muted-foreground max-w-2xl mx-auto">
                                Our smart parking system makes finding and reserving parking spots simple and efficient
                            </p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div className="card-container relative bg-white/5 backdrop-blur-sm rounded-lg border border-white/10 p-6">
                                <div className="w-10 h-10 rounded-full bg-primary flex items-center justify-center absolute -top-5 -left-5">
                                    <span className="font-bold">1</span>
                                </div>
                                <div className="mb-4 text-center">
                                    <i className="fas fa-search text-4xl text-primary mb-4"></i>
                                    <h3 className="text-xl font-bold mb-2">Find a Spot</h3>
                                    <p className="text-muted-foreground">
                                        Browse available parking spots in real-time on our interactive map
                                    </p>
                                </div>
                            </div>

                            <div className="card-container relative bg-white/5 backdrop-blur-sm rounded-lg border border-white/10 p-6">
                                <div className="w-10 h-10 rounded-full bg-primary flex items-center justify-center absolute -top-5 -left-5">
                                    <span className="font-bold">2</span>
                                </div>
                                <div className="mb-4 text-center">
                                    <i className="fas fa-calendar-check text-4xl text-primary mb-4"></i>
                                    <h3 className="text-xl font-bold mb-2">Make a Reservation</h3>
                                    <p className="text-muted-foreground">
                                        Reserve your spot for your desired time and duration
                                    </p>
                                </div>
                            </div>

                            <div className="card-container relative bg-white/5 backdrop-blur-sm rounded-lg border border-white/10 p-6">
                                <div className="w-10 h-10 rounded-full bg-primary flex items-center justify-center absolute -top-5 -left-5">
                                    <span className="font-bold">3</span>
                                </div>
                                <div className="mb-4 text-center">
                                    <i className="fas fa-car-side text-4xl text-primary mb-4"></i>
                                    <h3 className="text-xl font-bold mb-2">Park with Ease</h3>
                                    <p className="text-muted-foreground">
                                        Arrive at your reserved spot and park without the hassle
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Pricing Section */}
                <section id="pricing" className="py-20 bg-gradient-to-b from-parkBlue-800 to-parkBlue-900">
                    <div className="container mx-auto px-4">
                        <div className="text-center mb-16">
                            <h2 className="text-3xl md:text-4xl font-bold mb-4">Simple Pricing</h2>
                            <p className="text-muted-foreground max-w-2xl mx-auto">
                                Transparent pay-as-you-go pricing with no hidden fees
                            </p>
                        </div>

                        <div className="max-w-4xl mx-auto">
                            <div className="glass-card p-8 text-center bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                                <div className="inline-block bg-primary/20 rounded-full px-4 py-1 text-primary font-medium mb-4">
                                    Hourly Rate
                                </div>
                                <div className="flex items-center justify-center mb-6">
                                    <span className="text-4xl font-bold">30 Birr</span>
                                    <span className="text-muted-foreground ml-2">/ hour</span>
                                </div>
                                <ul className="space-y-3 text-left mb-8 max-w-md mx-auto">
                                    <li className="flex items-center">
                                        <i className="fas fa-check text-green-500 mr-2"></i>
                                        <span>Real-time availability</span>
                                    </li>
                                    <li className="flex items-center">
                                        <i className="fas fa-check text-green-500 mr-2"></i>
                                        <span>Free cancellation up to 30 minutes before</span>
                                    </li>
                                    <li className="flex items-center">
                                        <i className="fas fa-check text-green-500 mr-2"></i>
                                        <span>Secure payment system</span>
                                    </li>
                                    <li className="flex items-center">
                                        <i className="fas fa-check text-green-500 mr-2"></i>
                                        <span>24/7 customer support</span>
                                    </li>
                                </ul>
                                <Link to="/login" className="btn btn-primary px-8 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                                    Get Started
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Call to Action */}
                <section className="py-20">
                    <div className="container mx-auto px-4">
                        <div className="glass-card relative overflow-hidden p-8 md:p-12 bg-white/5 backdrop-blur-sm rounded-lg border border-white/10">
                            <div className="relative z-10 max-w-2xl">
                                <h2 className="text-3xl md:text-4xl font-bold mb-4">Ready to simplify your parking?</h2>
                                <p className="text-muted-foreground mb-8">
                                    Join thousands of drivers who have made parking stress-free with our smart system.
                                </p>
                                <div className="flex flex-wrap gap-4">
                                    <Link to="/register" className="btn btn-primary px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                                        Create Account
                                    </Link>
                                    <Link to="/login" className="btn btn-outline px-6 py-3 border border-white/20 rounded-lg hover:bg-white/10 transition-colors">
                                        Sign In
                                    </Link>
                                </div>
                            </div>
                            <div className="absolute -bottom-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                            <div className="absolute -top-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>
                        </div>
                    </div>
                </section>
            </main>

            {/* Footer */}
            <footer className="border-t border-white/10 py-12 mt-auto">
                <div className="container mx-auto px-4">
                    <div className="text-center">
                        <div className="flex items-center justify-center gap-2 mb-4">
                            <i className="fas fa-parking text-primary text-2xl"></i>
                            <span className="font-bold text-xl">Miki<span className="text-primary">Park</span></span>
                        </div>
                        <p className="text-muted-foreground">
                            © 2024 MikiPark. All rights reserved.
                        </p>
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default LandingPage;