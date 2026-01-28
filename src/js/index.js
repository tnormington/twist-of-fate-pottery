import 'bootstrap'
import "../sass/style.sass"

// Mask Button Effect - Inject required elements
document.addEventListener('DOMContentLoaded', function() {
  // Select all buttons that should have the mask effect
  const buttonSelectors = [
    '.wp-block-button__link',
    '.wp-element-button',
    '.woocommerce ul.products li.product .button',
    '.woocommerce ul.products li.product a.button',
    '.woocommerce a.button',
    '.woocommerce button.button',
    '.woocommerce input.button',
    '.tease-product .button',
    '.btn-primary',
    '.wpcf7-form input[type="submit"]'
  ];

  const buttons = document.querySelectorAll(buttonSelectors.join(', '));

  buttons.forEach(function(button) {
    // Skip if already wrapped
    if (button.closest('.btn-mask-container')) return;

    // Get button text
    const buttonText = button.textContent || button.value || '';

    // Create wrapper
    const wrapper = document.createElement('div');
    wrapper.className = 'btn-mask-container';

    // Create mas span
    const masSpan = document.createElement('span');
    masSpan.className = 'mas';
    masSpan.textContent = buttonText;

    // Wrap the button
    button.parentNode.insertBefore(wrapper, button);
    wrapper.appendChild(masSpan);
    wrapper.appendChild(button);

    // Add class to button for mask styling
    button.classList.add('mask-btn');

    // Mark as ready (triggers visibility)
    requestAnimationFrame(function() {
      wrapper.classList.add('ready');
    });

    // Add 'has-hovered' class on first hover to enable reverse animation
    button.addEventListener('mouseenter', function() {
      wrapper.classList.add('has-hovered');
    }, { once: true });
  });
});
