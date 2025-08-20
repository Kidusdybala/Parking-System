import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext.simple';

const TestPage = () => {
    return (
        <div className="min-h-screen bg-parkBlue-900 flex items-center justify-center">
            <div className="glass-card p-8 text-center">
                <h1 className="text-2xl font-bold mb-4">🎉 MikiPark is Working!</h1>
                <p className="text-muted-foreground">React app loaded successfully</p>
            </div>
        </div>
    );
};

function App() {
    return (
        <AuthProvider>
            <Router>
                <Routes>
                    <Route path="*" element={<TestPage />} />
                </Routes>
            </Router>
        </AuthProvider>
    );
}

export default App;