/**
 * Tests end-to-end avec Cypress
 */

describe('Mémorial Carine SIASSIA - Tests E2E', () => {
  beforeEach(() => {
    // Visiter la page d'accueil
    cy.visit('/');
    
    // Attendre que la page soit chargée
    cy.get('body').should('be.visible');
  });

  describe('Page d\'accueil', () => {
    it('devrait afficher le titre principal', () => {
      cy.get('h1').should('contain', 'Programme complet des funérailles de Carine SIASSIA');
    });

    it('devrait afficher la citation', () => {
      cy.get('.quote').should('contain', 'Quand une étoile s\'éteint');
    });

    it('devrait avoir un design responsive', () => {
      // Test sur desktop
      cy.viewport(1200, 800);
      cy.get('.hero-content').should('be.visible');
      
      // Test sur mobile
      cy.viewport(375, 667);
      cy.get('.hero-content').should('be.visible');
    });
  });

  describe('Galerie de photos', () => {
    it('devrait afficher la galerie de souvenirs', () => {
      cy.get('.photo-gallery').should('be.visible');
      cy.get('.photo-grid').should('be.visible');
      cy.get('.photo-item').should('have.length.at.least', 3);
    });

    it('devrait afficher les overlays au survol', () => {
      cy.get('.photo-item').first().trigger('mouseover');
      cy.get('.photo-overlay').first().should('be.visible');
    });
  });

  describe('Programme des événements', () => {
    it('devrait afficher les événements', () => {
      cy.get('.events-grid').should('be.visible');
      cy.get('.event-card').should('have.length.at.least', 2);
    });

    it('devrait afficher les détails des événements', () => {
      cy.get('.event-card').first().should('contain', 'Veillée des Souvenirs');
      cy.get('.event-card').eq(1).should('contain', 'Journée d\'Adieu');
    });

    it('devrait afficher la timeline', () => {
      cy.get('.timeline').should('be.visible');
      cy.get('.timeline-item').should('have.length.at.least', 5);
    });
  });

  describe('Système de bougie virtuelle', () => {
    it('devrait afficher la section mémorial virtuel', () => {
      cy.get('.virtual-memorial').should('be.visible');
      cy.get('.candle-container').should('be.visible');
    });

    it('devrait permettre d\'allumer une bougie', () => {
      // Vérifier l'état initial
      cy.get('.flame').should('not.have.class', 'lit');
      
      // Cliquer pour allumer la bougie
      cy.get('.candle-container').click();
      
      // Vérifier que la bougie est allumée
      cy.get('.flame').should('have.class', 'lit');
      
      // Vérifier que le compteur s'incrémente
      cy.get('#candleCount').should('not.contain', '247');
    });

    it('ne devrait pas permettre d\'allumer plusieurs bougies', () => {
      // Allumer une première bougie
      cy.get('.candle-container').click();
      cy.get('.flame').should('have.class', 'lit');
      
      // Essayer d'allumer une deuxième bougie
      cy.get('.candle-container').click();
      
      // La flamme devrait rester allumée (pas de changement)
      cy.get('.flame').should('have.class', 'lit');
    });
  });

  describe('Partage de souvenirs', () => {
    it('devrait ouvrir le modal de partage', () => {
      cy.get('button').contains('Partager une anecdote').click();
      cy.get('#memoryModal').should('be.visible');
      cy.get('#modalTitle').should('contain', 'Partager une anecdote');
    });

    it('devrait permettre de partager un souvenir', () => {
      // Ouvrir le modal
      cy.get('button').contains('Partager une anecdote').click();
      
      // Remplir le formulaire
      cy.get('#memText').type('Un beau souvenir de Carine');
      cy.get('#memName').type('Jean Dupont');
      
      // Soumettre
      cy.get('#memSubmit').click();
      
      // Vérifier que le modal se ferme
      cy.get('#memoryModal').should('not.be.visible');
    });

    it('devrait valider les champs requis', () => {
      cy.get('button').contains('Partager une anecdote').click();
      
      // Essayer de soumettre sans contenu
      cy.get('#memSubmit').click();
      
      // Le modal devrait rester ouvert
      cy.get('#memoryModal').should('be.visible');
    });
  });

  describe('Méditation guidée', () => {
    it('devrait démarrer la méditation', () => {
      cy.get('.meditation-btn').click();
      
      // Vérifier que le bouton change d'état
      cy.get('.meditation-btn').should('contain', 'Méditation en cours');
      
      // Attendre que la méditation se termine
      cy.wait(10000); // 4 étapes × 2.2s + marge
      
      // Vérifier que le bouton revient à l'état normal
      cy.get('.meditation-btn').should('contain', 'Moment de méditation');
    });
  });

  describe('Navigation et scroll', () => {
    it('devrait naviguer vers les événements', () => {
      cy.get('a[href="#events"]').click();
      
      // Vérifier que la page a scrollé vers la section événements
      cy.get('#events').should('be.visible');
    });

    it('devrait afficher l\'indicateur de scroll', () => {
      cy.get('.scroll-indicator').should('be.visible');
      cy.get('.scroll-indicator i').should('have.class', 'fa-chevron-down');
    });
  });

  describe('Partage social', () => {
    it('devrait afficher les boutons de partage', () => {
      cy.get('.share-buttons').should('be.visible');
      cy.get('.share-btn.facebook').should('be.visible');
      cy.get('.share-btn.whatsapp').should('be.visible');
      cy.get('.share-btn.email').should('be.visible');
      cy.get('.share-btn.copy').should('be.visible');
    });

    it('devrait copier le lien au clic', () => {
      // Mock de l'API Clipboard
      cy.window().then((win) => {
        cy.stub(win.navigator.clipboard, 'writeText').resolves();
      });
      
      cy.get('.share-btn.copy').click();
      
      // Vérifier que le lien a été copié
      cy.window().its('navigator.clipboard.writeText').should('have.been.called');
    });
  });

  describe('Accessibilité', () => {
    it('devrait avoir des titres hiérarchisés', () => {
      cy.get('h1').should('exist');
      cy.get('h2').should('have.length.at.least', 3);
    });

    it('devrait avoir des attributs alt sur les images', () => {
      cy.get('img').each(($img) => {
        cy.wrap($img).should('have.attr', 'alt');
      });
    });

    it('devrait être navigable au clavier', () => {
      // Tester la navigation au clavier
      cy.get('body').tab();
      cy.focused().should('be.visible');
    });
  });

  describe('Performance', () => {
    it('devrait charger rapidement', () => {
      cy.visit('/', {
        onBeforeLoad: (win) => {
          win.performance.mark('start');
        },
      });
      
      cy.window().then((win) => {
        win.performance.mark('end');
        win.performance.measure('loadTime', 'start', 'end');
        
        const measure = win.performance.getEntriesByName('loadTime')[0];
        expect(measure.duration).to.be.lessThan(3000); // Moins de 3 secondes
      });
    });

    it('devrait avoir un bon score Lighthouse', () => {
      cy.lighthouse({
        performance: 80,
        accessibility: 90,
        'best-practices': 80,
        seo: 90,
      });
    });
  });

  describe('Responsive Design', () => {
    const viewports = [
      { device: 'iPhone 5', width: 320, height: 568 },
      { device: 'iPhone X', width: 375, height: 812 },
      { device: 'iPad', width: 768, height: 1024 },
      { device: 'Desktop', width: 1200, height: 800 },
    ];

    viewports.forEach(({ device, width, height }) => {
      it(`devrait s'afficher correctement sur ${device}`, () => {
        cy.viewport(width, height);
        cy.visit('/');
        
        // Vérifier que les éléments principaux sont visibles
        cy.get('.hero-content').should('be.visible');
        cy.get('.photo-gallery').should('be.visible');
        cy.get('.events-grid').should('be.visible');
        
        // Vérifier qu'il n'y a pas de débordement horizontal
        cy.get('body').should('not.have.css', 'overflow-x', 'scroll');
      });
    });
  });
});
