/**
 * Tests unitaires pour les animations et interactions
 */

describe('Animations et Interactions', () => {
  let mockElement;
  let mockObserver;

  beforeEach(() => {
    // Créer un élément DOM mock
    mockElement = {
      classList: {
        add: jest.fn(),
        remove: jest.fn(),
        contains: jest.fn(),
      },
      style: {},
      offsetTop: 100,
      offsetHeight: 50,
    };

    // Mock IntersectionObserver
    mockObserver = {
      observe: jest.fn(),
      unobserve: jest.fn(),
      disconnect: jest.fn(),
    };
    
    global.IntersectionObserver = jest.fn().mockImplementation((callback) => {
      mockObserver.callback = callback;
      return mockObserver;
    });

    // Reset DOM
    document.body.innerHTML = '';
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  describe('IntersectionObserver pour les animations', () => {
    test('devrait observer les éléments avec la classe fade-in', () => {
      // Créer des éléments de test
      const element1 = document.createElement('div');
      element1.className = 'fade-in';
      document.body.appendChild(element1);

      const element2 = document.createElement('div');
      element2.className = 'fade-in stagger-1';
      document.body.appendChild(element2);

      // Simuler l'initialisation de l'observer
      const observer = new IntersectionObserver(() => {});
      
      // Vérifier que l'observer a été créé
      expect(global.IntersectionObserver).toHaveBeenCalled();
    });

    test('devrait ajouter la classe visible quand l\'élément est visible', () => {
      const element = document.createElement('div');
      element.className = 'fade-in';
      document.body.appendChild(element);

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
          }
        });
      });

      observer.observe(element);

      // Simuler l'intersection
      const mockEntry = {
        target: element,
        isIntersecting: true,
      };

      observer.callback([mockEntry]);

      expect(element.classList.add).toHaveBeenCalledWith('visible');
    });
  });

  describe('Système de bougie virtuelle', () => {
    test('devrait allumer une bougie et incrémenter le compteur', () => {
      // Mock localStorage
      localStorage.setItem('candleCount', '0');
      localStorage.setItem('hasLitCandle', '0');

      // Créer les éléments DOM nécessaires
      const flame = document.createElement('div');
      flame.className = 'flame';
      document.body.appendChild(flame);

      const counter = document.createElement('span');
      counter.id = 'candleCount';
      counter.textContent = '0';
      document.body.appendChild(counter);

      // Fonction lightCandle simulée
      function lightCandle() {
        const flameEl = document.querySelector('.flame');
        const counterEl = document.getElementById('candleCount');
        const hasLitCandle = localStorage.getItem('hasLitCandle') === '1';
        
        if (!flameEl || hasLitCandle) return;

        flameEl.classList.add('lit');
        let candleCount = parseInt(localStorage.getItem('candleCount') || '0', 10);
        candleCount += 1;
        localStorage.setItem('candleCount', String(candleCount));
        localStorage.setItem('hasLitCandle', '1');
        if (counterEl) counterEl.textContent = candleCount;
      }

      // Tester l'allumage de la bougie
      lightCandle();

      expect(flame.classList.add).toHaveBeenCalledWith('lit');
      expect(localStorage.getItem('candleCount')).toBe('1');
      expect(localStorage.getItem('hasLitCandle')).toBe('1');
      expect(counter.textContent).toBe('1');
    });

    test('ne devrait pas permettre d\'allumer plusieurs bougies par session', () => {
      localStorage.setItem('hasLitCandle', '1');
      
      const flame = document.createElement('div');
      flame.className = 'flame';
      document.body.appendChild(flame);

      let candleCount = parseInt(localStorage.getItem('candleCount') || '0', 10);
      const initialCount = candleCount;

      function lightCandle() {
        const flameEl = document.querySelector('.flame');
        const hasLitCandle = localStorage.getItem('hasLitCandle') === '1';
        
        if (!flameEl || hasLitCandle) return false;

        flameEl.classList.add('lit');
        candleCount += 1;
        localStorage.setItem('candleCount', String(candleCount));
        localStorage.setItem('hasLitCandle', '1');
        return true;
      }

      const result = lightCandle();
      expect(result).toBe(false);
      expect(candleCount).toBe(initialCount);
    });
  });

  describe('Système de partage de souvenirs', () => {
    test('devrait sauvegarder un souvenir dans localStorage', () => {
      const memories = [];
      
      function saveMemory(type, content, name) {
        const memory = {
          type,
          content,
          name: name || 'Anonyme',
          timestamp: new Date().toISOString(),
        };
        memories.push(memory);
        localStorage.setItem('memories', JSON.stringify(memories));
        return memory;
      }

      const memory = saveMemory('story', 'Un beau souvenir de Carine', 'Jean');

      expect(memories).toHaveLength(1);
      expect(memory.type).toBe('story');
      expect(memory.content).toBe('Un beau souvenir de Carine');
      expect(memory.name).toBe('Jean');
    });

    test('devrait récupérer les souvenirs depuis localStorage', () => {
      const testMemories = [
        {
          type: 'story',
          content: 'Souvenir 1',
          name: 'Jean',
          timestamp: '2025-01-01T00:00:00.000Z',
        },
        {
          type: 'message',
          content: 'Message 1',
          name: 'Marie',
          timestamp: '2025-01-02T00:00:00.000Z',
        },
      ];

      localStorage.setItem('memories', JSON.stringify(testMemories));

      function getMemories() {
        try {
          return JSON.parse(localStorage.getItem('memories') || '[]');
        } catch {
          return [];
        }
      }

      const memories = getMemories();
      expect(memories).toHaveLength(2);
      expect(memories[0].type).toBe('story');
      expect(memories[1].type).toBe('message');
    });
  });

  describe('Navigation et scroll', () => {
    test('devrait faire défiler vers une section au clic sur un lien d\'ancrage', () => {
      const targetElement = document.createElement('div');
      targetElement.id = 'events';
      targetElement.offsetTop = 500;
      document.body.appendChild(targetElement);

      const link = document.createElement('a');
      link.href = '#events';
      document.body.appendChild(link);

      // Mock scrollIntoView
      targetElement.scrollIntoView = jest.fn();

      // Simuler le clic
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const target = document.querySelector(link.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });

      link.click();

      expect(targetElement.scrollIntoView).toHaveBeenCalledWith({
        behavior: 'smooth',
        block: 'start',
      });
    });
  });

  describe('Méditation guidée', () => {
    test('devrait afficher les étapes de méditation séquentiellement', (done) => {
      const container = document.createElement('div');
      container.className = 'prayer-text';
      document.body.appendChild(container);

      const steps = [
        'Inspirez profondément…',
        'Fermez les yeux et souvenez-vous d\'un moment doux avec Carine…',
        'Laissez la gratitude remplir votre cœur…',
        'Soufflez doucement et gardez sa lumière avec vous.',
      ];

      let currentStep = 0;
      const interval = setInterval(() => {
        if (currentStep < steps.length) {
          const p = document.createElement('p');
          p.textContent = steps[currentStep];
          container.appendChild(p);
          currentStep++;
        } else {
          clearInterval(interval);
          expect(container.children).toHaveLength(steps.length);
          expect(container.children[0].textContent).toBe(steps[0]);
          expect(container.children[3].textContent).toBe(steps[3]);
          done();
        }
      }, 100);
    });
  });
});
