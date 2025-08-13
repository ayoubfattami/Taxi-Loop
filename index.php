<?php
// Traitement du formulaire de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $business_category = $_POST['business_category'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $honeypot = $_POST['website'] ?? ''; // Champ honeypot anti-spam
    
    // Protection anti-spam : si le champ honeypot est rempli, c'est un robot
    if (!empty($honeypot)) {
        $error_message = "Erreur lors de l'envoi du message. Veuillez réessayer.";
    }
    // Validation des données
    elseif (!empty($business_category) && !empty($full_name) && !empty($email) && !empty($phone) && !empty($message)) {
        // Configuration de l'email
        $to = 'ayoub@lera-concept.com';
        $subject = 'Nouvelle demande de contact - Taxi Loop';
        
        // Corps de l'email
        $email_body = "Nouvelle demande de contact:\n\n";
        $email_body .= "Secteur d'activité: " . $business_category . "\n";
        $email_body .= "Nom complet: " . $full_name . "\n";
        $email_body .= "Email: " . $email . "\n";
        $email_body .= "Téléphone: " . $phone . "\n";
        $email_body .= "Message: " . $message . "\n";
        
        // Headers de l'email
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        // Protection anti-spam : vérification IP proxy/VPN avec proxycheck.io
        $user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $api_key = 'v80608-173849-jc8400-o25w02';
        $is_proxy = false;
        
        if (!empty($user_ip) && filter_var($user_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // Appel à l'API proxycheck.io
            $api_url = "http://proxycheck.io/v2/{$user_ip}?key={$api_key}&vpn=1&asn=1";
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5, // Timeout de 5 secondes
                    'method' => 'GET'
                ]
            ]);
            
            $response = @file_get_contents($api_url, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                if (isset($data[$user_ip]['proxy']) && $data[$user_ip]['proxy'] === 'yes') {
                    $is_proxy = true;
                }
            }
        }
        
        // Si l'IP est détectée comme proxy/VPN, bloquer l'envoi
        if ($is_proxy) {
            $error_message = "Votre connexion semble utiliser un proxy ou VPN. Veuillez désactiver ces services et réessayer.";
        }
        // Envoi de l'email seulement si pas de proxy détecté
        elseif (mail($to, $subject, $email_body, $headers)) {
            $success_message = "Votre message a été envoyé avec succès !";
        } else {
            $error_message = "Erreur lors de l'envoi du message. Veuillez réessayer.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taxi Loop - Publicité Mobile Innovante</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* --- Navbar Responsive Styles --- */
        .navbar {
            width: 100%;
            background: #111;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            height: 64px;
        }
        .nav-logo {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 2rem;
            color: #fff;
            letter-spacing: 2px;
        }
        .nav-menu {
            display: flex;
            gap: 32px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .nav-menu li a {
            text-decoration: none;
            color: #fff;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            font-size: 1rem;
            transition: color 0.2s;
        }
        .nav-menu li a:hover {
            color: #FFD600;
        }
        .nav-burger {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            cursor: pointer;
            background: none;
            border: none;
        }
        .nav-burger span {
            display: block;
            width: 28px;
            height: 4px;
            margin: 4px 0;
            background: #fff;
            border-radius: 2px;
            transition: 0.3s;
        }
        /* --- Mobile Styles --- */
        @media (max-width: 900px) {
            .navbar {
                padding: 0 16px;
                height: 56px;
            }
            .nav-menu {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: #111;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 32px;
                z-index: 9999;
                transform: translateY(-100%);
                transition: transform 0.3s;
            }
            .nav-menu.open {
                transform: translateY(0);
            }
            .nav-menu li a {
                color: #fff;
                font-size: 1.3rem;
            }
            .nav-menu li a:hover {
                color: #FFD600;
            }
            .nav-burger {
                display: flex;
            }
            .nav-burger span {
                background: #fff;
            }
        }
        /* Prevent body scroll when menu open */
        body.menu-open {
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Navbar OUTSIDE .hero-container for stacking context -->
    <nav class="navbar">
        <div class="nav-logo">TL</div>
        <ul class="nav-menu" id="navMenu">
            <li><a href="#hero-section">Accueil</a></li>
            <li><a href="#opportunities-showcase">Comment ça marche&nbsp;?</a></li>
            <li><a href="#taxi-loop-clients-showcase">Clients</a></li>
            <li><a href="#taxi-loop-footer-contact">Rejoindre la boucle</a></li>
            <li><a href="#taxi-loop-contact-form-section">Réservez une campagne</a></li>
        </ul>
        <button class="nav-burger" id="navBurger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </nav>

    <div class="hero-container" id="hero-section">
        <div class="background-animation">
            <div class="infinity-symbol"></div>
            <div class="floating-tablets">
                <div class="tablet tablet-1"></div>
                <div class="tablet tablet-2"></div>
                <div class="tablet tablet-3"></div>
            </div>
        </div>
        
        <header class="hero-header">
            <!-- Navbar déplacée ici -->
        </header>

        <main class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="taxi-text">Taxi</span>
                    <span class="loop-text">
                        <span class="letter">L</span>
                        <span class="eye-container">
                            <span class="eye left-eye">
                                <span class="pupil"></span>
                            </span>
                        </span>
                        <span class="eye-container">
                            <span class="eye right-eye">
                                <span class="pupil"></span>
                            </span>
                        </span>
                        <span class="letter">P</span>
                    </span>
                </h1>
                <p class="hero-subtitle">Révolutionnez votre communication avec nos tablettes publicitaires dans les taxis</p>
                <div class="hero-stats">
                    <div class="stat">
                        <span class="stat-number">∞</span>
                        <span class="stat-label">Diffusion Continue</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Visibilité</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">Impact</span>
                    </div>
                </div>
                <div class="cta-buttons">
                    <button class="btn-primary" id="cta-reserver">Réserver une campagne</button>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Section Features -->
    <section class="features-section">
        <div class="features-container">
            <div class="feature-card">
                <div class="feature-image-wrapper">
                    <img src="assets/images/section1-imag1.webp" alt="La diffusion urbaine par excellence" class="feature-image">
                </div>
                <h3 class="feature-title">La diffusion urbaine par excellence</h3>
            </div>
            <div class="feature-card">
                <div class="feature-image-wrapper">
                    <img src="assets/images/section1-imag2.png" alt="Une fenêtre digitale dans chaque trajet" class="feature-image">
                </div>
                <h3 class="feature-title">Une fenêtre digitale dans chaque trajet</h3>
            </div>
        </div>
    </section>
    
    <!-- Section Opportunities -->
    <section class="opportunities-showcase" id="opportunities-showcase">
        <div class="opportunities-wrapper">
            <h2 class="opportunities-main-title">Un écran,<br>mille opportunités</h2>
            
            <div class="opportunity-block">
                <div class="opportunity-visual">
                    <img src="assets/images/section2-img1.png" alt="Tablette digitale dans un taxi" class="opportunity-img">
                </div>
                <div class="opportunity-description">
                    <p class="opportunity-text">Chaque taxi partenaire est équipé d'une tablette digitale à l'arrière du siège passager. Les contenus diffusés tournent en boucle : vidéos, visuels, messages courts... selon des créneaux horaires personnalisés.</p>
                </div>
            </div>
            
            <div class="opportunity-block">
                <div class="opportunity-description">
                    <p class="opportunity-text">Une solution flexible, sans frontières sectorielles. Que vous soyez un hôtel de charme, un salon de coiffure ou un espace culturel, Taxi Loop vous donne la parole.</p>
                </div>
                <div class="opportunity-visual">
                    <img src="assets/images/section2-img2.png" alt="Espaces commerciaux variés" class="opportunity-img">
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section No Sector Limit -->
    <section class="no-sector-section">
        <div class="no-sector-wrapper">
            <div class="no-sector-content">
                <h2 class="no-sector-title">Pas de secteur imposé : nous diffusons pour les hôtels, riads, cafés, expositions, clubs, concept stores, salles de sport, cabinets esthétiques, agences de communication, et bien plus encore.</h2>
                <p class="no-sector-subtitle">Profitez de la mobilité des taxis pour capter l'attention d'un public actif, local et international, en plein moment de disponibilité.</p>
            </div>
        </div>
    </section>
    
    <!-- Section Categories -->
    <section class="categories-section">
        <div class="categories-wrapper">
            <div class="categories-grid">
                <div class="category-block">
                    <div class="category-image">
                        <img src="assets/images/section3-img1.jpg" alt="Hôtels, villas & riads" class="category-img">
                    </div>
                    <h3 class="category-title">Hôtels, villas & riads</h3>
                    <p class="category-text">Touchez une clientèle touristique en immersion, à quelques minutes de leur prochain lieu de séjour.</p>
                </div>
                
                <div class="category-block">
                    <div class="category-image">
                        <img src="assets/images/section3-img2.jpg" alt="Restaurants & cafés" class="category-img">
                    </div>
                    <h3 class="category-title">Restaurants & cafés</h3>
                    <p class="category-text">Influencez les choix gourmands dès le trajet</p>
                </div>
                
                <div class="category-block">
                    <div class="category-image">
                        <img src="assets/images/section3-img3.jpg" alt="Culture & expositions" class="category-img">
                    </div>
                    <h3 class="category-title">Culture & expositions</h3>
                    <p class="category-text">Attirez les curieux, les flâneurs et les passionnés d'art avec des contenus culturels percutants et bien placés.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section Clients Carrousel -->
    <section class="taxi-loop-clients-showcase" id="taxi-loop-clients-showcase">
        <div class="taxi-loop-clients-wrapper">
            <h2 class="taxi-loop-clients-main-title">Nos Clients</h2>
            <div class="taxi-loop-clients-carousel-container">
                <div class="taxi-loop-clients-carousel-track">
                    <a href="https://unlimitedmarrakech.fr/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/1.png" alt="Client 1" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://rentall.ma/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/2.png" alt="Client 2" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://www.saphyyrevents.fr/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/3.png" alt="Client 3" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://rentaphone.ma/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/4.png" alt="Client 4" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://almaadenvillahotel.com/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/5.png" alt="Client 5" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://www.venuspop.ma/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/6.png" alt="Client 6" class="taxi-loop-client-logo-img">
                    </a>
                    <!-- Duplication pour l'effet infini -->
                    <a href="https://unlimitedmarrakech.fr/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/1.png" alt="Client 1" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://rentall.ma/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/2.png" alt="Client 2" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://www.saphyyrevents.fr/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/3.png" alt="Client 3" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://rentaphone.ma/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/4.png" alt="Client 4" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://almaadenvillahotel.com/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/5.png" alt="Client 5" class="taxi-loop-client-logo-img">
                    </a>
                    <a href="https://www.venuspop.ma/" target="_blank" class="taxi-loop-client-logo-item taxi-loop-client-logo-link">

                        <img src="assets/Logo/6.png" alt="Client 6" class="taxi-loop-client-logo-img">
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    
    
    <!-- Section Footer Contact -->
    <div class="taxi-loop-footer-contact" id="taxi-loop-footer-contact">
        <div class="taxi-loop-footer-wrapper">
            <div class="taxi-loop-footer-logo">
                <img src="assets/images/logo-taxi-loop.png" alt="Taxi Loop Logo" class="taxi-loop-footer-logo-img">
            </div>
            <h2 class="taxi-loop-footer-title">Rejoignez la boucle</h2>
            <p class="taxi-loop-footer-email">contact@taxiloop.ma</p>
            
            <div class="taxi-loop-footer-social">
                <h3 class="taxi-loop-footer-social-title">Social</h3>
                <div class="taxi-loop-footer-social-icons">
                    <a href="#" class="taxi-loop-footer-social-link" target="_blank">
                        <svg class="taxi-loop-footer-social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="taxi-loop-footer-social-link" target="_blank">
                        <svg class="taxi-loop-footer-social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                        </svg>
                    </a>
                    <a href="#" class="taxi-loop-footer-social-link" target="_blank">
                        <svg class="taxi-loop-footer-social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Formulaire de Contact -->
    <section class="taxi-loop-contact-form-section" id="taxi-loop-contact-form-section">
        <div class="taxi-loop-contact-form-container">
            <div class="taxi-loop-contact-form-wrapper">
                <?php if (isset($success_message)): ?>
                    <div class="taxi-loop-form-message taxi-loop-form-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error_message)): ?>
                    <div class="taxi-loop-form-message taxi-loop-form-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form class="taxi-loop-contact-form" method="POST" action="">
                    <div class="taxi-loop-form-group">
                        <label for="business-category" class="taxi-loop-form-label">Quel univers représente votre marque ?</label>
                        <div class="taxi-loop-custom-select">
                            <select id="business-category" name="business_category" class="taxi-loop-select" required>
                                <option value="" disabled selected>Sélectionnez votre secteur</option>
                                <option value="hebergement-tourisme">Hébergement & Tourisme</option>
                                <option value="restauration-lifestyle">Restauration & Lifestyle</option>
                                <option value="culture-loisirs">Culture & Loisirs</option>
                                <option value="commerces-concepts">Commerces & Concepts</option>
                                <option value="bien-etre-beaute">Bien-être & Beauté</option>
                                <option value="communication-services">Communication & Services</option>
                                <option value="sante-services-medicaux">Santé & Services médicaux</option>
                                <option value="autres-sur-mesure">Autres / Sur-mesure</option>
                            </select>
                            <div class="taxi-loop-select-arrow">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7 10l5 5 5-5z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="taxi-loop-form-group">
                        <label for="full-name" class="taxi-loop-form-label">Nom complet</label>
                        <input type="text" id="full-name" name="full_name" class="taxi-loop-form-input" required>
                    </div>
                    
                    <div class="taxi-loop-form-group">
                        <label for="email" class="taxi-loop-form-label">Adresse e-mail</label>
                        <input type="email" id="email" name="email" class="taxi-loop-form-input" required>
                    </div>
                    
                    <div class="taxi-loop-form-group">
                        <label for="phone" class="taxi-loop-form-label">Téléphone</label>
                        <input type="tel" id="phone" name="phone" class="taxi-loop-form-input" required>
                    </div>
                    
                    <div class="taxi-loop-form-group">
                        <label for="message" class="taxi-loop-form-label">Demande</label>
                        <textarea id="message" name="message" class="taxi-loop-form-textarea" rows="5" required></textarea>
                    </div>
                    
                    <!-- Champ honeypot anti-spam (invisible pour les utilisateurs) -->
                    <div style="position: absolute; left: -9999px; opacity: 0; pointer-events: none;">
                        <label for="website">Site web (ne pas remplir)</label>
                        <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    
                    <button type="submit" class="taxi-loop-form-submit">ENVOYER</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        // --- Burger Menu JS ---
        const burger = document.getElementById('navBurger');
        const menu = document.getElementById('navMenu');
        burger.addEventListener('click', function() {
            menu.classList.toggle('open');
            document.body.classList.toggle('menu-open', menu.classList.contains('open'));
        });
        // Close menu on link click (mobile)
        menu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                // Smooth scroll
                const targetId = this.getAttribute('href').replace('#','');
                const target = document.getElementById(targetId);
                if (target) {
                    e.preventDefault();
                    menu.classList.remove('open');
                    document.body.classList.remove('menu-open');
                    target.scrollIntoView({behavior: 'smooth'});
                }
            });
        });
        // Optional: close menu on outside click
        document.addEventListener('click', function(e) {
            if (menu.classList.contains('open') && !menu.contains(e.target) && e.target !== burger) {
                menu.classList.remove('open');
                document.body.classList.remove('menu-open');
            }
        });
        // Scroll vers le formulaire quand on clique sur le bouton CTA
        document.getElementById('cta-reserver').addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.getElementById('taxi-loop-contact-form-section');
            if (target) {
                target.scrollIntoView({behavior: 'smooth'});
            }
        });
    </script>
    <script src="script.js"></script>
</body>
</html>