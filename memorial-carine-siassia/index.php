<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✨ Carine SIASSIA - Une Étoile Qui Continue de Briller</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            --secondary-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            --dark-overlay: rgba(0, 0, 0, 0.7);
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.25);
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --accent-color: #667eea;
            --soft-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        /* Background with parallax effect */
        .hero-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: var(--primary-gradient);
            z-index: -2;
        }

        .hero-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            animation: shimmer 20s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .scroll-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, 
                transparent 0%, 
                rgba(247, 250, 252, 0.8) 30%, 
                rgba(247, 250, 252, 0.95) 70%);
            z-index: -1;
            transition: opacity 0.3s ease;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Enhanced Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            animation: heroFloat 6s ease-in-out infinite;
        }

        @keyframes heroFloat {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.02); }
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #fff 0%, #f093fb 50%, #ffecd2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -3px;
            text-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            animation: textGlow 3s ease-in-out infinite alternate;
        }

        @keyframes textGlow {
            from { filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)); }
            to { filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.6)); }
        }

        .hero .subtitle {
            font-size: 1.6rem;
            font-weight: 300;
            margin-bottom: 30px;
            opacity: 0.9;
            animation: fadeSlideUp 1s ease 0.5s both;
        }

        .hero .quote {
            font-size: 1.3rem;
            font-style: italic;
            margin-bottom: 40px;
            padding: 25px 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            animation: fadeSlideUp 1s ease 1s both;
        }

        .hero .quote::before, .hero .quote::after {
            content: '"';
            font-size: 4rem;
            font-family: 'Playfair Display', serif;
            position: absolute;
            color: rgba(255, 255, 255, 0.3);
        }

        .hero .quote::before {
            top: -10px;
            left: 10px;
        }

        .hero .quote::after {
            bottom: -40px;
            right: 10px;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            animation: bounce 2s infinite;
        }

        .scroll-indicator i {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.7);
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
            40% { transform: translateX(-50%) translateY(-10px); }
            60% { transform: translateX(-50%) translateY(-5px); }
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Main Content Section */
        .main-content {
            position: relative;
            background: rgba(247, 250, 252, 0.95);
            padding: 80px 0;
            margin-top: -100vh;
            z-index: 1;
        }

        /* Photo Gallery Section */
        .photo-gallery {
            margin: 60px 0;
            padding: 60px 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
            backdrop-filter: blur(20px);
            border-radius: 30px;
            box-shadow: var(--soft-shadow);
        }

        .photo-gallery h2 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 50px;
            color: var(--text-primary);
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .photo-item {
            position: relative;
            height: 300px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--soft-shadow);
            transition: all 0.4s ease;
            background: linear-gradient(45deg, #667eea, #764ba2);
        }

        .photo-item:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: var(--hover-shadow);
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-color), #764ba2);
            color: white;
            font-size: 4rem;
            transition: all 0.3s ease;
        }

        .photo-item:hover .photo-placeholder {
            transform: scale(1.1);
        }

        .photo-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .photo-item:hover .photo-overlay {
            transform: translateY(0);
        }

        /* Enhanced Cards */
        .memory-invitation, .event-card, .timeline, .contact-section {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 50px;
            margin: 40px 0;
            box-shadow: var(--soft-shadow);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .memory-invitation::before, .event-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: rotate 20s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .memory-invitation:hover, .event-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .memory-invitation {
            text-align: center;
        }

        .memory-invitation h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 25px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta-button {
            display: inline-block;
            background: var(--primary-gradient);
            color: white;
            padding: 18px 45px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        .cta-button:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
        }

        /* Enhanced Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(600px, 1fr));
            gap: 40px;
            margin: 60px 0;
        }

        .event-card .icon {
            font-size: 4rem;
            margin-bottom: 25px;
            display: block;
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .event-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .event-card .date {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        .event-card .location {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            border-left: 4px solid var(--accent-color);
            position: relative;
        }

        .event-card .location::before {
            content: '📍';
            position: absolute;
            top: 20px;
            left: -15px;
            background: white;
            padding: 5px;
            border-radius: 50%;
            font-size: 1.2rem;
        }

        /* Enhanced Timeline */
        .timeline h2 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 50px;
            color: var(--text-primary);
        }

        .timeline-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border-radius: 20px;
            border-left: 4px solid var(--accent-color);
            transition: all 0.3s ease;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 50%;
            width: 16px;
            height: 16px;
            background: var(--accent-color);
            border-radius: 50%;
            transform: translateY(-50%);
            box-shadow: 0 0 0 4px white;
        }

        .timeline-item:hover {
            transform: translateX(10px);
            box-shadow: var(--soft-shadow);
        }

        /* Enhanced Contact Cards */
        .contacts {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }

        .contact-card {
            background: var(--primary-gradient);
            color: white;
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .contact-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent 70%);
            transform: scale(0);
            transition: transform 0.4s ease;
        }

        .contact-card:hover::before {
            transform: scale(1);
        }

        .contact-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.4);
        }

        .contact-card .avatar {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        /* Floating Elements Enhancement */
        .floating-stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .star {
            position: absolute;
            color: rgba(255, 255, 255, 0.6);
            animation: twinkle 3s ease-in-out infinite;
        }

        @keyframes twinkle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
                letter-spacing: -1px;
            }
            
            .hero-content {
                padding: 40px 25px;
                margin: 0 20px;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .memory-invitation, .event-card, .timeline, .contact-section {
                padding: 30px 25px;
                margin: 30px 0;
            }
            
            .photo-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .timeline-item {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Loading Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .stagger-1 { transition-delay: 0.1s; }
        .stagger-2 { transition-delay: 0.2s; }
        .stagger-3 { transition-delay: 0.3s; }
        .stagger-4 { transition-delay: 0.4s; }
    </style>
</head>
<body>
    <div class="hero-background"></div>
    <div class="scroll-background"></div>
    <div class="floating-stars"></div>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>✨ Carine SIASSIA</h1>
            <p class="subtitle">Une Étoile Qui Continue de Briller</p>
            <div class="quote">
                Quand une étoile s'éteint, elle continue de briller dans nos cœurs...
            </div>
        </div>
        <div class="scroll-indicator">
            <i class="fas fa-chevron-down"></i>
        </div>
    </section>

    <div class="main-content">
        <div class="container">
            <!-- Photo Gallery Section -->
            <section class="photo-gallery fade-in">
                <h2><i class="fas fa-camera-retro"></i> Galerie de Souvenirs</h2>
                <div class="photo-grid">
                    <div class="photo-item">
                        <div class="photo-placeholder">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="photo-overlay">
                            <h4>Son sourire radieux</h4>
                            <p>Qui illuminait chaque journée</p>
                        </div>
                    </div>
                    <div class="photo-item">
                        <div class="photo-placeholder">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="photo-overlay">
                            <h4>Moments en famille</h4>
                            <p>Des instants précieux partagés</p>
                        </div>
                    </div>
                    <div class="photo-item">
                        <div class="photo-placeholder">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="photo-overlay">
                            <h4>Célébrations joyeuses</h4>
                            <p>Ses rires résonnent encore</p>
                        </div>
                    </div>
                    <div class="photo-item">
                        <div class="photo-placeholder">
                            <i class="fas fa-flower"></i>
                        </div>
                        <div class="photo-overlay">
                            <h4>Sa beauté naturelle</h4>
                            <p>Intérieure et extérieure</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Memory Invitation -->
            <section class="memory-invitation fade-in stagger-1">
                <h2><i class="fas fa-star"></i> Célébrons Sa Mémoire Ensemble</h2>
                <p>
                    Notre chère Carine nous a quittés, mais son sourire, sa bienveillance et ses éclats de rire résonnent encore. 
                    Rejoignez-nous pour honorer sa mémoire et partager nos plus beaux souvenirs d'elle.
                </p>
                <p style="margin-top: 20px; font-style: italic; color: var(--text-secondary);">
                    <i class="fas fa-quote-left"></i> 
                    Elle aurait voulu que nous gardions le sourire et que nous célébrions la vie qu'elle a si bien vécue.
                    <i class="fas fa-quote-right"></i>
                </p>
                <a href="#events" class="cta-button">
                    <i class="fas fa-calendar-alt"></i> Découvrir les événements
                </a>
            </section>

            <!-- Events Section -->
            <div id="events" class="events-grid">
                <div class="event-card fade-in stagger-2">
                    <span class="icon">🌙</span>
                    <h2>Veillée des Souvenirs</h2>
                    <div class="date"><i class="fas fa-calendar"></i> Samedi 30 Août 2025</div>
                    <div class="time"><i class="fas fa-clock"></i> À partir de 21h00</div>
                    <div class="location">
                        <strong><i class="fas fa-map-marker-alt"></i> 12, Rue Jules Guesdes</strong><br>
                        91130 Ris-Orangis
                    </div>
                    <div class="description">
                        <p>Venez avec vos photos, vos anecdotes, vos playlists... Faisons de cette soirée un vrai hommage à la femme extraordinaire qu'elle était !</p>
                        <p style="margin-top: 15px; font-weight: 600; color: var(--accent-color);">
                            <i class="fas fa-smile"></i> Pas de larmes, que des sourires et des bons souvenirs !
                        </p>
                    </div>
                    <div style="margin-top: 25px; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 15px;">
                        <h4><i class="fas fa-bus"></i> Comment venir :</h4>
                        <ul style="list-style: none; margin-top: 10px;">
                            <li><i class="fas fa-train"></i> Train : Gare Orangis Bois de l'Épine</li>
                            <li><i class="fas fa-bus"></i> Bus : Ligne 4042, arrêt Jean Mermoz</li>
                            <li><i class="fas fa-subway"></i> Tram : Ligne T12, arrêt Traité de Rome</li>
                        </ul>
                    </div>
                </div>

                <div class="event-card fade-in stagger-3">
                    <span class="icon">🌅</span>
                    <h2>Journée d'Adieu & de Gratitude</h2>
                    <div class="date"><i class="fas fa-calendar"></i> Lundi 1er Septembre 2025</div>
                    <div class="time"><i class="fas fa-sun"></i> Journée complète</div>
                    <div class="location">
                        <strong><i class="fas fa-route"></i> Parcours Fontainebleau → Melun</strong>
                    </div>
                    <div class="description">
                        <p>Un parcours en plusieurs étapes pour dire merci à Carine. Une journée de recueillement, de partage et de célébration de sa belle vie.</p>
                        <p style="margin-top: 15px; font-style: italic;">
                            <i class="fas fa-heart"></i> Ensemble, nous transformerons cette journée difficile en un bel hommage.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <section class="timeline fade-in stagger-4">
                <h2><i class="fas fa-clock"></i> Programme du 1er Septembre</h2>
                
                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 9h30 - 10h30</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-home"></i> Les Derniers Préparatifs</h3>
                        <p><strong>Maison Funéraire de l'Hôpital de Fontainebleau</strong><br>
                        55, Boulevard du Maréchal Joffre, 77300 Fontainebleau</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 10h45</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-car"></i> Départ vers l'Église</h3>
                        <p>Direction Melun pour le recueillement</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 11h00</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-church"></i> Recueillement & Témoignages</h3>
                        <p><strong>Église Protestante Unie</strong><br>
                        8, Avenue Thiers, 77000 Melun</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 13h00</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-praying-hands"></i> Cérémonie d'Adieu</h3>
                        <p>Messe en l'honneur de Carine</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 14h10</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-leaf"></i> Départ vers le Repos Éternel</h3>
                        <p><strong>Cimetière Nord de Melun</strong><br>
                        2, Rue des Mazereaux, 77000 Melun</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 15h10</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-wine-glass"></i> Moment de Convivialité</h3>
                        <p>Retour à l'église - On partage nos souvenirs autour d'un verre</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-time"><i class="fas fa-clock"></i> 16h00</div>
                    <div class="timeline-content">
                        <h3><i class="fas fa-heart-broken"></i> Clôture de la Cérémonie</h3>
                        <p>Fin de cette journée d'hommage</p>
                    </div>
                </div>
            </section>

            <!-- Testimonials Section -->
            <section class="testimonials fade-in stagger-1">
                <h2><i class="fas fa-comments"></i> Témoignages & Souvenirs</h2>
                <div class="testimonial-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <blockquote>
                            "Carine avait cette capacité unique de transformer chaque moment ordinaire en souvenir extraordinaire. Son rire était contagieux et son cœur, immense."
                        </blockquote>
                        <cite>— Un proche</cite>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-avatar">
                            <i class="fas fa-heart"></i>
                        </div>
                        <blockquote>
                            "Elle nous a appris que la beauté de la vie réside dans les petites attentions, les gestes tendres et les sourires partagés."
                        </blockquote>
                        <cite>— Sa famille</cite>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-avatar">
                            <i class="fas fa-star"></i>
                        </div>
                        <blockquote>
                            "Carine était une lumière dans nos vies. Même dans les moments difficiles, elle trouvait toujours le moyen de nous faire sourire."
                        </blockquote>
                        <cite>— Ses amis</cite>
                    </div>
                </div>
                
                <div class="memory-sharing">
                    <h3><i class="fas fa-pen-fancy"></i> Partagez vos souvenirs</h3>
                    <p>Vous avez des anecdotes, des photos ou des messages à partager ? Contactez-nous pour que nous puissions les ajouter à cette page de souvenirs.</p>
                    <div class="memory-actions">
                        <button class="memory-btn" onclick="openMemoryModal('story')">
                            <i class="fas fa-book"></i> Partager une anecdote
                        </button>
                        <button class="memory-btn" onclick="openMemoryModal('photo')">
                            <i class="fas fa-image"></i> Envoyer une photo
                        </button>
                        <button class="memory-btn" onclick="openMemoryModal('message')">
                            <i class="fas fa-envelope"></i> Laisser un message
                        </button>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section class="contact-section fade-in stagger-2">
                <h2><i class="fas fa-address-book"></i> Organisateurs & Contacts</h2>
                <div class="contacts">
                    <div class="contact-card">
                        <div class="avatar">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>💙 Alphée Mathurin NZONZI</h3>
                        <p>Son époux du cœur</p>
                        <div class="phone">
                            <i class="fas fa-phone"></i> 
                            <a href="tel:0660983288">06 60 98 32 88</a>
                        </div>
                        <p style="font-size: 0.9rem; margin-top: 10px; opacity: 0.8;">
                            Pour toute question concernant les cérémonies
                        </p>
                    </div>
                    
                    <div class="contact-card">
                        <div class="avatar">
                            <i class="fas fa-hands-helping"></i>
                        </div>
                        <h3>🤝 Gaspard Tatiana MALOKO</h3>
                        <p>Organisation & coordination</p>
                        <div class="phone">
                            <i class="fas fa-phone"></i> 
                            <a href="tel:0666583549">06 66 58 35 49</a>
                        </div>
                        <p style="font-size: 0.9rem; margin-top: 10px; opacity: 0.8;">
                            Coordination des événements et logistique
                        </p>
                    </div>
                    
                    <div class="contact-card">
                        <div class="avatar">
                            <i class="fas fa-camera"></i>
                        </div>
                        <h3>📸 BAVOTA Bavon Photo</h3>
                        <p>Souvenirs en images</p>
                        <div class="phone">
                            <i class="fas fa-phone"></i> 
                            <a href="tel:0627567488">06 27 56 74 88</a>
                        </div>
                        <p style="font-size: 0.9rem; margin-top: 10px; opacity: 0.8;">
                            Immortaliser ces moments précieux
                        </p>
                    </div>
                </div>
                
                <div class="emergency-contact">
                    <h3><i class="fas fa-exclamation-triangle"></i> Informations importantes</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-parking"></i>
                            <h4>Stationnement</h4>
                            <p>Places disponibles près de l'église et du cimetière</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-wheelchair"></i>
                            <h4>Accessibilité</h4>
                            <p>Tous les lieux sont accessibles aux personnes à mobilité réduite</p>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-utensils"></i>
                            <h4>Restauration</h4>
                            <p>Collation offerte lors du moment de convivialité</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Virtual Memorial -->
            <section class="virtual-memorial fade-in stagger-3">
                <h2><i class="fas fa-infinity"></i> Mémorial Virtuel</h2>
                <p class="memorial-intro">
                    Un espace dédié pour allumer une bougie virtuelle, laisser un message ou simplement se recueillir en pensant à Carine.
                </p>
                
                <div class="virtual-actions">
                    <div class="virtual-candle">
                        <div class="candle-container" onclick="lightCandle()">
                            <div class="candle">
                                <div class="flame"></div>
                                <div class="wick"></div>
                                <div class="wax"></div>
                            </div>
                        </div>
                        <p>Cliquez pour allumer une bougie</p>
                        <div class="candle-counter">
                            <span id="candleCount">247</span> bougies allumées
                        </div>
                    </div>
                    
                    <div class="prayer-space">
                        <h3><i class="fas fa-dove"></i> Espace de Recueillement</h3>
                        <div class="prayer-text">
                            <p><em>"Que la paix soit avec toi, chère Carine. Tes rires continuent de résonner dans nos cœurs et ta lumière guide nos pas."</em></p>
                        </div>
                        <button class="meditation-btn" onclick="startMeditation()">
                            <i class="fas fa-leaf"></i> Moment de méditation
                        </button>
                    </div>
                </div>
            </section>

            <!-- Final Message -->
            <footer class="memory-invitation fade-in stagger-4">
                <h2><i class="fas fa-hands-praying"></i> Merci</h2>
                <div class="final-message">
                    <p>
                        Votre présence et votre soutien en cette période difficile sont précieux pour nous tous. 
                        Ensemble, nous garderons vivante la mémoire de notre chère Carine.
                    </p>
                    
                    <div class="quote-section">
                        <div class="main-quote">
                            <i class="fas fa-quote-left"></i>
                            <p>Elle aurait dit : "Pas de larmes, que des sourires et des bons souvenirs !"</p>
                            <i class="fas fa-quote-right"></i>
                        </div>
                        
                        <div class="sub-quotes">
                            <p><i class="fas fa-star"></i> <em>"Une étoile ne disparaît jamais, elle devient éternelle."</em></p>
                            <p><i class="fas fa-heart"></i> <em>"L'amour que nous portons aux autres ne meurt jamais."</em></p>
                            <p><i class="fas fa-infinity"></i> <em>"Dans nos cœurs, tu vivras pour l'éternité."</em></p>
                        </div>
                    </div>
                    
                    <div class="social-sharing">
                        <h3><i class="fas fa-share-alt"></i> Partagez cette page</h3>
                        <div class="share-buttons">
                            <button onclick="shareOnFacebook()" class="share-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button onclick="shareOnWhatsApp()" class="share-btn whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button onclick="shareByEmail()" class="share-btn email">
                                <i class="fas fa-envelope"></i>
                            </button>
                            <button onclick="copyLink()" class="share-btn copy">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="memorial-footer">
                    <p style="opacity: 0.7; font-size: 0.9rem;">
                        <i class="fas fa-calendar-alt"></i> Créé avec amour en août 2025 • 
                        <i class="fas fa-heart"></i> En mémoire de Carine SIASSIA •
                        <i class="fas fa-star"></i> Pour l'éternité
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Modal for memory sharing -->
    <div id="memoryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- Additional Styles -->
    <style>
        /* Testimonials Styles */
        .testimonials {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 50px;
            margin: 40px 0;
            box-shadow: var(--soft-shadow);
        }

        .testimonials h2 {
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            margin-bottom: 40px;
            color: var(--text-primary);
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .testimonial-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(255, 255, 255, 0.8));
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .testimonial-avatar {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .testimonial-card blockquote {
            font-style: italic;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
            color: var(--text-secondary);
        }

        .testimonial-card cite {
            font-weight: 600;
            color: var(--accent-color);
        }

        /* Memory Sharing Styles */
        .memory-sharing {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(255, 255, 255, 0.9));
            padding: 40px;
            border-radius: 20px;
            text-align: center;
        }

        .memory-sharing h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .memory-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .memory-btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .memory-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        /* Contact Section Enhancement */
        .contact-card .phone a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .emergency-contact {
            margin-top: 50px;
            padding-top: 40px;
            border-top: 2px solid rgba(102, 126, 234, 0.2);
        }

        .emergency-contact h3 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--text-primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            text-align: center;
            padding: 20px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
        }

        .info-item i {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 10px;
        }

        /* Virtual Memorial Styles */
        .virtual-memorial {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(255, 255, 255, 0.95));
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 50px;
            margin: 40px 0;
            text-align: center;
        }

        .virtual-memorial h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.8rem;
            margin-bottom: 30px;
        }

        .memorial-intro {
            font-size: 1.2rem;
            margin-bottom: 40px;
            color: var(--text-secondary);
        }

        .virtual-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
        }

        /* Virtual Candle Styles */
        .virtual-candle {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .candle-container {
            cursor: pointer;
            margin-bottom: 20px;
        }

        .candle {
            position: relative;
            width: 30px;
            height: 100px;
            margin: 0 auto;
        }

        .flame {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 30px;
            background: radial-gradient(circle, #ff6b35 30%, #f7931e 70%);
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            opacity: 0;
            animation: flicker 2s ease-in-out infinite alternate;
            transition: opacity 0.3s ease;
        }

        .flame.lit {
            opacity: 1;
        }

        @keyframes flicker {
            0% { transform: translateX(-50%) rotate(-2deg) scale(1); }
            100% { transform: translateX(-50%) rotate(2deg) scale(1.05); }
        }

        .wick {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 10px;
            background: #333;
            border-radius: 1px;
        }

        .wax {
            width: 30px;
            height: 90px;
            background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
            border-radius: 3px;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .candle-counter {
            font-size: 1.1rem;
            color: var(--accent-color);
            font-weight: 600;
        }

        /* Prayer Space Styles */
        .prayer-space {
            padding: 30px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
        }

        .prayer-text {
            margin: 20px 0;
            padding: 20px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            border-left: 4px solid var(--accent-color);
        }

        .meditation-btn {
            background: var(--secondary-gradient);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .meditation-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(252, 182, 159, 0.4);
        }

        /* Final Message Styles */
        .final-message {
            text-align: center;
        }

        .quote-section {
            margin: 40px 0;
        }

        .main-quote {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 30px;
            position: relative;
        }

        .main-quote i {
            font-size: 2rem;
            opacity: 0.3;
        }

        .sub-quotes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .sub-quotes p {
            padding: 15px;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 15px;
            font-size: 1rem;
        }

        /* Social Sharing Styles */
        .social-sharing {
            margin: 40px 0;
        }

        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .share-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .share-btn.facebook { background: #3b5998; }
        .share-btn.whatsapp { background: #25d366; }
        .share-btn.email { background: #ea4335; }
        .share-btn.copy { background: #6c757d; }

        .share-btn:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .memorial-footer {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(102, 126, 234, 0.2);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 2rem;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: #000;
        }
    </style>

        <script>
/* =========================
   Effets d’apparition (IntersectionObserver)
   ========================= */
const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -100px 0px' };
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
}, observerOptions);
document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

/* =========================
   Parallaxe et décor du Hero
   ========================= */
const heroBackground = document.querySelector('.hero-background');
const scrollBackground = document.querySelector('.scroll-background');
function onScrollDecor() {
  const scrolled = window.pageYOffset || document.documentElement.scrollTop;
  if (heroBackground) heroBackground.style.transform = `translateY(${scrolled * 0.3}px)`;
  if (scrollBackground) scrollBackground.style.transform = `translateY(${scrolled * 0.15}px)`;
}
window.addEventListener('scroll', onScrollDecor);

// petites étoiles flottantes
function createFloatingStar() {
  const star = document.createElement('div');
  star.className = 'floating-star';
  star.textContent = '✦';
  star.style.left = Math.random() * 100 + '%';
  star.style.animationDuration = (6 + Math.random() * 6) + 's';
  document.body.appendChild(star);
  setTimeout(() => star.remove(), 14000);
}
for (let i = 0; i < 16; i++) setTimeout(createFloatingStar, i * 400);

/* =========================
   Défilement doux des ancres
   ========================= */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', (e) => {
    const target = document.querySelector(a.getAttribute('href'));
    if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
  });
});

/* =========================
   Bougie du mémorial (persistant)
   ========================= */
let candleCount = parseInt(localStorage.getItem('candleCount') || '0', 10);
let hasLitCandle = localStorage.getItem('hasLitCandle') === '1';
const counterEl = document.getElementById('candleCount');
if (counterEl) counterEl.textContent = candleCount;

function createSparkles() {
  // keyframes pour l’effet si absent
  if (!document.getElementById('sparkleKeyframes')) {
    const sparkleStyle = document.createElement('style');
    sparkleStyle.id = 'sparkleKeyframes';
    sparkleStyle.textContent = `
      @keyframes sparkleFloat {
        0% { opacity: 0; transform: translateY(0) scale(0.8); }
        30% { opacity: 1; }
        100% { opacity: 0; transform: translateY(-60px) scale(1.2); }
      }
    `;
    document.head.appendChild(sparkleStyle);
  }

  const candle = document.querySelector('.candle');
  if (!candle) return;

  for (let i = 0; i < 10; i++) {
    setTimeout(() => {
      const sparkle = document.createElement('div');
      sparkle.innerHTML = '✨';
      sparkle.style.position = 'absolute';
      sparkle.style.left = (10 + Math.random() * 60) + 'px';
      sparkle.style.top = (10 + Math.random() * 60) + 'px';
      sparkle.style.fontSize = '1rem';
      sparkle.style.pointerEvents = 'none';
      sparkle.style.animation = 'sparkleFloat 1.8s ease-out forwards';
      candle.appendChild(sparkle);
      setTimeout(() => sparkle.remove(), 1900);
    }, i * 80);
  }
}

function lightCandle() {
  const flame = document.querySelector('.flame');
  if (!flame || hasLitCandle) return;

  flame.classList.add('lit');
  candleCount += 1;
  hasLitCandle = true;
  localStorage.setItem('candleCount', String(candleCount));
  localStorage.setItem('hasLitCandle', '1');
  if (counterEl) counterEl.textContent = candleCount;
  createSparkles();
}

/* =========================
   Modal "Partage de souvenirs"
   ========================= */
const modal = document.getElementById('memoryModal');
const modalTitle = document.getElementById('modalTitle');
const modalBody = document.getElementById('modalBody');
const modalClose = modal ? modal.querySelector('.close') : null;

function openMemoryModal(type) {
  if (!modal || !modalTitle || !modalBody) return;

  let inner = '';
  if (type === 'story') {
    modalTitle.innerHTML = '<i class="fas fa-book"></i> Partager une anecdote';
    inner = `
      <label style="display:block;margin:8px 0 4px;">Votre anecdote</label>
      <textarea id="memText" rows="5" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;"></textarea>
      <label style="display:block;margin:14px 0 4px;">Votre nom (optionnel)</label>
      <input id="memName" type="text" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;">
      <button id="memSubmit" class="cta-button" style="margin-top:14px;">Envoyer</button>
    `;
  } else if (type === 'photo') {
    modalTitle.innerHTML = '<i class="fas fa-image"></i> Envoyer une photo';
    inner = `
      <label style="display:block;margin:8px 0 4px;">Choisir une image</label>
      <input id="memFile" type="file" accept="image/*" style="width:100%;padding:10px;border-radius:10px;border:1px solid #ddd;">
      <label style="display:block;margin:14px 0 4px;">Description (optionnel)</label>
      <textarea id="memText" rows="3" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;"></textarea>
      <label style="display:block;margin:14px 0 4px;">Votre nom (optionnel)</label>
      <input id="memName" type="text" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;">
      <button id="memSubmit" class="cta-button" style="margin-top:14px;">Envoyer</button>
    `;
  } else {
    modalTitle.innerHTML = '<i class="fas fa-envelope"></i> Laisser un message';
    inner = `
      <label style="display:block;margin:8px 0 4px;">Votre message</label>
      <textarea id="memText" rows="4" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;"></textarea>
      <label style="display:block;margin:14px 0 4px;">Votre nom (optionnel)</label>
      <input id="memName" type="text" style="width:100%;padding:12px;border-radius:10px;border:1px solid #ddd;">
      <button id="memSubmit" class="cta-button" style="margin-top:14px;">Envoyer</button>
    `;
  }

  modalBody.innerHTML = inner;
  modal.style.display = 'block';

  const btn = document.getElementById('memSubmit');
  if (btn) btn.onclick = () => submitMemory(type);
}

function closeMemoryModal() {
  if (!modal) return;
  modal.style.display = 'none';
  modalBody.innerHTML = '';
}
if (modalClose) modalClose.addEventListener('click', closeMemoryModal);
window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMemoryModal(); });
window.addEventListener('click', (e) => { if (e.target === modal) closeMemoryModal(); });

/* =========================
   Stockage & rendu des souvenirs (localStorage)
   ========================= */
function getMemories() {
  try { return JSON.parse(localStorage.getItem('memories') || '[]'); }
  catch { return []; }
}
function saveMemories(list) { localStorage.setItem('memories', JSON.stringify(list)); }

function renderMemories() {
  const grid = document.querySelector('.testimonial-grid');
  if (!grid) return;

  // Crée (ou récupère) un sous-conteneur pour les contributions des visiteurs
  let userGrid = document.getElementById('userMemoriesGrid');
  if (!userGrid) {
    userGrid = document.createElement('div');
    userGrid.id = 'userMemoriesGrid';
    userGrid.style.gridColumn = '1 / -1';
    userGrid.style.display = 'grid';
    userGrid.style.gridTemplateColumns = 'repeat(auto-fit, minmax(350px, 1fr))';
    userGrid.style.gap = '30px';
    grid.appendChild(userGrid);
  }
  userGrid.innerHTML = '';

  const items = getMemories();
  items.forEach(m => {
    const card = document.createElement('div');
    card.className = 'testimonial-card';
    card.innerHTML = `
      <div class="testimonial-avatar">
        <i class="${m.type === 'photo' ? 'fas fa-camera' : 'fas fa-user-circle'}"></i>
      </div>
      <blockquote>${m.html}</blockquote>
      <cite>— ${m.name || 'Anonyme'}</cite>
    `;
    userGrid.appendChild(card);
  });
}

function submitMemory(type) {
  const text = document.getElementById('memText') ? document.getElementById('memText').value.trim() : '';
  const name = document.getElementById('memName') ? document.getElementById('memName').value.trim() : '';
  const list = getMemories();

  if (type === 'photo') {
    const file = document.getElementById('memFile').files[0];
    if (!file) { alert('Choisis une image.'); return; }
    const reader = new FileReader();
    reader.onload = () => {
      const dataUrl = reader.result;
      list.unshift({
        type: 'photo',
        name,
        html: `<div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                 <img src="${dataUrl}" alt="Souvenir" style="width:100%;max-height:260px;object-fit:cover;border-radius:16px;">
                 ${text ? `<p style="margin:0;">${text}</p>` : ''}
               </div>`
      });
      saveMemories(list);
      renderMemories();
      closeMemoryModal();
    };
    reader.readAsDataURL(file);
    return;
  }

  // message / anecdote
  if (!text) { alert('Merci de saisir du texte.'); return; }
  list.unshift({ type, name, html: `<p style="margin:0;">${text}</p>` });
  saveMemories(list);
  renderMemories();
  closeMemoryModal();
}

/* =========================
   Méditation guidée
   ========================= */
function startMeditation() {
  const btn = document.querySelector('.meditation-btn');
  const container = document.querySelector('.prayer-text');
  if (!btn || !container) return;

  const original = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Méditation en cours…';

  const steps = [
    'Inspirez profondément…',
    'Fermez les yeux et souvenez-vous d’un moment doux avec Carine…',
    'Laissez la gratitude remplir votre cœur…',
    'Soufflez doucement et gardez sa lumière avec vous.'
  ];

  container.innerHTML = '';
  let i = 0;
  const iv = setInterval(() => {
    const p = document.createElement('p');
    p.textContent = steps[i];
    container.appendChild(p);
    i++;
    if (i === steps.length) {
      clearInterval(iv);
      setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = original;
      }, 800);
    }
  }, 2200);
}

/* =========================
   Initialisation au chargement
   ========================= */
document.addEventListener('DOMContentLoaded', () => {
  renderMemories();
  // flamme déjà allumée ?
  if (localStorage.getItem('hasLitCandle') === '1') {
    const flame = document.querySelector('.flame');
    if (flame) flame.classList.add('lit');
  }
});
</script>
</body>
</html>
