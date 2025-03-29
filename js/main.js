
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            const nav = document.querySelector('.main-nav');
            nav.classList.toggle('show');
            
            // Toggle menu icon
            const icon = menuToggle.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });
    }

    // Add responsive mobile navigation styles dynamically
    const style = document.createElement('style');
    style.textContent = `
        @media (max-width: 768px) {
            .main-nav.show {
                display: block;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--primary-color);
                padding: 1rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }
            
            .main-nav.show ul {
                flex-direction: column;
            }
            
            .main-nav.show li {
                margin: 0;
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .main-nav.show li:last-child {
                border-bottom: none;
            }
        }
    `;
    document.head.appendChild(style);

    // Animate items on scroll
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.event-card, .update-card, .committee-card');
        
        elements.forEach(function(element) {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (elementPosition < screenPosition) {
                element.classList.add('animate-fade-in');
            }
        });
    };

    // Run animation check on load and scroll
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // Run once on page load
});

// Define animation class
document.head.insertAdjacentHTML('beforeend', `
    <style>
        .animate-fade-in {
            animation: fadeIn 0.6s ease forwards;
        }
        
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .event-card, .update-card, .committee-card {
            opacity: 0;
        }
    </style>
`);
