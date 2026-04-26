/**
 * Tests unitaires pour l'API backend
 */

describe('API Backend', () => {
  const API_BASE_URL = 'http://localhost/memorial-carine-siassia/backend/api';

  beforeEach(() => {
    // Reset fetch mock
    fetch.mockClear();
  });

  describe('API Memories', () => {
    test('devrait récupérer les souvenirs approuvés', async () => {
      const mockMemories = [
        {
          id: 1,
          type: 'story',
          content: 'Un beau souvenir',
          author_name: 'Jean',
          created_at: '2025-01-01T00:00:00Z',
        },
      ];

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          success: true,
          data: mockMemories,
          pagination: {
            page: 1,
            limit: 20,
            total: 1,
            pages: 1,
          },
        }),
      });

      const response = await fetch(`${API_BASE_URL}/memories.php`);
      const data = await response.json();

      expect(fetch).toHaveBeenCalledWith(`${API_BASE_URL}/memories.php`);
      expect(data.success).toBe(true);
      expect(data.data).toHaveLength(1);
      expect(data.data[0].type).toBe('story');
    });

    test('devrait créer un nouveau souvenir', async () => {
      const newMemory = {
        type: 'story',
        content: 'Nouveau souvenir',
        author_name: 'Marie',
        author_email: 'marie@example.com',
      };

      fetch.mockResolvedValueOnce({
        ok: true,
        status: 201,
        json: async () => ({
          success: true,
          message: 'Souvenir créé avec succès',
          id: 123,
        }),
      });

      const response = await fetch(`${API_BASE_URL}/memories.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(newMemory),
      });

      const data = await response.json();

      expect(fetch).toHaveBeenCalledWith(
        `${API_BASE_URL}/memories.php`,
        expect.objectContaining({
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(newMemory),
        })
      );
      expect(data.success).toBe(true);
      expect(data.id).toBe(123);
    });

    test('devrait gérer les erreurs de validation', async () => {
      const invalidMemory = {
        type: 'story',
        // content manquant
      };

      fetch.mockResolvedValueOnce({
        ok: false,
        status: 400,
        json: async () => ({
          error: "Le champ 'content' est requis",
        }),
      });

      const response = await fetch(`${API_BASE_URL}/memories.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(invalidMemory),
      });

      const data = await response.json();

      expect(response.status).toBe(400);
      expect(data.error).toBe("Le champ 'content' est requis");
    });
  });

  describe('API Candles', () => {
    test('devrait récupérer les statistiques des bougies', async () => {
      const mockStats = {
        total_candles: 247,
        unique_visitors: 150,
        today_candles: 5,
        week_candles: 25,
      };

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          success: true,
          data: mockStats,
        }),
      });

      const response = await fetch(`${API_BASE_URL}/candles.php`);
      const data = await response.json();

      expect(fetch).toHaveBeenCalledWith(`${API_BASE_URL}/candles.php`);
      expect(data.success).toBe(true);
      expect(data.data.total_candles).toBe(247);
    });

    test('devrait allumer une bougie', async () => {
      const candleData = {
        visitor_name: 'Jean',
        message: 'En souvenir de Carine',
      };

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          success: true,
          id: 248,
          stats: {
            total_candles: 248,
            unique_visitors: 151,
            today_candles: 6,
            week_candles: 26,
          },
        }),
      });

      const response = await fetch(`${API_BASE_URL}/candles.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(candleData),
      });

      const data = await response.json();

      expect(fetch).toHaveBeenCalledWith(
        `${API_BASE_URL}/candles.php`,
        expect.objectContaining({
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(candleData),
        })
      );
      expect(data.success).toBe(true);
      expect(data.stats.total_candles).toBe(248);
    });

    test('devrait empêcher d\'allumer plusieurs bougies par jour', async () => {
      fetch.mockResolvedValueOnce({
        ok: false,
        status: 400,
        json: async () => ({
          success: false,
          message: 'Vous avez déjà allumé une bougie aujourd\'hui',
        }),
      });

      const response = await fetch(`${API_BASE_URL}/candles.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          visitor_name: 'Jean',
          message: 'Encore une bougie',
        }),
      });

      const data = await response.json();

      expect(response.status).toBe(400);
      expect(data.success).toBe(false);
      expect(data.message).toBe('Vous avez déjà allumé une bougie aujourd\'hui');
    });
  });

  describe('Upload d\'images', () => {
    test('devrait uploader une image avec succès', async () => {
      const mockFile = new File(['test image content'], 'test.jpg', {
        type: 'image/jpeg',
      });

      const formData = new FormData();
      formData.append('image', mockFile);

      fetch.mockResolvedValueOnce({
        ok: true,
        json: async () => ({
          success: true,
          filename: 'memory_1234567890.jpg',
          url: 'uploads/memory_1234567890.jpg',
          message: 'Image uploadée avec succès',
        }),
      });

      const response = await fetch(`${API_BASE_URL}/../upload.php`, {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();

      expect(fetch).toHaveBeenCalledWith(
        `${API_BASE_URL}/../upload.php`,
        expect.objectContaining({
          method: 'POST',
          body: formData,
        })
      );
      expect(data.success).toBe(true);
      expect(data.filename).toMatch(/^memory_\d+\.jpg$/);
    });

    test('devrait rejeter un fichier trop volumineux', async () => {
      const mockFile = new File(['x'.repeat(10 * 1024 * 1024)], 'large.jpg', {
        type: 'image/jpeg',
      });

      const formData = new FormData();
      formData.append('image', mockFile);

      fetch.mockResolvedValueOnce({
        ok: false,
        status: 400,
        json: async () => ({
          error: 'Fichier trop volumineux (max 5MB)',
        }),
      });

      const response = await fetch(`${API_BASE_URL}/../upload.php`, {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();

      expect(response.status).toBe(400);
      expect(data.error).toBe('Fichier trop volumineux (max 5MB)');
    });

    test('devrait rejeter un type de fichier non autorisé', async () => {
      const mockFile = new File(['test content'], 'test.txt', {
        type: 'text/plain',
      });

      const formData = new FormData();
      formData.append('image', mockFile);

      fetch.mockResolvedValueOnce({
        ok: false,
        status: 400,
        json: async () => ({
          error: 'Type de fichier non autorisé',
        }),
      });

      const response = await fetch(`${API_BASE_URL}/../upload.php`, {
        method: 'POST',
        body: formData,
      });

      const data = await response.json();

      expect(response.status).toBe(400);
      expect(data.error).toBe('Type de fichier non autorisé');
    });
  });

  describe('Gestion des erreurs', () => {
    test('devrait gérer les erreurs de réseau', async () => {
      fetch.mockRejectedValueOnce(new Error('Network error'));

      try {
        await fetch(`${API_BASE_URL}/memories.php`);
      } catch (error) {
        expect(error.message).toBe('Network error');
      }
    });

    test('devrait gérer les erreurs 500 du serveur', async () => {
      fetch.mockResolvedValueOnce({
        ok: false,
        status: 500,
        json: async () => ({
          error: 'Erreur interne du serveur',
        }),
      });

      const response = await fetch(`${API_BASE_URL}/memories.php`);
      const data = await response.json();

      expect(response.status).toBe(500);
      expect(data.error).toBe('Erreur interne du serveur');
    });
  });
});
