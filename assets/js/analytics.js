// SinaFast Portfolio - Analytics helper
// Replace 'G-XXXXXXXXXX' in index.html with your GA4 Measurement ID

// Utility to send custom events via gtag
function trackEvent(category, action, label, value) {
  if (typeof gtag !== 'function') return;
  gtag('event', action, {
    'event_category': category,
    'event_label': label,
    'value': value || 0
  });
}

// Track portfolio/project clicks
document.addEventListener('DOMContentLoaded', function() {
  // portfolio links have data-track="portfolio" and data-project-name attributes
  document.querySelectorAll('[data-track="portfolio"]').forEach(function(el){
    el.addEventListener('click', function(){
      trackEvent('portfolio', 'click', el.getAttribute('data-project-name') || el.href);
    });
  });

  // service buttons
  document.querySelectorAll('[data-track="service"]').forEach(function(el){
    el.addEventListener('click', function(){
      trackEvent('services', 'click', el.getAttribute('data-service-name') || el.textContent.trim());
    });
  });

  // contact form submission (assumes form id="contact-form")
  var contactForm = document.getElementById('contact-form');
  if (contactForm) {
    contactForm.addEventListener('submit', function(){
      trackEvent('contact', 'form_submit', 'contact_form');
    });
  }

  // basic scroll depth events
  var scrollDepth = 0;
  window.addEventListener('scroll', function(){
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    var scrolled = (window.scrollY / docHeight) * 100;
    if (scrolled > 25 && scrollDepth < 25) { scrollDepth = 25; trackEvent('engagement','scroll_depth','25%'); }
    if (scrolled > 50 && scrollDepth < 50) { scrollDepth = 50; trackEvent('engagement','scroll_depth','50%'); }
    if (scrolled > 75 && scrollDepth < 75) { scrollDepth = 75; trackEvent('engagement','scroll_depth','75%'); }
    if (scrolled > 99 && scrollDepth < 100) { scrollDepth = 100; trackEvent('engagement','scroll_depth','100%'); }
  });
});
