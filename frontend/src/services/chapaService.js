import axios from './api';

/**
 * Chapa Payment Service for Frontend
 * Handles secure payment processing via Chapa gateway
 */

export class ChapaService {
    constructor() {
        this.baseURL = '/api/chapa';
    }

    /**
     * Initialize wallet top-up payment
     * @param {Object} paymentData - Payment details
     * @returns {Promise<Object>} Payment initialization response
     */
    async initializeWalletTopup(paymentData) {
        try {
            const response = await axios.post(`${this.baseURL}/wallet/topup`, paymentData);
            
            if (response.data.status === 'success') {
                return {
                    success: true,
                    data: response.data.data,
                    checkoutUrl: response.data.data.checkout_url,
                    txRef: response.data.data.tx_ref,
                    message: response.data.message
                };
            }
            
            return {
                success: false,
                message: response.data.message || 'Payment initialization failed',
                errors: response.data.errors || []
            };
        } catch (error) {
            console.error('Chapa wallet topup error:', error);
            
            if (error.response?.status === 401) {
                throw new Error('Authentication required. Please login again.');
            }
            
            if (error.response?.status === 422) {
                return {
                    success: false,
                    message: 'Validation failed',
                    errors: error.response.data.errors || []
                };
            }
            
            return {
                success: false,
                message: error.response?.data?.message || 'Network error occurred',
                errors: []
            };
        }
    }

    /**
     * Initialize reservation payment
     * @param {Object} reservationData - Reservation payment details
     * @returns {Promise<Object>} Payment initialization response
     */
    async initializeReservationPayment(reservationData) {
        try {
            const response = await axios.post(`${this.baseURL}/reservation/payment`, reservationData);
            
            if (response.data.status === 'success') {
                return {
                    success: true,
                    data: response.data.data,
                    checkoutUrl: response.data.data.checkout_url,
                    txRef: response.data.data.tx_ref,
                    message: response.data.message
                };
            }
            
            return {
                success: false,
                message: response.data.message || 'Payment initialization failed'
            };
        } catch (error) {
            console.error('Chapa reservation payment error:', error);
            return {
                success: false,
                message: error.response?.data?.message || 'Network error occurred'
            };
        }
    }

    /**
     * Verify payment status
     * @param {string} txRef - Transaction reference
     * @returns {Promise<Object>} Verification response
     */
    async verifyPayment(txRef) {
        try {
            const response = await axios.get(`${this.baseURL}/verify/${txRef}`);
            
            return {
                success: response.data.status === 'success',
                transaction: response.data.transaction,
                message: response.data.message,
                verified: response.data.transaction?.status === 'success'
            };
        } catch (error) {
            console.error('Payment verification error:', error);
            return {
                success: false,
                message: error.response?.data?.message || 'Verification failed'
            };
        }
    }

    /**
     * Get user transaction history
     * @returns {Promise<Object>} Transaction history
     */
    async getTransactionHistory() {
        try {
            const response = await axios.get(`${this.baseURL}/transactions`);
            
            return {
                success: response.data.status === 'success',
                transactions: response.data.data || [],
                message: response.data.message
            };
        } catch (error) {
            console.error('Transaction history error:', error);
            return {
                success: false,
                transactions: [],
                message: error.response?.data?.message || 'Failed to fetch transactions'
            };
        }
    }

    /**
     * Redirect user to Chapa checkout
     * @param {string} checkoutUrl - Chapa checkout URL
     */
    redirectToCheckout(checkoutUrl) {
        if (checkoutUrl) {
            window.location.href = checkoutUrl;
        } else {
            console.error('No checkout URL provided');
        }
    }

    /**
     * Format amount for display
     * @param {number} amount - Amount to format
     * @param {string} currency - Currency (default: ETB)
     * @returns {string} Formatted amount
     */
    formatAmount(amount, currency = 'ETB') {
        return `${parseFloat(amount).toFixed(2)} ${currency}`;
    }

    /**
     * Validate payment amount
     * @param {number} amount - Amount to validate
     * @returns {Object} Validation result
     */
    validateAmount(amount) {
        const numAmount = parseFloat(amount);
        
        if (isNaN(numAmount)) {
            return { valid: false, message: 'Please enter a valid amount' };
        }
        
        if (numAmount < 10) {
            return { valid: false, message: 'Minimum amount is 10 ETB' };
        }
        
        if (numAmount > 50000) {
            return { valid: false, message: 'Maximum amount is 50,000 ETB' };
        }
        
        return { valid: true, message: 'Amount is valid' };
    }

    /**
     * Get recommended payment amounts
     * @returns {Array} Array of recommended amounts
     */
    getRecommendedAmounts() {
        return [50, 100, 200, 500, 1000];
    }

    /**
     * Check if amount qualifies for any bonuses
     * @param {number} amount - Payment amount
     * @returns {Object} Bonus information
     */
    checkForBonuses(amount) {
        const numAmount = parseFloat(amount);
        
        if (numAmount >= 1000) {
            return {
                hasBonus: true,
                bonusPercent: 5,
                bonusAmount: numAmount * 0.05,
                message: 'Get 5% bonus on payments over 1000 ETB!'
            };
        }
        
        if (numAmount >= 500) {
            return {
                hasBonus: true,
                bonusPercent: 2,
                bonusAmount: numAmount * 0.02,
                message: 'Get 2% bonus on payments over 500 ETB!'
            };
        }
        
        return {
            hasBonus: false,
            bonusPercent: 0,
            bonusAmount: 0,
            message: null
        };
    }
}

// Create singleton instance
export const chapaService = new ChapaService();

export default chapaService;