import axios from './api';

class ChatService {
    /**
     * Send a message to the MikiPark chatbot
     * @param {string} message - The user's message
     * @returns {Promise<Object>} - The bot's response
     */
    async sendMessage(message) {
        try {
            const response = await axios.post('/api/chat', {
                message: message.trim()
            });

            return {
                success: true,
                data: response.data
            };
        } catch (error) {
            console.error('Chat service error:', error);
            
            // Handle different error scenarios
            if (error.response?.status === 401) {
                return {
                    success: false,
                    error: 'Please log in to use the chat assistant.',
                    needsAuth: true
                };
            }
            
            if (error.response?.status === 422) {
                return {
                    success: false,
                    error: 'Please enter a valid message.',
                    validationError: true
                };
            }
            
            return {
                success: false,
                error: error.response?.data?.message || 'Failed to send message. Please try again.',
                data: null
            };
        }
    }

    /**
     * Get predefined quick questions for the chat
     * @returns {Array} - Array of quick question objects
     */
    getQuickQuestions() {
        return [
            {
                id: 'balance',
                text: 'How do I check my balance?',
                icon: 'fas fa-wallet',
                category: 'account'
            },
            {
                id: 'topup',
                text: 'How do I top up my balance?',
                icon: 'fas fa-plus-circle',
                category: 'payment'
            },
            {
                id: 'parking',
                text: 'How do I reserve a parking spot?',
                icon: 'fas fa-parking',
                category: 'parking'
            },
            {
                id: 'reservation',
                text: 'How do I manage my reservations?',
                icon: 'fas fa-calendar-check',
                category: 'reservation'
            },
            {
                id: 'cost',
                text: 'How much does parking cost?',
                icon: 'fas fa-money-bill-wave',
                category: 'pricing'
            },
            {
                id: 'profile',
                text: 'How do I update my profile?',
                icon: 'fas fa-user-edit',
                category: 'account'
            },
            {
                id: 'login',
                text: 'How do I log in?',
                icon: 'fas fa-sign-in-alt',
                category: 'account'
            },
            {
                id: 'help',
                text: 'What can you help me with?',
                icon: 'fas fa-question-circle',
                category: 'general'
            }
        ];
    }

    /**
     * Format bot response for display
     * @param {string} botResponse - Raw bot response
     * @returns {Object} - Formatted response object
     */
    formatBotResponse(botResponse) {
        // Check if response contains structured format
        if (botResponse.includes('📌') && botResponse.includes('📝') && botResponse.includes('✅')) {
            const sections = {
                question: this.extractSection(botResponse, '📌', '📝'),
                answer: this.extractSection(botResponse, '📝', '📊'),
                info: this.extractSection(botResponse, '📊', '✅'),
                conclusion: this.extractSection(botResponse, '✅', null)
            };

            return {
                isStructured: true,
                sections,
                raw: botResponse
            };
        }

        return {
            isStructured: false,
            raw: botResponse
        };
    }

    /**
     * Extract a section from structured bot response
     * @param {string} text - Full text
     * @param {string} startMarker - Start marker
     * @param {string} endMarker - End marker (null for end of text)
     * @returns {string} - Extracted section
     */
    extractSection(text, startMarker, endMarker) {
        const startIndex = text.indexOf(startMarker);
        if (startIndex === -1) return '';

        const contentStart = startIndex + startMarker.length;
        const endIndex = endMarker ? text.indexOf(endMarker, contentStart) : text.length;
        
        if (endIndex === -1) return text.substring(contentStart).trim();
        
        return text.substring(contentStart, endIndex).trim();
    }

    /**
     * Get chat history from localStorage
     * @returns {Array} - Array of chat messages
     */
    getChatHistory() {
        try {
            const history = localStorage.getItem('mikipark_chat_history');
            return history ? JSON.parse(history) : [];
        } catch (error) {
            console.error('Error loading chat history:', error);
            return [];
        }
    }

    /**
     * Save chat message to localStorage
     * @param {Object} message - Message object
     */
    saveChatMessage(message) {
        try {
            const history = this.getChatHistory();
            const newMessage = {
                ...message,
                id: Date.now() + Math.random(),
                timestamp: new Date().toISOString()
            };
            
            history.push(newMessage);
            
            // Keep only last 50 messages
            if (history.length > 50) {
                history.splice(0, history.length - 50);
            }
            
            localStorage.setItem('mikipark_chat_history', JSON.stringify(history));
        } catch (error) {
            console.error('Error saving chat message:', error);
        }
    }

    /**
     * Clear chat history
     */
    clearChatHistory() {
        try {
            localStorage.removeItem('mikipark_chat_history');
        } catch (error) {
            console.error('Error clearing chat history:', error);
        }
    }
}

export default new ChatService();
