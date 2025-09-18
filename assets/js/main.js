// assets/js/main.js - Core JavaScript functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initNavigation();
    initScrollEffects();
    initForms();
    initAnimations();
    loadPreviewContent();
});

// Navigation functionality
function initNavigation() {
    const navbar = document.getElementById('navbar');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navMenu = document.getElementById('navMenu');
    
    // Scroll effect for navbar
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
    
    // Mobile menu toggle
    if (mobileMenuBtn && navMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = mobileMenuBtn.querySelector('i');
            
            if (navMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close mobile menu when clicking on links
        const navLinks = navMenu.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                navMenu.classList.remove('active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
}

// Scroll effects and animations
function initScrollEffects() {
    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in-up');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.service-card, .gallery-item, .testimonial-slide, .info-item');
    animateElements.forEach(el => observer.observe(el));
}

// Form handling
function initForms() {
    // Newsletter form
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', handleNewsletterSubmit);
    }
    
    // Generic form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });
    });
}

// Newsletter subscription
async function handleNewsletterSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const email = form.querySelector('.newsletter-input').value;
    const button = form.querySelector('.newsletter-btn');
    
    if (!validateEmail(email)) {
        showMessage('Please enter a valid email address.', 'error');
        return;
    }
    
    // Show loading state
    button.classList.add('loading');
    button.disabled = true;
    
    try {
        const response = await fetch('api/newsletter.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('Successfully subscribed to newsletter!', 'success');
            form.reset();
        } else {
            showMessage(result.message || 'Subscription failed. Please try again.', 'error');
        }
    } catch (error) {
        console.error('Newsletter subscription error:', error);
        showMessage('Network error. Please try again.', 'error');
    } finally {
        button.classList.remove('loading');
        button.disabled = false;
    }
}

// Field validation
function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    
    // Remove existing error styling
    field.classList.remove('error');
    
    // Check if required field is empty
    if (field.hasAttribute('required') && !value) {
        showFieldError(field, 'This field is required.');
        return false;
    }
    
    // Email validation
    if (field.type === 'email' && value && !validateEmail(value)) {
        showFieldError(field, 'Please enter a valid email address.');
        return false;
    }
    
    // Phone validation
    if (field.type === 'tel' && value && !validatePhone(value)) {
        showFieldError(field, 'Please enter a valid phone number.');
        return false;
    }
    
    return true;
}

function clearFieldError(e) {
    const field = e.target;
    field.classList.remove('error');
    
    const errorMsg = field.parentNode.querySelector('.field-error');
    if (errorMsg) {
        errorMsg.remove();
    }
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    field.parentNode.appendChild(errorDiv);
}

// Validation helpers
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePhone(phone) {
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
    return phoneRegex.test(phone);
}

// Message display
function showMessage(message, type = 'info') {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.toast-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = `toast-message toast-${type}`;
    messageDiv.textContent = message;
    
    // Style the message
    Object.assign(messageDiv.style, {
        position: 'fixed',
        top: '100px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '10px',
        color: 'white',
        fontWeight: '500',
        zIndex: '9999',
        transform: 'translateX(100%)',
        transition: 'transform 0.3s ease',
        maxWidth: '400px',
        wordWrap: 'break-word'
    });
    
    // Set background color based on type
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    messageDiv.style.backgroundColor = colors[type] || colors.info;
    
    document.body.appendChild(messageDiv);
    
    // Animate in
    setTimeout(() => {
        messageDiv.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        messageDiv.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 300);
    }, 5000);
}

// Animation initialization
function initAnimations() {
    // Counter animation for stats
    const counters = document.querySelectorAll('.stat-number');
    const counterObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    });
    
    counters.forEach(counter => counterObserver.observe(counter));
}

function animateCounter(element) {
    const target = parseInt(element.textContent.replace(/\D/g, ''));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;
    
    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        const suffix = element.textContent.includes('+') ? '+' : '';
        element.textContent = Math.floor(current) + suffix;
    }, 16);
}

// Load preview content for homepage
function loadPreviewContent() {
    loadServicesPreview();
    loadGalleryPreview();
}

async function loadServicesPreview() {
    const servicesContainer = document.getElementById('servicesPreview');
    if (!servicesContainer) return;
    
    try {
        const response = await fetch('api/services.php?limit=3');
        const services = await response.json();
        
        servicesContainer.innerHTML = services.map(service => `
            <div class="service-card">
                <div class="service-icon">
                    <i class="${service.icon}"></i>
                </div>
                <h3>${service.title}</h3>
                <p>${service.description}</p>
                <div class="service-price">
                    ${service.price_from ? `<span class="price">From ${formatPrice(service.price_from)}</span>` : '<span class="price">Contact for Quote</span>'}
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading services preview:', error);
        // Load default content
        loadDefaultServicesPreview(servicesContainer);
    }
}

function loadDefaultServicesPreview(container) {
    const defaultServices = [
        {
            icon: 'fas fa-heart',
            title: 'Full Wedding Planning',
            description: 'Complete wedding planning service from engagement to honeymoon.',
            price_from: 15000
        },
        {
            icon: 'fas fa-calendar-check',
            title: 'Partial Wedding Planning',
            description: 'Professional guidance for specific aspects of your wedding.',
            price_from: 8000
        },
        {
            icon: 'fas fa-clock',
            title: 'Day-of Coordination',
            description: 'Ensure your wedding day runs smoothly with expert coordination.',
            price_from: 3000
        }
    ];
    
    container.innerHTML = defaultServices.map(service => `
        <div class="service-card">
            <div class="service-icon">
                <i class="${service.icon}"></i>
            </div>
            <h3>${service.title}</h3>
            <p>${service.description}</p>
            <div class="service-price">
                <span class="price">From ${formatPrice(service.price_from)}</span>
            </div>
        </div>
    `).join('');
}

async function loadGalleryPreview() {
    const galleryContainer = document.getElementById('galleryPreview');
    if (!galleryContainer) return;
    
    try {
        const response = await fetch('api/gallery.php?limit=6');
        const items = await response.json();
        
        galleryContainer.innerHTML = items.map(item => `
            <div class="gallery-item">
                <img src="${item.image_url}" alt="${item.alt_text}" loading="lazy">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                    <div class="gallery-info">
                        <h4>${item.title}</h4>
                        <p>${item.description}</p>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading gallery preview:', error);
        // Load default content
        loadDefaultGalleryPreview(galleryContainer);
    }
}

function loadDefaultGalleryPreview(container) {
    const defaultItems = [
        {
            image_url: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=400',
            alt_text: 'Beautiful wedding ceremony',
            title: 'Elegant Ceremony',
            description: 'Outdoor wedding ceremony'
        },
        {
            image_url: 'https://images.unsplash.com/photo-1465495976277-4387d4b0e4a6?w=400',
            alt_text: 'Wedding reception',
            title: 'Reception Dinner',
            description: 'Romantic candlelit reception'
        },
        {
            image_url: 'https://images.unsplash.com/photo-1520854221256-17451cc331bf?w=400',
            alt_text: 'Wedding decorations',
            title: 'Floral Arrangements',
            description: 'Beautiful wedding flowers'
        },
        {
            image_url: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=400',
            alt_text: 'Wedding cake',
            title: 'Wedding Cake',
            description: 'Custom designed cake'
        },
        {
            image_url: 'https://images.unsplash.com/photo-1606800052052-a08af7148866?w=400',
            alt_text: 'Bridal bouquet',
            title: 'Bridal Bouquet',
            description: 'Handcrafted bouquet'
        },
        {
            image_url: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=400',
            alt_text: 'Wedding rings',
            title: 'Wedding Rings',
            description: 'Symbol of eternal love'
        }
    ];
    
    container.innerHTML = defaultItems.map(item => `
        <div class="gallery-item">
            <img src="${item.image_url}" alt="${item.alt_text}" loading="lazy">
            <div class="gallery-overlay">
                <i class="fas fa-search-plus"></i>
                <div class="gallery-info">
                    <h4>${item.title}</h4>
                    <p>${item.description}</p>
                </div>
            </div>
        </div>
    `).join('');
}

// Utility functions
function formatPrice(price) {
    return new Intl.NumberFormat('ro-RO').format(price) + ' LEI';
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
});

// Performance monitoring
window.addEventListener('load', function() {
    if ('performance' in window) {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        console.log('Page load time:', loadTime + 'ms');
    }
});
