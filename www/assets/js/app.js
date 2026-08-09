/**
 * MVCLM PHP Boilerplate
 * SPDX-License-Identifier: MIT
 */

(function() {
    'use strict';

    // Utility functions
    const Utils = {
        // Query selector wrapper
        $(selector, context = document) {
            return context.querySelector(selector);
        },

        // Query selector all wrapper
        $$(selector, context = document) {
            return Array.from(context.querySelectorAll(selector));
        },

        // Add event listener with error handling
        on(element, event, handler, options = {}) {
            if (element && typeof handler === 'function') {
                element.addEventListener(event, handler, options);
            }
        },

        // Remove event listener
        off(element, event, handler, options = {}) {
            if (element && typeof handler === 'function') {
                element.removeEventListener(event, handler, options);
            }
        },

        // Toggle class utility
        toggleClass(element, className) {
            if (element) {
                element.classList.toggle(className);
            }
        },

        // Add class utility
        addClass(element, className) {
            if (element) {
                element.classList.add(className);
            }
        },

        // Remove class utility
        removeClass(element, className) {
            if (element) {
                element.classList.remove(className);
            }
        },

        // Check if element has class
        hasClass(element, className) {
            return element ? element.classList.contains(className) : false;
        },

        // Get closest parent with selector
        closest(element, selector) {
            if (!element) return null;
            return element.closest(selector);
        }
    };

    // Dropdown functionality
    class Dropdown {
        constructor(element) {
            this.element = element;
            this.button = Utils.$('[aria-haspopup="menu"]', element);
            this.menu = Utils.$('.hs-dropdown-menu', element);
            this.isOpen = false;
            
            this.init();
        }

        init() {
            if (!this.button || !this.menu) return;

            // Button click handler
            Utils.on(this.button, 'click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggle();
            });

            // Close on outside click
            Utils.on(document, 'click', (e) => {
                if (!this.element.contains(e.target) && this.isOpen) {
                    this.close();
                }
            });

            // Close on escape key
            Utils.on(document, 'keydown', (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                    this.button.focus();
                }
            });

            // Keyboard navigation
            Utils.on(this.menu, 'keydown', (e) => {
                this.handleKeyboardNavigation(e);
            });
        }

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            if (this.isOpen) return;

            this.isOpen = true;
            Utils.removeClass(this.menu, 'hidden');
            Utils.removeClass(this.menu, 'opacity-0');
            Utils.addClass(this.menu, 'hs-dropdown-open:opacity-100');
            
            this.button.setAttribute('aria-expanded', 'true');
            
            // Focus first menu item
            setTimeout(() => {
                const firstItem = Utils.$('a', this.menu);
                if (firstItem) firstItem.focus();
            }, 100);
        }

        close() {
            if (!this.isOpen) return;

            this.isOpen = false;
            Utils.addClass(this.menu, 'opacity-0');
            Utils.removeClass(this.menu, 'hs-dropdown-open:opacity-100');
            
            this.button.setAttribute('aria-expanded', 'false');
            
            setTimeout(() => {
                Utils.addClass(this.menu, 'hidden');
            }, 150);
        }

        handleKeyboardNavigation(e) {
            const items = Utils.$$('a', this.menu);
            const currentIndex = items.findIndex(item => item === document.activeElement);

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
                    items[nextIndex].focus();
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
                    items[prevIndex].focus();
                    break;
                    
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    if (document.activeElement) {
                        document.activeElement.click();
                    }
                    break;
            }
        }
    }

    // Collapse functionality (for mobile menus)
    class Collapse {
        constructor(element) {
            this.element = element;
            this.target = Utils.$(this.element.getAttribute('data-hs-collapse') || this.element.getAttribute('aria-controls'));
            this.isOpen = false;
            
            this.init();
        }

        init() {
            if (!this.target) return;

            Utils.on(this.element, 'click', (e) => {
                e.preventDefault();
                this.toggle();
            });
        }

        toggle() {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        }

        open() {
            if (this.isOpen) return;

            this.isOpen = true;
            Utils.removeClass(this.target, 'hidden');
            this.element.setAttribute('aria-expanded', 'true');
            
            // Animate height
            this.target.style.height = '0px';
            this.target.offsetHeight; // Force reflow
            this.target.style.height = this.target.scrollHeight + 'px';
            
            setTimeout(() => {
                this.target.style.height = 'auto';
            }, 300);
        }

        close() {
            if (!this.isOpen) return;

            this.isOpen = false;
            this.target.style.height = this.target.scrollHeight + 'px';
            this.target.offsetHeight; // Force reflow
            this.target.style.height = '0px';
            
            this.element.setAttribute('aria-expanded', 'false');
            
            setTimeout(() => {
                Utils.addClass(this.target, 'hidden');
                this.target.style.height = '';
            }, 300);
        }
    }

    // Form validation utilities
    class FormValidator {
        static validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        static validatePassword(password, minLength = 6) {
            return password && password.length >= minLength;
        }

        static validateRequired(value) {
            return value && value.trim().length > 0;
        }

        // Must stay in step with RegisterController and AjaxController. This is
        // published on window.MVCLM, so a mismatch here becomes a rule the
        // browser accepts and the server refuses.
        static validateUsername(username) {
            const usernameRegex = /^[A-Za-z0-9]{3,16}$/;
            return usernameRegex.test(username);
        }
    }

    // Notification system
    class NotificationSystem {
        static show(message, type = 'info', duration = 5000) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
            
            const colors = {
                success: 'bg-green-500 text-white',
                error: 'bg-red-500 text-white',
                warning: 'bg-yellow-500 text-white',
                info: 'bg-blue-500 text-white'
            };
            
            notification.className += ` ${colors[type] || colors.info}`;
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button type="button" class="ml-4 text-white hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove
            if (duration > 0) {
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }, duration);
            }
        }
    }

    // Loading state management
    class LoadingManager {
        static show(element, text = 'Loading...') {
            if (!element) return;
            
            element.disabled = true;
            element.dataset.originalText = element.textContent;
            element.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${text}
            `;
        }

        static hide(element) {
            if (!element) return;
            
            element.disabled = false;
            if (element.dataset.originalText) {
                element.textContent = element.dataset.originalText;
                delete element.dataset.originalText;
            }
        }
    }

    // Main app initialization
    class App {
        constructor() {
            this.dropdowns = [];
            this.collapses = [];
            
            this.init();
        }

        init() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                Utils.on(document, 'DOMContentLoaded', () => this.initializeComponents());
            } else {
                this.initializeComponents();
            }
        }

        initializeComponents() {
            this.initDropdowns();
            this.initCollapses();
            this.initForms();
            this.initUtilities();
        }

        initDropdowns() {
            const dropdownElements = Utils.$$('.hs-dropdown');
            dropdownElements.forEach(element => {
                this.dropdowns.push(new Dropdown(element));
            });
        }

        initCollapses() {
            const collapseButtons = Utils.$$('.hs-collapse-toggle');
            collapseButtons.forEach(element => {
                this.collapses.push(new Collapse(element));
            });
        }

        initForms() {
            // Enhanced form validation and UX
            const forms = Utils.$$('form');
            forms.forEach(form => {
                Utils.on(form, 'submit', (e) => {
                    setTimeout(() => {
                        if (e.defaultPrevented) return;

                        const submitButton = Utils.$('button[type="submit"]', form);
                        if (submitButton) {
                            LoadingManager.show(submitButton, 'Processing...');
                        }
                    }, 0);
                });
            });

            // Real-time validation for specific inputs
            const emailInputs = Utils.$$('input[type="email"]');
            emailInputs.forEach(input => {
                Utils.on(input, 'blur', () => {
                    if (input.value && !FormValidator.validateEmail(input.value)) {
                        input.classList.add('border-red-300');
                        input.classList.remove('border-gray-300');
                    } else {
                        input.classList.remove('border-red-300');
                        input.classList.add('border-gray-300');
                    }
                });
            });
        }

        initUtilities() {
            // Smooth scrolling for anchor links
            const anchorLinks = Utils.$$('a[href^="#"]');
            anchorLinks.forEach(link => {
                Utils.on(link, 'click', (e) => {
                    const href = link.getAttribute('href');
                    if (href === '#') return;
                    
                    const target = Utils.$(href);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Auto-hide alerts after delay
            const alerts = Utils.$$('.alert-auto-hide');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        if (alert.parentElement) {
                            alert.remove();
                        }
                    }, 300);
                }, 5000);
            });

            // Copy to clipboard functionality
            const copyButtons = Utils.$$('[data-copy]');
            copyButtons.forEach(button => {
                Utils.on(button, 'click', () => {
                    const text = button.dataset.copy;
                    if (text) {
                        navigator.clipboard.writeText(text).then(() => {
                            NotificationSystem.show('Copied to clipboard!', 'success', 2000);
                        });
                    }
                });
            });
        }
    }

    window.MVCLM = {
        Utils,
        FormValidator,
        NotificationSystem,
        LoadingManager,

        // The session's CSRF token, read from the meta element both layouts
        // render. Any script posting to this application must send it under the
        // name csrf_token, because index.php refuses a POST without it. Markup
        // forms do not need this: they carry the hidden field Csrf::field()
        // writes for them.
        csrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        },

        // Initialize the app
        init() {
            return new App();
        }
    };

    // Auto-initialize
    const app = new App();

})();