function TestApp() {
    return (
        <div className="min-h-screen bg-blue-900 text-white flex items-center justify-center">
            <div className="text-center">
                <h1 className="text-4xl font-bold mb-4">
                    🎉 React + Laravel Working!
                </h1>
                <p className="text-lg mb-4">
                    Vite + React + Laravel integration successful
                </p>
                <button 
                    className="bg-blue-600 hover:bg-blue-700 px-6 py-2 rounded"
                    onClick={() => alert('React is working perfectly!')}
                >
                    Click Me!
                </button>
            </div>
        </div>
    );
}

export default TestApp;