// Taxi Loop - Script JavaScript pour les animations et interactions

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    
    // Animation des yeux qui suivent le curseur
    const pupils = document.querySelectorAll('.pupil');
    const eyes = document.querySelectorAll('.eye');
    
    // Fonction pour faire suivre le curseur par les yeux
    function followCursor(e) {
        pupils.forEach((pupil, index) => {
            const eye = eyes[index];
            const eyeRect = eye.getBoundingClientRect();
            const eyeCenterX = eyeRect.left + eyeRect.width / 2;
            const eyeCenterY = eyeRect.top + eyeRect.height / 2;
            
            const deltaX = e.clientX - eyeCenterX;
            const deltaY = e.clientY - eyeCenterY;
            
            const angle = Math.atan2(deltaY, deltaX);
            const distance = Math.min(eyeRect.width * 0.15, Math.sqrt(deltaX * deltaX + deltaY * deltaY) * 0.1);
            
            const pupilX = Math.cos(angle) * distance;
            const pupilY = Math.sin(angle) * distance;
            
            pupil.style.transform = `translate(${pupilX}px, ${pupilY}px)`;
        });
    }
    
    // Ajouter l'événement de suivi du curseur
    document.addEventListener('mousemove', followCursor);
    
    // Animation automatique des yeux quand pas de mouvement de souris
    let mouseTimeout;
    let autoEyeMovement;
    
    function startAutoEyeMovement() {
        autoEyeMovement = setInterval(() => {
            const randomAngle = Math.random() * Math.PI * 2;
            const randomDistance = Math.random() * 15;
            
            pupils.forEach(pupil => {
                const pupilX = Math.cos(randomAngle) * randomDistance;
                const pupilY = Math.sin(randomAngle) * randomDistance;
                pupil.style.transform = `translate(${pupilX}px, ${pupilY}px)`;
            });
        }, 2000);
    }
    
    function stopAutoEyeMovement() {
        if (autoEyeMovement) {
            clearInterval(autoEyeMovement);
        }
    }
    
    document.addEventListener('mousemove', () => {
        stopAutoEyeMovement();
        clearTimeout(mouseTimeout);
        mouseTimeout = setTimeout(startAutoEyeMovement, 3000);
    });
    
    // Démarrer le mouvement automatique des yeux
    startAutoEyeMovement();
    
    // Carrousel de publicités dans la tablette
    const adSlides = document.querySelectorAll('.ad-slide');
    let currentSlide = 0;
    
    function showNextSlide() {
        // Masquer la slide actuelle
        adSlides[currentSlide].classList.remove('active');
        
        // Passer à la slide suivante
        currentSlide = (currentSlide + 1) % adSlides.length;
        
        // Afficher la nouvelle slide
        adSlides[currentSlide].classList.add('active');
    }
    
    // Changer de slide toutes les 3 secondes
    setInterval(showNextSlide, 3000);
    
    // Animation de pulsation pour les statistiques
    const statNumbers = document.querySelectorAll('.stat-number');
    
    function animateStats() {
        statNumbers.forEach((stat, index) => {
            setTimeout(() => {
                stat.style.transform = 'scale(1.1)';
                stat.style.transition = 'transform 0.3s ease';
                
                setTimeout(() => {
                    stat.style.transform = 'scale(1)';
                }, 300);
            }, index * 200);
        });
    }
    
    // Animer les stats toutes les 5 secondes
    setInterval(animateStats, 5000);
    
    // Animation d'entrée progressive des éléments
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observer les éléments à animer
    const animatedElements = document.querySelectorAll('.hero-stats, .cta-buttons');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Effet de parallaxe pour les éléments flottants
    function parallaxEffect() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.floating-tablets .tablet');
        
        parallaxElements.forEach((element, index) => {
            const speed = 0.5 + (index * 0.1);
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }
    
    // Ajouter l'effet de parallaxe au scroll
    window.addEventListener('scroll', parallaxEffect);
    
    // Animation du connecteur infini
    const infinityConnector = document.querySelector('.infinity-connector');
    
    function animateInfinityConnector() {
        infinityConnector.style.transform = 'translate(-50%, -50%) scaleX(1.2)';
        infinityConnector.style.transition = 'transform 0.5s ease';
        
        setTimeout(() => {
            infinityConnector.style.transform = 'translate(-50%, -50%) scaleX(1)';
        }, 500);
    }
    
    // Animer le connecteur infini toutes les 4 secondes
    setInterval(animateInfinityConnector, 4000);
    
    // Effet de hover sur les boutons CTA
    const ctaButtons = document.querySelectorAll('.btn-primary, .btn-secondary');
    
    ctaButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Animation de clignotement aléatoire pour les yeux
    function randomBlink() {
        eyes.forEach(eye => {
            if (Math.random() < 0.3) {
                eye.style.transform = 'scaleY(0.1)';
                eye.style.transition = 'transform 0.1s ease';
                
                setTimeout(() => {
                    eye.style.transform = 'scaleY(1)';
                }, 100);
            }
        });
    }
    
    // Clignotement aléatoire toutes les 3-7 secondes
    function scheduleRandomBlink() {
        const delay = 3000 + Math.random() * 4000;
        setTimeout(() => {
            randomBlink();
            scheduleRandomBlink();
        }, delay);
    }
    
    scheduleRandomBlink();
    
    // Effet de typing pour le sous-titre
    const subtitle = document.querySelector('.hero-subtitle');
    const originalText = subtitle.textContent;
    
    function typeWriter() {
        subtitle.textContent = '';
        let i = 0;
        
        function type() {
            if (i < originalText.length) {
                subtitle.textContent += originalText.charAt(i);
                i++;
                setTimeout(type, 50);
            }
        }
        
        type();
    }
    
    // Démarrer l'effet de typing après 1 seconde
    setTimeout(typeWriter, 1000);
    
    // Gestion du scroll fluide pour l'indicateur de scroll
    const scrollIndicator = document.querySelector('.scroll-indicator');
    
    if (scrollIndicator) {
        scrollIndicator.addEventListener('click', () => {
            window.scrollTo({
                top: window.innerHeight,
                behavior: 'smooth'
            });
        });
    }
    
    // Animation de rotation continue pour le symbole infini en arrière-plan
    const backgroundInfinity = document.querySelector('.infinity-symbol');
    
    function rotateInfinity() {
        let rotation = 0;
        setInterval(() => {
            rotation += 0.5;
            backgroundInfinity.style.transform = `rotate(${rotation}deg)`;
        }, 50);
    }
    
    rotateInfinity();
    
    // Effet de particules flottantes
    function createFloatingParticle() {
        const particle = document.createElement('div');
        particle.style.position = 'absolute';
        particle.style.width = '4px';
        particle.style.height = '4px';
        particle.style.background = 'var(--primary-yellow)';
        particle.style.borderRadius = '50%';
        particle.style.opacity = '0.3';
        particle.style.pointerEvents = 'none';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = '100%';
        particle.style.zIndex = '1';
        
        document.querySelector('.background-animation').appendChild(particle);
        
        // Animation de la particule
        let position = 100;
        const speed = 0.5 + Math.random() * 1;
        
        function animateParticle() {
            position -= speed;
            particle.style.top = position + '%';
            
            if (position < -10) {
                particle.remove();
            } else {
                requestAnimationFrame(animateParticle);
            }
        }
        
        animateParticle();
    }
    
    // Créer des particules flottantes périodiquement
    setInterval(createFloatingParticle, 2000);
    
    // Optimisation des performances - réduire les animations sur mobile
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Réduire la fréquence des animations sur mobile
        document.removeEventListener('mousemove', followCursor);
        stopAutoEyeMovement();
        
        // Animation simplifiée pour mobile
        pupils.forEach(pupil => {
            pupil.style.animation = 'lookAround 8s ease-in-out infinite';
        });
    }
    
    console.log('🚕 Taxi Loop - Hero section chargée avec succès!');
});

// Fonction utilitaire pour déboguer les animations
function debugAnimations() {
    console.log('État des animations:');
    console.log('- Yeux:', document.querySelectorAll('.eye').length);
    console.log('- Slides publicitaires:', document.querySelectorAll('.ad-slide').length);
    console.log('- Tablettes flottantes:', document.querySelectorAll('.tablet').length);
}

// Exposer la fonction de debug globalement
window.debugTaxiLoop = debugAnimations;