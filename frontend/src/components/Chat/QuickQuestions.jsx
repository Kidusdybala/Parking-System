import chatService from '../../services/chatService';

const QuickQuestions = ({ onQuestionClick }) => {
    const questions = chatService.getQuickQuestions();

    // Group questions by category
    const groupedQuestions = questions.reduce((acc, question) => {
        if (!acc[question.category]) {
            acc[question.category] = [];
        }
        acc[question.category].push(question);
        return acc;
    }, {});

    const categoryConfig = {
        account: {
            title: 'Account & Profile',
            icon: 'fas fa-user',
            color: 'blue'
        },
        parking: {
            title: 'Parking & Spots',
            icon: 'fas fa-parking',
            color: 'green'
        },
        reservation: {
            title: 'Reservations',
            icon: 'fas fa-calendar-check',
            color: 'purple'
        },
        payment: {
            title: 'Payments & Balance',
            icon: 'fas fa-credit-card',
            color: 'yellow'
        },
        pricing: {
            title: 'Pricing & Costs',
            icon: 'fas fa-money-bill-wave',
            color: 'red'
        },
        general: {
            title: 'General Help',
            icon: 'fas fa-question-circle',
            color: 'gray'
        }
    };

    const getColorClasses = (color) => {
        const colorMap = {
            blue: 'bg-blue-500/10 border-blue-500/20 text-blue-600 hover:bg-blue-500/20',
            green: 'bg-green-500/10 border-green-500/20 text-green-600 hover:bg-green-500/20',
            purple: 'bg-purple-500/10 border-purple-500/20 text-purple-600 hover:bg-purple-500/20',
            yellow: 'bg-yellow-500/10 border-yellow-500/20 text-yellow-600 hover:bg-yellow-500/20',
            red: 'bg-red-500/10 border-red-500/20 text-red-600 hover:bg-red-500/20',
            gray: 'bg-gray-500/10 border-gray-500/20 text-gray-600 hover:bg-gray-500/20'
        };
        return colorMap[color] || colorMap.gray;
    };

    return (
        <div className="space-y-4">
            <div className="text-center">
                <h4 className="text-sm font-semibold text-foreground mb-2">Quick Questions</h4>
                <p className="text-xs text-muted-foreground">Click on any question to get started</p>
            </div>

            {Object.entries(groupedQuestions).map(([category, categoryQuestions]) => {
                const config = categoryConfig[category];
                if (!config) return null;

                return (
                    <div key={category} className="space-y-2">
                        {/* Category Header */}
                        <div className="flex items-center space-x-2 px-2">
                            <i className={`${config.icon} text-xs text-muted-foreground`}></i>
                            <span className="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                                {config.title}
                            </span>
                        </div>

                        {/* Questions */}
                        <div className="grid grid-cols-1 gap-2">
                            {categoryQuestions.map((question) => (
                                <button
                                    key={question.id}
                                    onClick={() => onQuestionClick(question)}
                                    className={`
                                        p-3 rounded-lg border text-left transition-all duration-200 
                                        hover:scale-[1.02] hover:shadow-sm group
                                        ${getColorClasses(config.color)}
                                    `}
                                >
                                    <div className="flex items-center space-x-3">
                                        <div className="flex-shrink-0">
                                            <i className={`${question.icon} text-sm`}></i>
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium truncate group-hover:text-current">
                                                {question.text}
                                            </p>
                                        </div>
                                        <div className="flex-shrink-0">
                                            <i className="fas fa-chevron-right text-xs opacity-50 group-hover:opacity-100 transition-opacity"></i>
                                        </div>
                                    </div>
                                </button>
                            ))}
                        </div>
                    </div>
                );
            })}

            {/* Custom Question Input */}
            <div className="pt-2 border-t border-border">
                <div className="text-center">
                    <p className="text-xs text-muted-foreground mb-2">
                        Or type your own question below
                    </p>
                    <div className="flex items-center justify-center space-x-1 text-xs text-muted-foreground">
                        <i className="fas fa-arrow-down animate-bounce"></i>
                        <span>Use the input field below</span>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default QuickQuestions;
