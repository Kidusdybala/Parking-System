import { useLocation, useNavigate, Link } from 'react-router-dom';
import { useEffect, useState } from 'react';

const ReceiptPage = () => {
    const location = useLocation();
    const navigate = useNavigate();
    const [session, setSession] = useState(null);

    useEffect(() => {
        if (location.state?.session) {
            setSession(location.state.session);
        } else {
            // If no session data, redirect back to dashboard
            navigate('/dashboard');
        }
    }, [location.state, navigate]);

    if (!session) {
        return (
            <div className="container py-8">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-primary mx-auto"></div>
                    <p className="mt-4 text-muted-foreground">Loading receipt...</p>
                </div>
            </div>
        );
    }

    const handlePrint = () => {
        window.print();
    };

    const handleDownload = () => {
        const receiptContent = `
PARKING RECEIPT
===============

Receipt ID: ${session.id}
Date: ${session.startTime?.toLocaleDateString() || 'N/A'}
Time: ${session.startTime?.toLocaleTimeString() || 'N/A'}

PARKING DETAILS
---------------
Spot Number: ${session.spotNumber}
Start Time: ${session.startTime?.toLocaleString() || 'N/A'}
End Time: ${session.endTime?.toLocaleString() || 'N/A'}
Duration: ${session.durationMinutes <= 30 ? `${session.durationMinutes} minutes (minimum fee)` : `${session.durationMinutes} minutes`}

PAYMENT DETAILS
---------------
Hourly Rate: ${session.hourlyRate} birr/hour
Total Amount: ${session.totalCost} birr

Thank you for using our parking service!
        `;

        const blob = new Blob([receiptContent], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `parking-receipt-${session.id}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    return (
        <div className="container py-6">
            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-3xl font-bold mb-2">Parking Receipt</h1>
                    <p className="text-muted-foreground">Your parking session has been completed successfully</p>
                </div>
                <div className="flex gap-3">
                    <Link 
                        to="/dashboard" 
                        className="btn btn-outline"
                    >
                        <i className="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </Link>
                    <Link 
                        to="/reservations" 
                        className="btn btn-primary"
                    >
                        <i className="fas fa-calendar mr-2"></i>
                        My Reservations
                    </Link>
                </div>
            </div>

            {/* Success Header */}
            <div className="glass-card p-6 mb-6">
                <div className="text-center">
                    <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i className="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <h2 className="text-2xl font-bold text-green-600 mb-2">Payment Successful!</h2>
                    <p className="text-muted-foreground">Receipt #{session.id}</p>
                </div>
            </div>

            {/* Receipt Details */}
            <div className="glass-card p-6 mb-6">
                <div className="flex items-center gap-3 mb-6">
                    <h3 className="text-xl font-semibold">Parking Session Details</h3>
                    <span className="badge text-green-400 bg-green-500/20 border-green-500/30">
                        <i className="fas fa-check-circle mr-1"></i>
                        Completed
                    </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-parking mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Parking Spot</span>
                                <div className="font-semibold text-foreground">{session.spotNumber}</div>
                            </div>
                        </div>

                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-calendar mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Date</span>
                                <div className="font-semibold text-foreground">{session.startTime?.toLocaleDateString() || 'N/A'}</div>
                            </div>
                        </div>

                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-play mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Start Time</span>
                                <div className="font-semibold text-foreground">{session.startTime?.toLocaleTimeString() || 'N/A'}</div>
                            </div>
                        </div>

                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-stop mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">End Time</span>
                                <div className="font-semibold text-foreground">{session.endTime?.toLocaleTimeString() || 'N/A'}</div>
                            </div>
                        </div>
                    </div>

                    <div className="space-y-4">
                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-clock mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Duration</span>
                                <div className="font-semibold text-foreground">
                                    {session.durationMinutes <= 30 ? `${session.durationMinutes} minutes (minimum fee)` : `${session.durationMinutes} minutes`}
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-dollar-sign mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Hourly Rate</span>
                                <div className="font-semibold text-foreground">{session.hourlyRate} birr/hour</div>
                            </div>
                        </div>

                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-receipt mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Receipt ID</span>
                                <div className="font-semibold text-foreground">#{session.id}</div>
                            </div>
                        </div>

                        <div className="flex items-center text-muted-foreground">
                            <i className="fas fa-credit-card mr-3 w-5"></i>
                            <div>
                                <span className="text-sm">Payment Status</span>
                                <div className="font-semibold text-green-600">Paid</div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Total Amount Highlight */}
                <div className="mt-6 p-4 bg-primary/10 rounded-lg border border-primary/20">
                    <div className="flex justify-between items-center">
                        <span className="text-lg font-semibold">Total Amount Paid:</span>
                        <span className="text-2xl font-bold text-primary">{session.totalCost} birr</span>
                    </div>
                </div>
            </div>

            {/* Action Buttons */}
            <div className="glass-card p-6">
                <div className="flex flex-col sm:flex-row gap-4">
                    <button
                        onClick={handlePrint}
                        className="btn btn-outline flex-1"
                    >
                        <i className="fas fa-print mr-2"></i>
                        Print Receipt
                    </button>
                    <button
                        onClick={handleDownload}
                        className="btn btn-primary flex-1"
                    >
                        <i className="fas fa-download mr-2"></i>
                        Download Receipt
                    </button>
                    <button
                        onClick={() => navigate('/dashboard')}
                        className="btn btn-secondary flex-1"
                    >
                        <i className="fas fa-home mr-2"></i>
                        Back to Dashboard
                    </button>
                </div>

                {/* Footer */}
                <div className="text-center mt-6 pt-6 border-t border-border">
                    <p className="text-sm text-muted-foreground">
                        Thank you for using our parking service!
                    </p>
                    <p className="text-xs text-muted-foreground mt-1">
                        For support, contact us at support@parkingsystem.com
                    </p>
                </div>
            </div>
        </div>
    );
};

export default ReceiptPage;