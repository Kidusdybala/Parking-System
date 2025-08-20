import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../contexts/AuthContext';

const Layout = ({ children }) => {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const { user, logout } = useAuth();
    const location = useLocation();

    const navigation = [
        { name: 'Dashboard', href: '/dashboard', icon: 'fas fa-tachometer-alt' },
        { name: 'Parking Spots', href: '/parking', icon: 'fas fa-parking' },
        { name: 'Reservations', href: '/reservations', icon: 'fas fa-calendar-check' },
        { name: 'Profile', href: '/profile', icon: 'fas fa-user' },
    ];

    const adminNavigation = [
        { name: 'Admin Dashboard', href: '/admin', icon: 'fas fa-cogs' },
    ];

    const handleLogout = async () => {
        await logout();
    };

    const isActive = (href) => location.pathname === href;

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Mobile sidebar */}
            <div className={`fixed inset-0 flex z-40 md:hidden ${sidebarOpen ? '' : 'hidden'}`}>
                <div className="fixed inset-0 bg-gray-600 bg-opacity-75" onClick={() => setSidebarOpen(false)}></div>
                <div className="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                    <div className="absolute top-0 right-0 -mr-12 pt-2">
                        <button
                            type="button"
                            className="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                            onClick={() => setSidebarOpen(false)}
                        >
                            <i className="fas fa-times h-6 w-6 text-white"></i>
                        </button>
                    </div>
                    <div className="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div className="flex-shrink-0 flex items-center px-4">
                            <h1 className="text-xl font-bold text-blue-600">MikiPark</h1>
                        </div>
                        <nav className="mt-5 px-2 space-y-1">
                            {navigation.map((item) => (
                                <Link
                                    key={item.name}
                                    to={item.href}
                                    className={`${
                                        isActive(item.href)
                                            ? 'bg-blue-100 text-blue-900'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                    } group flex items-center px-2 py-2 text-base font-medium rounded-md`}
                                >
                                    <i className={`${item.icon} mr-4 flex-shrink-0 h-6 w-6`}></i>
                                    {item.name}
                                </Link>
                            ))}
                            {user?.role === 3 && adminNavigation.map((item) => (
                                <Link
                                    key={item.name}
                                    to={item.href}
                                    className={`${
                                        isActive(item.href)
                                            ? 'bg-blue-100 text-blue-900'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                    } group flex items-center px-2 py-2 text-base font-medium rounded-md`}
                                >
                                    <i className={`${item.icon} mr-4 flex-shrink-0 h-6 w-6`}></i>
                                    {item.name}
                                </Link>
                            ))}
                        </nav>
                    </div>
                </div>
            </div>

            {/* Desktop sidebar */}
            <div className="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
                <div className="flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white">
                    <div className="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div className="flex items-center flex-shrink-0 px-4">
                            <h1 className="text-xl font-bold text-blue-600">MikiPark</h1>
                        </div>
                        <nav className="mt-5 flex-1 px-2 bg-white space-y-1">
                            {navigation.map((item) => (
                                <Link
                                    key={item.name}
                                    to={item.href}
                                    className={`${
                                        isActive(item.href)
                                            ? 'bg-blue-100 text-blue-900'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                    } group flex items-center px-2 py-2 text-sm font-medium rounded-md`}
                                >
                                    <i className={`${item.icon} mr-3 flex-shrink-0 h-6 w-6`}></i>
                                    {item.name}
                                </Link>
                            ))}
                            {user?.role === 3 && adminNavigation.map((item) => (
                                <Link
                                    key={item.name}
                                    to={item.href}
                                    className={`${
                                        isActive(item.href)
                                            ? 'bg-blue-100 text-blue-900'
                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                    } group flex items-center px-2 py-2 text-sm font-medium rounded-md`}
                                >
                                    <i className={`${item.icon} mr-3 flex-shrink-0 h-6 w-6`}></i>
                                    {item.name}
                                </Link>
                            ))}
                        </nav>
                    </div>
                </div>
            </div>

            {/* Main content */}
            <div className="md:pl-64 flex flex-col flex-1">
                <div className="sticky top-0 z-10 md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-gray-200">
                    <button
                        type="button"
                        className="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        onClick={() => setSidebarOpen(true)}
                    >
                        <i className="fas fa-bars h-6 w-6"></i>
                    </button>
                </div>

                {/* Top bar */}
                <div className="bg-white shadow">
                    <div className="px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between h-16">
                            <div className="flex items-center">
                                <h2 className="text-lg font-semibold text-gray-900">
                                    Welcome back, {user?.name}!
                                </h2>
                            </div>
                            <div className="flex items-center space-x-4">
                                <span className="text-sm text-gray-500">
                                    Balance: ${user?.balance || 0}
                                </span>
                                <button
                                    onClick={handleLogout}
                                    className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                                >
                                    <i className="fas fa-sign-out-alt mr-2"></i>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Page content */}
                <main className="flex-1">
                    <div className="py-6">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {children}
                        </div>
                    </div>
                </main>
            </div>
        </div>
    );
};

export default Layout;