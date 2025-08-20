import { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import axios from 'axios';

const VerifyEmailPage = () => {
    const navigate = useNavigate();
    const location = useLocation();
    const emailFromQuery = new URLSearchParams(location.search).get('email') || '';

    const [email, setEmail] = useState(emailFromQuery);
    const [code, setCode] = useState('');
    const [loading, setLoading] = useState(false);
    const [resending, setResending] = useState(false);
    const [error, setError] = useState('');
    const [message, setMessage] = useState('');

    const handleVerify = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setMessage('');
        try {
            const res = await axios.post('/api/verify-email', { email, code });
            if (res.data.success) {
                setMessage('Email verified successfully. Redirecting to login...');
                setTimeout(() => navigate('/login'), 1200);
            } else {
                setError(res.data.message || 'Verification failed');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Verification failed');
        } finally {
            setLoading(false);
        }
    };

    const handleResend = async () => {
        setResending(true);
        setError('');
        setMessage('');
        try {
            const res = await axios.post('/api/resend-verification', { email });
            if (res.data.success) {
                setMessage('Verification code sent. Please check your email.');
            } else {
                setError(res.data.message || 'Failed to send code');
            }
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to send code');
        } finally {
            setResending(false);
        }
    };

    return (
        <div className="min-h-screen flex flex-col">
            <div className="fixed inset-0 noise-bg pointer-events-none"></div>
            <header className="sticky top-0 z-30 w-full backdrop-blur-sm border-b border-white/10 bg-parkBlue-900/80">
                <div className="container flex items-center justify-between h-16">
                    <div className="flex items-center gap-2">
                        <Link to="/">
                            <i className="fas fa-parking text-primary text-2xl"></i>
                            <span className="font-bold text-xl">Miki<span className="text-primary">Park</span></span>
                        </Link>
                    </div>
                </div>
            </header>

            <main className="flex-1 flex items-center justify-center py-12 px-4">
                <div className="glass-card w-full max-w-md p-8 relative overflow-hidden">
                    <div className="absolute -top-24 -right-24 w-64 h-64 bg-primary/10 rounded-full blur-3xl"></div>
                    <div className="absolute -bottom-24 -left-24 w-64 h-64 bg-parkBlue-500/10 rounded-full blur-3xl"></div>

                    <div className="relative z-10">
                        <div className="text-center mb-8">
                            <h1 className="text-2xl font-bold">Verify your email</h1>
                            <p className="text-muted-foreground mt-2">Enter the 6-digit code sent to your email</p>
                        </div>

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

                        <form onSubmit={handleVerify}>
                            <div className="space-y-4">
                                <div>
                                    <label htmlFor="email" className="block text-sm font-medium mb-1">Email Address</label>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email"
                                        className="input w-full" 
                                        placeholder="user@gmail.com" 
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        required 
                                        autoComplete="email"
                                    />
                                </div>
                                <div>
                                    <label htmlFor="code" className="block text-sm font-medium mb-1">Verification Code</label>
                                    <input 
                                        type="text" 
                                        id="code" 
                                        name="code"
                                        className="input w-full tracking-widest text-center" 
                                        placeholder="123456" 
                                        value={code}
                                        onChange={(e) => setCode(e.target.value.replace(/[^0-9]/g, '').slice(0, 6))}
                                        required 
                                        inputMode="numeric"
                                    />
                                </div>
                                <button type="submit" className="btn btn-primary w-full" disabled={loading}>
                                    {loading ? 'Verifying...' : 'Verify Email'}
                                    <i className="fas fa-check ml-2"></i>
                                </button>
                            </div>
                        </form>

                        <div className="mt-6 flex items-center justify-between">
                            <button type="button" className="text-primary hover:underline text-sm" onClick={handleResend} disabled={resending || !email}>
                                {resending ? 'Sending...' : 'Resend Code'}
                            </button>
                            <Link to="/login" className="text-sm text-muted-foreground hover:underline">Back to login</Link>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
};

export default VerifyEmailPage;


