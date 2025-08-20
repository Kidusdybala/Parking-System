import { Component } from 'react';

class ErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null, errorInfo: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true };
    }

    componentDidCatch(error, errorInfo) {
        this.setState({
            error: error,
            errorInfo: errorInfo
        });
        console.error('Error caught by boundary:', error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="min-h-screen flex items-center justify-center bg-parkBlue-900">
                    <div className="glass-card p-8 max-w-md mx-4">
                        <div className="text-center">
                            <i className="fas fa-exclamation-triangle text-4xl text-red-400 mb-4"></i>
                            <h2 className="text-xl font-bold mb-4">Something went wrong</h2>
                            <p className="text-muted-foreground mb-6">
                                The application encountered an error. Please refresh the page or try again later.
                            </p>
                            <div className="space-y-2">
                                <button
                                    onClick={() => window.location.reload()}
                                    className="btn btn-primary w-full"
                                >
                                    <i className="fas fa-refresh mr-2"></i>
                                    Refresh Page
                                </button>
                                <button
                                    onClick={() => this.setState({ hasError: false, error: null, errorInfo: null })}
                                    className="btn btn-outline w-full"
                                >
                                    Try Again
                                </button>
                            </div>
                            {process.env.NODE_ENV === 'development' && this.state.error && (
                                <details className="mt-4 text-left">
                                    <summary className="cursor-pointer text-sm text-muted-foreground">
                                        Error Details
                                    </summary>
                                    <pre className="mt-2 text-xs bg-red-900/20 p-2 rounded overflow-auto">
                                        {this.state.error.toString()}
                                        {this.state.errorInfo.componentStack}
                                    </pre>
                                </details>
                            )}
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

export default ErrorBoundary;