# Google Analytics 4 Setup Guide - SinaFast Portfolio

**Date**: January 3, 2026  
**Portfolio**: SinaFast - Sinachi Franklin Ezeonyeka  
**Status**: Ready for Implementation

---

## 📊 Overview

Google Analytics 4 (GA4) is essential for tracking visitor behavior, understanding user engagement, and monitoring portfolio performance. This guide provides step-by-step setup instructions.

---

## 🚀 QUICK START

### Step 1: Create a Google Analytics 4 Property

1. Go to [Google Analytics](https://analytics.google.com)
2. Click **"Start measuring"** or sign in to existing account
3. Click **"Create"** → **"Create account"**
4. Fill in account name: `SinaFast Portfolio`
5. Configure Google Analytics property:
   - **Property name**: `SinaFast Website`
   - **Reporting timezone**: `Africa/Lagos` (or your timezone)
   - **Currency**: `NGN` (Nigerian Naira) or `USD`
6. Choose **"Web"** as platform
7. Enter website information:
   - **Website URL**: `https://yourdomain.com`
   - **Website name**: `SinaFast - Sinachi Franklin Ezeonyeka`
8. Select industry category: **Professional Services** or **Information Technology**
9. Review Google Analytics Terms of Service → **Accept**

### Step 2: Create a Web Stream

1. After property creation, select **"Web"** stream
2. Enter stream name: `SinaFast Main Website`
3. Website URL: `https://yourdomain.com`
4. Stream name: `SinaFast Main Website`
5. Click **"Create stream"**

### Step 3: Get Tracking Code

1. You'll see the **Measurement ID** (looks like `G-XXXXXXXXXX`)
2. Copy the entire **Global Site Tag (gtag.js)** code

---

## 💻 IMPLEMENTATION OPTIONS

### Option A: Add to index.html (Recommended)

Add this code to your `<head>` section in [index.html](index.html#L1-L50):

```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
<!-- End Google Analytics -->
```

**Replace `G-XXXXXXXXXX` with your actual Measurement ID**

**Exact placement in index.html**:
- Insert after `<meta charset="utf-8">` line
- Before `<meta name="viewport">` or after all meta tags
- Example: After line 7, before other script tags

### Option B: Create analytics.js Helper File

Create new file: `assets/js/analytics.js`

```javascript
// SinaFast Portfolio - Analytics Configuration
// Replace YOUR_MEASUREMENT_ID with actual GA4 ID

window.dataLayer = window.dataLayer || [];

function gtag(){
  dataLayer.push(arguments);
}

gtag('js', new Date());
gtag('config', 'G-YOUR_MEASUREMENT_ID');

// Track custom events
function trackEvent(category, action, label) {
  gtag('event', action, {
    'event_category': category,
    'event_label': label
  });
}

// Track portfolio link clicks
document.addEventListener('DOMContentLoaded', function() {
  const portfolioLinks = document.querySelectorAll('[data-track="portfolio"]');
  portfolioLinks.forEach(link => {
    link.addEventListener('click', function() {
      trackEvent('portfolio', 'click', this.getAttribute('data-project-name'));
    });
  });
});

// Track service clicks
const serviceButtons = document.querySelectorAll('[data-track="service"]');
serviceButtons.forEach(btn => {
  btn.addEventListener('click', function() {
    trackEvent('services', 'click', this.getAttribute('data-service-name'));
  });
});

// Track contact form submissions
const contactForm = document.getElementById('contact-form');
if (contactForm) {
  contactForm.addEventListener('submit', function() {
    trackEvent('contact', 'form_submit', 'contact_form');
  });
}

// Track scroll depth
let scrollDepth = 0;
window.addEventListener('scroll', function() {
  const docHeight = document.documentElement.scrollHeight - window.innerHeight;
  const scrolled = (window.scrollY / docHeight) * 100;
  
  if (scrolled > 25 && scrollDepth < 25) {
    scrollDepth = 25;
    trackEvent('engagement', 'scroll_depth', '25%');
  }
  if (scrolled > 50 && scrollDepth < 50) {
    scrollDepth = 50;
    trackEvent('engagement', 'scroll_depth', '50%');
  }
  if (scrolled > 75 && scrollDepth < 75) {
    scrollDepth = 75;
    trackEvent('engagement', 'scroll_depth', '75%');
  }
  if (scrolled > 100 && scrollDepth < 100) {
    scrollDepth = 100;
    trackEvent('engagement', 'scroll_depth', '100%');
  }
});
```

Include in index.html:
```html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script src="assets/js/analytics.js"></script>
```

### Option C: Google Tag Manager Setup (Advanced)

For more advanced tracking:

1. Go to [Google Tag Manager](https://tagmanager.google.com)
2. Create new account: `SinaFast Portfolio`
3. Create container: `Website`
4. Add GA4 tag
5. Set up triggers for specific events
6. Deploy container

---

## 📈 KEY METRICS TO TRACK

### Automatic Events (GA4 Default)
- Page views
- Session starts
- Session ends
- User engagement time
- Scroll depth
- Outbound clicks

### Recommended Custom Events

Add these tracking attributes to your HTML:

**Portfolio Links**:
```html
<a href="#" data-track="portfolio" data-project-name="E-Commerce App">View Project</a>
```

**Service Links**:
```html
<button data-track="service" data-service-name="Web Development">Learn More</button>
```

**Contact Form**:
```html
<form id="contact-form" action="forms/contact.php" method="POST">
  <!-- form fields -->
</form>
```

**Social Links**:
```html
<a href="https://twitter.com/yourusername" data-track="social" data-platform="twitter">Twitter</a>
```

---

## ✅ VERIFICATION CHECKLIST

### After Implementation

- [ ] Tracking code added to index.html
- [ ] Google Analytics property created
- [ ] Measurement ID obtained and verified
- [ ] Website deployed/accessible online
- [ ] GA4 shows active users in real-time (check Real-Time Report)
- [ ] Initial page views recorded
- [ ] Events showing in Events report
- [ ] Custom event tracking working

### In Google Analytics Dashboard

1. **Go to Dashboard** → **Real-time report**
2. Visit your website in new tab
3. Should see active user in GA4 within 10 seconds
4. Click around, scroll, submit form
5. Verify events appear in GA4 Events report

---

## 🔧 TROUBLESHOOTING

### Issue: No data showing in GA4

**Solution**:
1. Verify Measurement ID is correct (starts with `G-`)
2. Check browser console for errors (F12 → Console tab)
3. Ensure website is publicly accessible (not localhost for production)
4. Wait 24-48 hours for data processing
5. Check "Reporting Identity" settings

### Issue: Events not tracking

**Solution**:
1. Verify data attributes are correctly named
2. Check JavaScript console for JavaScript errors
3. Use GA4 DebugView to validate events
4. Enable Google Analytics DebugView:
   - Add to URL: `?debug_mode=true`
   - Events should show in DebugView tab

### Issue: Zero users

**Solution**:
1. Ensure you're NOT viewing from same IP as analytics viewer
2. Check property filters aren't excluding your IP
3. Verify tracking code is in the `<head>` section
4. Use incognito/private browser window
5. Check timezone matches your analytics setting

---

## 📊 RECOMMENDED EVENTS TO TRACK

### 1. Portfolio Engagement
```javascript
// Track portfolio filter
gtag('event', 'view_item_list', {
  'items': [
    {'item_name': 'App Projects', 'item_category': 'portfolio'}
  ]
});

// Track portfolio detail view
gtag('event', 'view_item', {
  'items': [
    {'item_name': 'E-Commerce Mobile App', 'item_category': 'portfolio'}
  ]
});
```

### 2. Service Interest
```javascript
// Track service view
gtag('event', 'view_promotion', {
  'promotion_name': 'Web Development Service'
});

// Track service inquiry
gtag('event', 'generate_lead', {
  'value': 0,
  'currency': 'NGN',
  'lead_type': 'Service Inquiry'
});
```

### 3. Contact Form Interaction
```javascript
// Track form start
gtag('event', 'sign_up', {
  'method': 'contact_form'
});

// Track form submission (in contact.php success response)
gtag('event', 'contact_form_submit', {
  'event_category': 'engagement',
  'event_label': 'form_success'
});
```

### 4. External Link Tracking
```javascript
// Track social media clicks
gtag('event', 'click', {
  'event_category': 'social',
  'event_label': 'twitter'
});

// Track GitHub/Portfolio link clicks
gtag('event', 'outbound_click', {
  'destination': 'github.com/yourusername',
  'event_category': 'outbound'
});
```

---

## 🎯 ANALYTICS GOALS

Set up these conversion goals:

### Goal 1: Contact Form Submission
- **Name**: Contact Form Submission
- **Type**: Event
- **Event name**: contact_form_submit
- **Value**: $0 (informational)

### Goal 2: Portfolio Project Views
- **Name**: Portfolio Project View
- **Type**: Event
- **Event name**: view_item
- **Condition**: item_category = portfolio

### Goal 3: Service Inquiry
- **Name**: Service Inquiry
- **Type**: Event
- **Event name**: generate_lead

### Goal 4: Page Scroll (25%+)
- **Name**: Deep Page Engagement
- **Type**: Event
- **Event name**: scroll_depth
- **Condition**: Greater than 50

---

## 📱 MOBILE ANALYTICS SETUP

GA4 automatically tracks mobile devices. To enhance:

1. Create mobile-specific views in Google Analytics
2. Track app vs web separately if needed
3. Monitor mobile conversion rates
4. Test on actual mobile devices

**Mobile Testing**:
```
Device Testing:
- iPhone (Safari)
- Android (Chrome)
- Tablet (iPad, Samsung Tab)
- Different screen sizes (320px, 768px, 1024px+)
```

---

## 🔐 PRIVACY & COMPLIANCE

### GDPR Compliance (if serving EU users)

1. Add consent banner to site
2. Make GA4 dependent on user consent
3. Allow users to opt-out
4. Include privacy policy

### Sample Privacy Notice
```
This site uses Google Analytics to understand how visitors use our site. 
Google Analytics uses cookies to collect information about your visit 
in an anonymous form. For more information, see Google's privacy policy.
```

### Add to privacy policy:
- Google Analytics data collection
- Cookie usage
- User data retention (default: 14 months)
- Opt-out options

---

## 📊 DASHBOARD SETUP

### Create Custom Dashboard

**Recommended Reports**:

1. **Overview Dashboard**
   - Users (daily)
   - Sessions
   - Avg session duration
   - Bounce rate
   - Top pages

2. **Portfolio Performance**
   - Portfolio page views
   - Scroll depth on portfolio
   - Portfolio filter clicks
   - Average time on portfolio

3. **Conversion Funnel**
   - Contact form views
   - Form starts
   - Form completions
   - Conversion rate

4. **Traffic Sources**
   - Organic search
   - Direct visits
   - Referral traffic
   - Social traffic

5. **Geographic**
   - Top countries
   - Top cities
   - Language
   - Device type

---

## 📈 PERFORMANCE BENCHMARKS

**Target Metrics** (First Month):
- Sessions: 50-200
- Users: 30-150
- Bounce rate: 40-60%
- Avg session duration: 1-3 minutes
- Contact form submission rate: 2-5%

**Target Metrics** (First Quarter):
- Sessions: 200-1000
- Users: 150-750
- Bounce rate: 35-50%
- Avg session duration: 2-4 minutes
- Contact form submission rate: 3-8%

---

## 🔗 ADDITIONAL RESOURCES

- [Google Analytics 4 Documentation](https://support.google.com/analytics)
- [GA4 Setup Guide](https://support.google.com/analytics/answer/10089681)
- [Google Analytics Debugger Extension](https://chrome.google.com/webstore)
- [GTM Implementation Guide](https://support.google.com/tagmanager)

---

## ✅ IMPLEMENTATION CHECKLIST

- [ ] Create Google Analytics account
- [ ] Create GA4 property
- [ ] Create web stream
- [ ] Copy Measurement ID (G-XXXXXXXXXX)
- [ ] Add tracking code to index.html
- [ ] Deploy website
- [ ] Verify real-time users showing in GA
- [ ] Set up custom events for portfolio interactions
- [ ] Create conversion goals
- [ ] Set up custom dashboard
- [ ] Configure reporting timezone
- [ ] Add Google Search Console verification
- [ ] Submit sitemap to Search Console
- [ ] Monitor reports daily for first week
- [ ] Adjust events based on initial data

---

## 📞 SUPPORT

For questions about GA4:
- Visit Google Analytics Help: [support.google.com/analytics](https://support.google.com/analytics)
- Check Analytics Community: [support.google.com/analytics/community](https://support.google.com/analytics/community)
- Contact: [Your Email] or [Your Website]

---

**Last Updated**: January 3, 2026  
**Next Review**: Quarterly (April 3, 2026)
