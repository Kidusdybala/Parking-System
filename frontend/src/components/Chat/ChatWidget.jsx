import { useState, useEffect, useRef } from 'react';
import { useAuth } from '../../contexts/AuthContext';
import chatService from '../../services/chatService';
import ChatMessage from './ChatMessage';
import QuickQuestions from './QuickQuestions';

const ChatWidget = () => {
    const [isOpen, setIsOpen] = useState(false);
    const [messages, setMessages] = useState([]);
    const [inputMessage, setInputMessage] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [showQuickQuestions, setShowQuickQuestions] = useState(true);
    const messagesEndRef = useRef(null);
    const inputRef = useRef(null);
    const { user } = useAuth();

    // Load chat history on component mount
    useEffect(() => {
        const history = chatService.getChatHistory();
        if (history.length > 0) {
            setMessages(history);
            setShowQuickQuestions(false);
        } else {
            // Add welcome message
            const welcomeMessage = {
                id: 'welcome',
                type: 'bot',
                content: `🤖 **MikiPark Assistant**\n\nHello${user ? ` ${user.name}` : ''}! I'm here to help you with the MikiPark parking system.\n\nYou can ask me about:\n• Account management and login\n• Balance top-up and payments\n• Finding and reserving parking spots\n• Managing your reservations\n• System features and navigation\n\nHow can I help you today?`,
                timestamp: new Date().toISOString(),
                isWelcome: true
            };
            setMessages([welcomeMessage]);
        }
    }, [user]);

    // Scroll to bottom when messages change
    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    // Focus input when chat opens
    useEffect(() => {
        if (isOpen && inputRef.current) {
            setTimeout(() => inputRef.current.focus(), 100);
        }
    }, [isOpen]);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    };

    const handleSendMessage = async (messageText = null) => {
        const message = messageText || inputMessage.trim();
        if (!message || isLoading) return;

        // Add user message
        const userMessage = {
            type: 'user',
            content: message,
            timestamp: new Date().toISOString()
        };

        setMessages(prev => [...prev, userMessage]);
        setInputMessage('');
        setIsLoading(true);
        setShowQuickQuestions(false);

        // Save user message
        chatService.saveChatMessage(userMessage);

        try {
            const response = await chatService.sendMessage(message);

            if (response.success) {
                const botMessage = {
                    type: 'bot',
                    content: response.data.bot,
                    timestamp: new Date().toISOString(),
                    formatted: chatService.formatBotResponse(response.data.bot)
                };

                setMessages(prev => [...prev, botMessage]);
                chatService.saveChatMessage(botMessage);
            } else {
                const errorMessage = {
                    type: 'bot',
                    content: `🤖 **MikiPark Assistant**\n\n❌ ${response.error}`,
                    timestamp: new Date().toISOString(),
                    isError: true
                };

                setMessages(prev => [...prev, errorMessage]);
            }
        } catch (error) {
            const errorMessage = {
                type: 'bot',
                content: '🤖 **MikiPark Assistant**\n\n❌ Sorry, I\'m experiencing technical difficulties. Please try again in a moment.',
                timestamp: new Date().toISOString(),
                isError: true
            };

            setMessages(prev => [...prev, errorMessage]);
        } finally {
            setIsLoading(false);
        }
    };

    const handleQuickQuestion = (question) => {
        handleSendMessage(question.text);
    };

    const handleKeyPress = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            handleSendMessage();
        }
    };

    const toggleChat = () => {
        setIsOpen(!isOpen);
    };

    const clearChat = () => {
        setMessages([]);
        chatService.clearChatHistory();
        setShowQuickQuestions(true);
        
        // Add welcome message back
        const welcomeMessage = {
            id: 'welcome',
            type: 'bot',
            content: `🤖 **MikiPark Assistant**\n\nChat cleared! How can I help you with MikiPark today?`,
            timestamp: new Date().toISOString(),
            isWelcome: true
        };
        setMessages([welcomeMessage]);
    };

    if (!user) {
        return null; // Don't show chat widget if user is not logged in
    }

    return (
        <div className="fixed bottom-4 right-4 z-50">
            {/* Chat Window */}
            {isOpen && (
                <div className="mb-4 w-96 h-[500px] bg-card border border-border rounded-lg shadow-2xl flex flex-col overflow-hidden">
                    {/* Header */}
                    <div className="bg-primary text-primary-foreground p-4 flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <div className="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                                <i className="fas fa-robot text-sm"></i>
                            </div>
                            <div>
                                <h3 className="font-semibold text-sm">MikiPark Assistant</h3>
                                <p className="text-xs opacity-90">Always here to help</p>
                            </div>
                        </div>
                        <div className="flex items-center space-x-2">
                            <button
                                onClick={clearChat}
                                className="text-white/80 hover:text-white p-1 rounded"
                                title="Clear chat"
                            >
                                <i className="fas fa-trash text-sm"></i>
                            </button>
                            <button
                                onClick={toggleChat}
                                className="text-white/80 hover:text-white p-1 rounded"
                            >
                                <i className="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    {/* Messages */}
                    <div className="flex-1 overflow-y-auto p-4 space-y-4 bg-background">
                        {messages.map((message, index) => (
                            <ChatMessage key={message.id || index} message={message} />
                        ))}
                        
                        {/* Quick Questions */}
                        {showQuickQuestions && messages.length <= 1 && (
                            <QuickQuestions onQuestionClick={handleQuickQuestion} />
                        )}
                        
                        {/* Loading indicator */}
                        {isLoading && (
                            <div className="flex items-center space-x-2 text-muted-foreground">
                                <div className="flex space-x-1">
                                    <div className="w-2 h-2 bg-primary rounded-full animate-bounce"></div>
                                    <div className="w-2 h-2 bg-primary rounded-full animate-bounce" style={{ animationDelay: '0.1s' }}></div>
                                    <div className="w-2 h-2 bg-primary rounded-full animate-bounce" style={{ animationDelay: '0.2s' }}></div>
                                </div>
                                <span className="text-sm">MikiPark Assistant is typing...</span>
                            </div>
                        )}
                        
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Input */}
                    <div className="p-4 border-t border-border bg-card">
                        <div className="flex space-x-2">
                            <input
                                ref={inputRef}
                                type="text"
                                value={inputMessage}
                                onChange={(e) => setInputMessage(e.target.value)}
                                onKeyPress={handleKeyPress}
                                placeholder="Ask me anything about MikiPark..."
                                className="flex-1 px-3 py-2 bg-background border border-border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                disabled={isLoading}
                            />
                            <button
                                onClick={() => handleSendMessage()}
                                disabled={!inputMessage.trim() || isLoading}
                                className="px-4 py-2 bg-primary text-primary-foreground rounded-md hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <i className="fas fa-paper-plane text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Chat Toggle Button */}
            <button
                onClick={toggleChat}
                className="w-14 h-14 bg-primary text-primary-foreground rounded-full shadow-lg hover:bg-primary/90 transition-all duration-200 flex items-center justify-center group"
            >
                {isOpen ? (
                    <i className="fas fa-times text-lg"></i>
                ) : (
                    <>
                        <i className="fas fa-comments text-lg group-hover:scale-110 transition-transform"></i>
                        {/* Notification dot for new users */}
                        {messages.length <= 1 && (
                            <div className="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                <div className="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                            </div>
                        )}
                    </>
                )}
            </button>
        </div>
    );
};

export default ChatWidget;
