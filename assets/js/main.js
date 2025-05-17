/**
 * E-Learning Platform - Main JavaScript
 * This file contains all the interactive functionality for the e-learning platform
 */

// Wait for the DOM to be fully loaded before executing scripts
document.addEventListener('DOMContentLoaded', function() {
  // Initialize all components
  initTabs();
  initTooltips();
  initDropdowns();
  initMobileMenu();
  initFormValidation();
  initRippleEffect();
  initAnimations();
  
  // Add smooth scrolling to all links
  initSmoothScroll();
});

/**
 * Initialize tab functionality
 */
function initTabs() {
  const tabs = document.querySelectorAll('.tab');
  if (!tabs.length) return;
  
  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      // Get the target tab pane
      const targetId = this.dataset.tab;
      const targetPane = document.getElementById(`${targetId}-tab`);
      
      if (!targetPane) return;
      
      // Remove active class from all tabs and panes
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
      
      // Add active class to current tab and pane
      this.classList.add('active');
      targetPane.classList.add('active');
      
      // Store the active tab in session storage for persistence
      if (typeof(Storage) !== 'undefined') {
        sessionStorage.setItem('activeTab', targetId);
      }
    });
  });
  
  // Restore active tab from session storage if available
  if (typeof(Storage) !== 'undefined' && sessionStorage.getItem('activeTab')) {
    const activeTabId = sessionStorage.getItem('activeTab');
    const activeTab = document.querySelector(`.tab[data-tab="${activeTabId}"]`);
    
    if (activeTab) {
      activeTab.click();
    }
  }
}

/**
 * Initialize tooltips
 */
function initTooltips() {
  const tooltips = document.querySelectorAll('[data-tooltip]');
  if (!tooltips.length) return;
  
  tooltips.forEach(tooltip => {
    const tooltipText = tooltip.dataset.tooltip;
    const tooltipElement = document.createElement('div');
    tooltipElement.classList.add('tooltip-text');
    tooltipElement.textContent = tooltipText;
    
    tooltip.classList.add('tooltip');
    tooltip.appendChild(tooltipElement);
  });
}

/**
 * Initialize dropdown menus
 */
function initDropdowns() {
  const dropdowns = document.querySelectorAll('.dropdown-toggle');
  if (!dropdowns.length) return;
  
  dropdowns.forEach(dropdown => {
    dropdown.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const dropdownMenu = this.nextElementSibling;
      if (!dropdownMenu) return;
      
      // Toggle the dropdown menu
      dropdownMenu.classList.toggle('show');
      
      // Close other open dropdowns
      document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        if (menu !== dropdownMenu) {
          menu.classList.remove('show');
        }
      });
    });
  });
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
      menu.classList.remove('show');
    });
  });
}

/**
 * Initialize mobile menu toggle
 */
function initMobileMenu() {
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.querySelector('.sidebar');
  
  if (!menuToggle || !sidebar) return;
  
  menuToggle.addEventListener('click', function() {
    sidebar.classList.toggle('show');
    document.body.classList.toggle('sidebar-open');
  });
  
  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', function(e) {
    if (sidebar.classList.contains('show') && 
        !sidebar.contains(e.target) && 
        !menuToggle.contains(e.target)) {
      sidebar.classList.remove('show');
      document.body.classList.remove('sidebar-open');
    }
  });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
  const forms = document.querySelectorAll('form');
  if (!forms.length) return;
  
  forms.forEach(form => {
    // Add novalidate attribute to disable browser's default validation
    form.setAttribute('novalidate', '');
    
    // Add submit event listener
    form.addEventListener('submit', function(e) {
      let isValid = true;
      
      // Check all required inputs
      const requiredInputs = form.querySelectorAll('[required]');
      requiredInputs.forEach(input => {
        // Remove existing error messages
        const existingError = input.parentNode.querySelector('.form-error');
        if (existingError) {
          existingError.remove();
        }
        
        // Reset input styles
        input.classList.remove('is-invalid');
        
        // Validate the input
        if (!validateInput(input)) {
          isValid = false;
          input.classList.add('is-invalid');
          
          // Create error message
          const errorMessage = document.createElement('div');
          errorMessage.classList.add('form-error');
          errorMessage.textContent = getErrorMessage(input);
          
          // Insert error message after the input
          input.parentNode.insertBefore(errorMessage, input.nextSibling);
        }
      });
      
      // Prevent form submission if validation fails
      if (!isValid) {
        e.preventDefault();
        
        // Scroll to the first invalid input
        const firstInvalidInput = form.querySelector('.is-invalid');
        if (firstInvalidInput) {
          firstInvalidInput.focus();
          firstInvalidInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      } else {
        // Show loading state if form is valid
        const submitButton = form.querySelector('[type="submit"]');
        if (submitButton && !form.dataset.noLoading) {
          const originalText = submitButton.innerHTML;
          submitButton.disabled = true;
          submitButton.innerHTML = '<span class="spinner spinner-sm"></span> Processing...';
          
          // Reset button after submission (for demo purposes)
          setTimeout(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
          }, 2000);
        }
      }
    });
    
    // Add input event listeners for real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      input.addEventListener('input', function() {
        // Remove error message if input becomes valid
        if (validateInput(this)) {
          this.classList.remove('is-invalid');
          const errorMessage = this.parentNode.querySelector('.form-error');
          if (errorMessage) {
            errorMessage.remove();
          }
        }
      });
    });
  });
}

/**
 * Validate a form input
 * @param {HTMLElement} input - The input element to validate
 * @returns {boolean} - Whether the input is valid
 */
function validateInput(input) {
  // Check if the input is empty
  if (input.required && !input.value.trim()) {
    return false;
  }
  
  // Validate email format
  if (input.type === 'email' && input.value.trim()) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(input.value);
  }
  
  // Validate password length
  if (input.type === 'password' && input.dataset.minLength) {
    const minLength = parseInt(input.dataset.minLength);
    return input.value.length >= minLength;
  }
  
  // Validate number range
  if (input.type === 'number') {
    const min = input.hasAttribute('min') ? parseFloat(input.min) : null;
    const max = input.hasAttribute('max') ? parseFloat(input.max) : null;
    const value = parseFloat(input.value);
    
    if (min !== null && value < min) return false;
    if (max !== null && value > max) return false;
  }
  
  return true;
}

/**
 * Get error message for an invalid input
 * @param {HTMLElement} input - The invalid input element
 * @returns {string} - The error message
 */
function getErrorMessage(input) {
  if (!input.value.trim()) {
    return 'This field is required';
  }
  
  if (input.type === 'email') {
    return 'Please enter a valid email address';
  }
  
  if (input.type === 'password' && input.dataset.minLength) {
    const minLength = parseInt(input.dataset.minLength);
    return `Password must be at least ${minLength} characters long`;
  }
  
  if (input.type === 'number') {
    const min = input.hasAttribute('min') ? parseFloat(input.min) : null;
    const max = input.hasAttribute('max') ? parseFloat(input.max) : null;
    
    if (min !== null && max !== null) {
      return `Please enter a number between ${min} and ${max}`;
    } else if (min !== null) {
      return `Please enter a number greater than or equal to ${min}`;
    } else if (max !== null) {
      return `Please enter a number less than or equal to ${max}`;
    }
  }
  
  return 'Invalid input';
}

/**
 * Initialize ripple effect for buttons
 */
function initRippleEffect() {
  const buttons = document.querySelectorAll('.btn-ripple');
  if (!buttons.length) return;
  
  buttons.forEach(button => {
    button.addEventListener('click', function(e) {
      const rect = button.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;
      
      const ripple = document.createElement('span');
      ripple.classList.add('ripple');
      ripple.style.left = `${x}px`;
      ripple.style.top = `${y}px`;
      
      button.appendChild(ripple);
      
      setTimeout(() => {
        ripple.remove();
      }, 600);
    });
  });
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]:not([href="#"])');
  if (!links.length) return;
  
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      const targetId = this.getAttribute('href');
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        // Scroll to the target element smoothly
        targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Update URL hash without scrolling
        history.pushState(null, null, targetId);
      }
    });
  });
}

/**
 * Initialize animations for elements with data-animate attribute
 */
function initAnimations() {
  const animatedElements = document.querySelectorAll('[data-animate]');
  if (!animatedElements.length) return;
  
  // Create an intersection observer
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Add the animation class when element is in viewport
        const animationClass = entry.target.dataset.animate;
        entry.target.classList.add(animationClass);
        
        // Stop observing after animation is applied
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  
  // Observe all elements with data-animate attribute
  animatedElements.forEach(element => {
    // Add a base class for animations
    element.classList.add('animated');
    observer.observe(element);
  });
}

/**
 * Show a toast notification
 * @param {string} message - The message to display
 * @param {string} type - The type of notification (success, error, warning, info)
 * @param {number} duration - Duration in milliseconds
 */
function showToast(message, type = 'info', duration = 3000) {
  // Create toast container if it doesn't exist
  let toastContainer = document.querySelector('.toast-container');
  if (!toastContainer) {
    toastContainer = document.createElement('div');
    toastContainer.classList.add('toast-container');
    document.body.appendChild(toastContainer);
  }
  
  // Create toast element
  const toast = document.createElement('div');
  toast.classList.add('toast', `toast-${type}`);
  toast.innerHTML = `
    <div class="toast-content">
      <span class="toast-message">${message}</span>
    </div>
    <button class="toast-close">&times;</button>
  `;
  
  // Add toast to container
  toastContainer.appendChild(toast);
  
  // Show the toast with animation
  setTimeout(() => {
    toast.classList.add('show');
  }, 10);
  
  // Add close button functionality
  const closeButton = toast.querySelector('.toast-close');
  closeButton.addEventListener('click', () => {
    removeToast(toast);
  });
  
  // Auto-remove toast after duration
  setTimeout(() => {
    removeToast(toast);
  }, duration);
}

/**
 * Remove a toast notification with animation
 * @param {HTMLElement} toast - The toast element to remove
 */
function removeToast(toast) {
  toast.classList.remove('show');
  
  // Remove from DOM after animation completes
  setTimeout(() => {
    toast.remove();
    
    // Remove container if no toasts left
    const toastContainer = document.querySelector('.toast-container');
    if (toastContainer && !toastContainer.hasChildNodes()) {
      toastContainer.remove();
    }
  }, 300);
}

/**
 * Toggle password visibility in password fields
 * @param {string} inputId - The ID of the password input
 * @param {string} toggleId - The ID of the toggle button
 */
function togglePasswordVisibility(inputId, toggleId) {
  const passwordInput = document.getElementById(inputId);
  const toggleButton = document.getElementById(toggleId);
  
  if (!passwordInput || !toggleButton) return;
  
  toggleButton.addEventListener('click', function() {
    // Toggle password visibility
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    // Update toggle button icon/text
    this.innerHTML = type === 'password' ? 
      '<i class="icon-eye"></i>' : 
      '<i class="icon-eye-off"></i>';
  });
}

/**
 * Initialize a countdown timer
 * @param {string} elementId - The ID of the element to display the countdown
 * @param {Date} targetDate - The target date and time for the countdown
 */
function initCountdown(elementId, targetDate) {
  const countdownElement = document.getElementById(elementId);
  if (!countdownElement) return;
  
  // Update the countdown every second
  const countdownInterval = setInterval(function() {
    // Get current date and calculate time remaining
    const now = new Date().getTime();
    const distance = targetDate.getTime() - now;
    
    // Calculate days, hours, minutes, seconds
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    // Display the countdown
    countdownElement.innerHTML = `
      <div class="countdown-item">
        <span class="countdown-value">${days}</span>
        <span class="countdown-label">Days</span>
      </div>
      <div class="countdown-item">
        <span class="countdown-value">${hours}</span>
        <span class="countdown-label">Hours</span>
      </div>
      <div class="countdown-item">
        <span class="countdown-value">${minutes}</span>
        <span class="countdown-label">Minutes</span>
      </div>
      <div class="countdown-item">
        <span class="countdown-value">${seconds}</span>
        <span class="countdown-label">Seconds</span>
      </div>
    `;
    
    // Clear interval when countdown is over
    if (distance < 0) {
      clearInterval(countdownInterval);
      countdownElement.innerHTML = '<div class="countdown-expired">Expired</div>';
    }
  }, 1000);
}

/**
 * Initialize a progress bar
 * @param {string} elementId - The ID of the progress bar element
 * @param {number} value - The current progress value (0-100)
 * @param {boolean} animated - Whether to animate the progress bar
 */
function initProgressBar(elementId, value, animated = true) {
  const progressBar = document.getElementById(elementId);
  if (!progressBar) return;
  
  // Get the progress bar inner element
  const progressBarInner = progressBar.querySelector('.progress-bar');
  if (!progressBarInner) return;
  
  // Set the progress value
  progressBarInner.style.width = `${value}%`;
  
  // Add animation class if needed
  if (animated) {
    progressBarInner.classList.add('progress-bar-animated', 'progress-bar-striped');
  }
  
  // Update the progress text if it exists
  const progressText = progressBar.querySelector('.progress-text');
  if (progressText) {
    progressText.textContent = `${value}%`;
  }
}

/**
 * Initialize a modal dialog
 * @param {string} modalId - The ID of the modal element
 */
function initModal(modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  
  // Get all elements that open this modal
  const triggers = document.querySelectorAll(`[data-toggle="modal"][data-target="#${modalId}"]`);
  const closeButtons = modal.querySelectorAll('.close-btn, [data-dismiss="modal"]');
  
  // Add click event to triggers
  triggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      openModal(modal);
    });
  });
  
  // Add click event to close buttons
  closeButtons.forEach(button => {
    button.addEventListener('click', function() {
      closeModal(modal);
    });
  });
  
  // Close modal when clicking outside content
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeModal(modal);
    }
  });
  
  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modal.classList.contains('show')) {
      closeModal(modal);
    }
  });
}

/**
 * Open a modal dialog
 * @param {HTMLElement} modal - The modal element to open
 */
function openModal(modal) {
  // Prevent body scrolling
  document.body.classList.add('modal-open');
  
  // Show the modal
  modal.style.display = 'flex';
  setTimeout(() => {
    modal.classList.add('show');
  }, 10);
  
  // Focus the first input if exists
  const firstInput = modal.querySelector('input, button:not(.close-btn)');
  if (firstInput) {
    setTimeout(() => {
      firstInput.focus();
    }, 300);
  }
  
  // Trigger custom event
  modal.dispatchEvent(new CustomEvent('modal:open'));
}

/**
 * Close a modal dialog
 * @param {HTMLElement} modal - The modal element to close
 */
function closeModal(modal) {
  modal.classList.remove('show');
  
  // Hide modal and re-enable body scrolling after animation
  setTimeout(() => {
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
    
    // Trigger custom event
    modal.dispatchEvent(new CustomEvent('modal:close'));
  }, 300);
}