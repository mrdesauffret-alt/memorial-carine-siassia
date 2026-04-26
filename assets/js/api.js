/**
 * Gestionnaire API pour le mémorial
 */

class MemorialAPI {
    constructor() {
        this.baseURL = window.location.origin + '/backend/api';
        this.uploadURL = window.location.origin + '/backend/upload.php';
    }

    /**
     * Récupérer les souvenirs approuvés
     */
    async getMemories(page = 1, limit = 20) {
        try {
            const response = await fetch(`${this.baseURL}/memories.php?page=${page}&limit=${limit}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des souvenirs:', error);
            throw error;
        }
    }

    /**
     * Créer un nouveau souvenir
     */
    async createMemory(memoryData) {
        try {
            const response = await fetch(`${this.baseURL}/memories.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(memoryData)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la création du souvenir:', error);
            throw error;
        }
    }

    /**
     * Uploader une image
     */
    async uploadImage(file) {
        try {
            const formData = new FormData();
            formData.append('image', file);

            const response = await fetch(this.uploadURL, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Erreur lors de l\'upload de l\'image:', error);
            throw error;
        }
    }

    /**
     * Récupérer les statistiques des bougies
     */
    async getCandleStats() {
        try {
            const response = await fetch(`${this.baseURL}/candles.php`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des stats des bougies:', error);
            throw error;
        }
    }

    /**
     * Allumer une bougie
     */
    async lightCandle(visitorName = '', message = '') {
        try {
            const response = await fetch(`${this.baseURL}/candles.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    visitor_name: visitorName,
                    message: message
                })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Erreur lors de l\'allumage de la bougie:', error);
            throw error;
        }
    }

    /**
     * Récupérer les bougies récentes
     */
    async getRecentCandles(limit = 20) {
        try {
            const response = await fetch(`${this.baseURL}/candles.php/recent?limit=${limit}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des bougies récentes:', error);
            throw error;
        }
    }
}

// Instance globale de l'API
window.memorialAPI = new MemorialAPI();
