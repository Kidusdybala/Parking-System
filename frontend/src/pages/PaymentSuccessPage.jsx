import { useState, useEffect } from 'react';
import { useNavigate, useSearchParams, Link } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { chapaService } from '../services/chapaService';
import LoadingSpinner from '../components/Common/LoadingSpinner';

const PaymentSuccessPage = () => {
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const { user, updateUser } = useAuth();
    const [loading, setLoading] = useState(true);
    const [paymentStatus, setPaymentStatus] = useState(null);
    const [transactionDetails, setTransactionDetails] = useState(null);
    const [error, setError] = useState('');

    useEffect(() => {
        verifyPayment();
    }, []);

    const verifyPayment = async () => {
        try {
            // Get transaction reference from URL params or session storage
            const txRef = searchParams.get('trx_ref') || searchParams.get('tx_ref');
            const pendingTopup = sessionStorage.getItem('pendingTopup');
            
            let transactionRef = txRef;
            
            // If no txRef in URL, try to get from session storage
            if (!transactionRef && pendingTopup) {
                const pending = JSON.parse(pendingTopup);
                transactionRef = pending.txRef;
            }

            if (!transactionRef) {
                setError('No transaction reference found');
                setLoading(false);
                return;
            }

            // Verify the payment with Chapa
            const result = await chapaService.verifyPayment(transactionRef);
            
            if (result.success) {
                setPaymentStatus(result.verified ? 'success' : 'pending');
                setTransactionDetails(result.transaction);

                // If payment was successful, refresh user data to get updated balance
                if (result.verified && result.transaction.status === 'success') {
                    // Remove pending transaction from storage
                    sessionStorage.removeItem('pendingTopup');
                    
                    // Refresh user data to get updated balance
                    try {
                        const response = await fetch('/api/user', {
                            headers: {
                                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const userData = await response.json();
                            updateUser(userData.user);
                        }
                    } catch (error) {
                        console.error('Failed to refresh user data:', error);
                    }
                }
            } else {
                setPaymentStatus('failed');
                setError(result.message || 'Payment verification failed');
            }
        } catch (error) {
            console.error('Payment verification error:', error);
            setPaymentStatus('failed');
            setError('An error occurred while verifying payment');
        } finally {
            setLoading(false);
        }
    };

    const handleReturnToDashboard = () => {
        navigate('/dashboard');
    };

    const handleReturnToProfile = () => {
        navigate('/profile');
    };

    const handleTryAgain = () => {
        navigate('/profile');
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <div className="text-center">
                    <LoadingSpinner />
                    <p className="mt-4 text-muted-foreground">Verifying your payment...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-purple-800 flex items-center justify-center p-4">
            <div className="max-w-md w-full">
                <div className="glass-card p-8 text-center">
                    {paymentStatus === 'success' && (
                        <>
                            <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i className="fas fa-check-circle text-3xl text-green-500"></i>
                            </div>
                            <h1 className="text-2xl font-bold text-green-600 mb-4">Payment Successful!</h1>
                            <p className="text-muted-foreground mb-6">
                                Your wallet has been topped up successfully. The amount has been added to your balance.
                            </p>
                            
                            {transactionDetails && (
                                <div className="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 text-left">
                                    <h3 className="font-semibold text-green-800 mb-2">Transaction Details:</h3>
                                    <div className="space-y-1 text-sm text-green-700">
                                        <p><strong>Amount:</strong> {chapaService.formatAmount(transactionDetails.amount)}</p>
                                        <p><strong>Reference:</strong> {transactionDetails.tx_ref}</p>
                                        <p><strong>Status:</strong> <span className="capitalize">{transactionDetails.status}</span></p>
                                        {transactionDetails.created_at && (
                                            <p><strong>Date:</strong> {new Date(transactionDetails.created_at).toLocaleString()}</p>
                                        )}
                                    </div>
                                </div>
                            )}
                            
                            <div className="space-y-3">
                                <button
                                    onClick={handleReturnToProfile}
                                    className="btn btn-primary w-full"
                                >
                                    <i className="fas fa-wallet mr-2"></i>
                                    View Wallet
                                </button>
                                <button
                                    onClick={handleReturnToDashboard}
                                    className="btn btn-outline w-full"
                                >
                                    <i className="fas fa-home mr-2"></i>
                                    Go to Dashboard
                                </button>
                            </div>
                        </>
                    )}

                    {paymentStatus === 'pending' && (
                        <>
                            <div className="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i className="fas fa-clock text-3xl text-yellow-500"></i>
                            </div>
                            <h1 className="text-2xl font-bold text-yellow-600 mb-4">Payment Pending</h1>
                            <p className="text-muted-foreground mb-6">
                                Your payment is being processed. This may take a few minutes to complete.
                            </p>
                            
                            {transactionDetails && (
                                <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
                                    <h3 className="font-semibold text-yellow-800 mb-2">Transaction Details:</h3>
                                    <div className="space-y-1 text-sm text-yellow-700">
                                        <p><strong>Amount:</strong> {chapaService.formatAmount(transactionDetails.amount)}</p>
                                        <p><strong>Reference:</strong> {transactionDetails.tx_ref}</p>
                                        <p><strong>Status:</strong> <span className="capitalize">{transactionDetails.status}</span></p>
                                    </div>
                                </div>
                            )}
                            
                            <div className="space-y-3">
                                <button
                                    onClick={() => window.location.reload()}
                                    className="btn btn-primary w-full"
                                >
                                    <i className="fas fa-sync mr-2"></i>
                                    Check Status Again
                                </button>
                                <button
                                    onClick={handleReturnToDashboard}
                                    className="btn btn-outline w-full"
                                >
                                    <i className="fas fa-home mr-2"></i>
                                    Go to Dashboard
                                </button>
                            </div>
                        </>
                    )}

                    {paymentStatus === 'failed' && (
                        <>
                            <div className="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i className="fas fa-times-circle text-3xl text-red-500"></i>
                            </div>
                            <h1 className="text-2xl font-bold text-red-600 mb-4">Payment Failed</h1>
                            <p className="text-muted-foreground mb-2">
                                We couldn't process your payment. Please try again.
                            </p>
                            {error && (
                                <p className="text-sm text-red-500 mb-6">{error}</p>
                            )}
                            
                            <div className="space-y-3">
                                <button
                                    onClick={handleTryAgain}
                                    className="btn btn-primary w-full"
                                >
                                    <i className="fas fa-retry mr-2"></i>
                                    Try Again
                                </button>
                                <button
                                    onClick={handleReturnToDashboard}
                                    className="btn btn-outline w-full"
                                >
                                    <i className="fas fa-home mr-2"></i>
                                    Go to Dashboard
                                </button>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default PaymentSuccessPage;