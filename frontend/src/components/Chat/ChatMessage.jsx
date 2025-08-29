import { useState } from 'react';

const ChatMessage = ({ message }) => {
    const [isExpanded, setIsExpanded] = useState(false);
    const isBot = message.type === 'bot';
    const isUser = message.type === 'user';

    // Format timestamp
    const formatTime = (timestamp) => {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    // Parse markdown-style formatting
    const parseContent = (content) => {
        if (!content) return '';
        
        // Replace **bold** with <strong>
        content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Replace *italic* with <em>
        content = content.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Replace line breaks
        content = content.replace(/\n/g, '<br>');
        
        return content;
    };

    // Render structured bot response
    const renderStructuredResponse = (formatted) => {
        const { sections } = formatted;
        
        return (
            <div className="space-y-3">
                {/* Question */}
                {sections.question && (
                    <div className="bg-primary/10 border-l-4 border-primary p-3 rounded-r">
                        <div className="flex items-center space-x-2 mb-1">
                            <i className="fas fa-question-circle text-primary text-sm"></i>
                            <span className="font-semibold text-primary text-sm">Question</span>
                        </div>
                        <p className="text-sm" dangerouslySetInnerHTML={{ __html: parseContent(sections.question) }} />
                    </div>
                )}

                {/* Answer */}
                {sections.answer && (
                    <div className="bg-green-500/10 border-l-4 border-green-500 p-3 rounded-r">
                        <div className="flex items-center space-x-2 mb-2">
                            <i className="fas fa-lightbulb text-green-500 text-sm"></i>
                            <span className="font-semibold text-green-500 text-sm">Answer</span>
                        </div>
                        <div className="text-sm space-y-1" dangerouslySetInnerHTML={{ __html: parseContent(sections.answer) }} />
                    </div>
                )}

               </div>
        );
    };

    // Render regular content
    const renderRegularContent = (content) => {
        const isLongContent = content.length > 300;
        const displayContent = isLongContent && !isExpanded 
            ? content.substring(0, 300) + '...' 
            : content;

        return (
            <div>
                <div 
                    className="text-sm leading-relaxed"
                    dangerouslySetInnerHTML={{ __html: parseContent(displayContent) }}
                />
                {isLongContent && (
                    <button
                        onClick={() => setIsExpanded(!isExpanded)}
                        className="mt-2 text-xs text-primary hover:text-primary/80 font-medium"
                    >
                        {isExpanded ? 'Show less' : 'Show more'}
                    </button>
                )}
            </div>
        );
    };

    if (isUser) {
        return (
            <div className="flex justify-end">
                <div className="max-w-[80%] bg-primary text-primary-foreground rounded-lg px-4 py-2 shadow-sm">
                    <div className="text-sm leading-relaxed">
                        {message.content}
                    </div>
                    <div className="text-xs opacity-70 mt-1 text-right">
                        {formatTime(message.timestamp)}
                    </div>
                </div>
            </div>
        );
    }

    if (isBot) {
        return (
            <div className="flex justify-start">
                <div className="flex space-x-2 max-w-[85%]">
                    {/* Bot Avatar */}
                    <div className="w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                        <i className="fas fa-robot text-primary-foreground text-xs"></i>
                    </div>
                    
                    {/* Message Content */}
                    <div className={`rounded-lg px-4 py-3 shadow-sm ${
                        message.isError 
                            ? 'bg-red-500/10 border border-red-500/20' 
                            : 'bg-card border border-border'
                    }`}>
                        {/* Render structured or regular content */}
                        {message.formatted?.isStructured 
                            ? renderStructuredResponse(message.formatted)
                            : renderRegularContent(message.content)
                        }
                        
                        {/* Timestamp */}
                        <div className="text-xs text-muted-foreground mt-2 flex items-center justify-between">
                            <span>MikiPark Assistant</span>
                            <span>{formatTime(message.timestamp)}</span>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return null;
};

export default ChatMessage;
