// Simple React component for testing

const SimpleApp = () => {
    return (
        <div className="min-h-screen bg-blue-900 text-white flex items-center justify-center">
            <div className="text-center">
                <h1 className="text-4xl font-bold mb-4">
                    🎉 React is Working!
                </h1>
                <p className="text-lg">
                    Your Laravel + React integration is successful.
                </p>
                <div className="mt-8 space-x-4">
                    <button className="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded">
                        Test Button
                    </button>
                </div>
            </div>
        </div>
    );
};

export default SimpleApp;