(function () {
  'use strict';

  var cookieName = 'lms_cookie_consent';
  var config = window.lmsCookieConsent || {};

  function getCookie(name) {
    return document.cookie
      .split('; ')
      .find(function (row) { return row.indexOf(name + '=') === 0; });
  }

  function saveConsent(value) {
    var form = new FormData();
    form.append('choice', value);
    form.append('_csrf', config.csrfToken || '');

    return fetch(config.endpoint || 'cookie_consent.php', {
      method: 'POST',
      body: form,
      credentials: 'same-origin',
      headers: {
        'X-CSRF-Token': config.csrfToken || ''
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Cookie consent request failed');
      }
      return response.json();
    }).then(function (payload) {
      if (!payload || payload.ok !== true) {
        throw new Error('Cookie consent was not saved');
      }
      config.hasChoice = true;
    });
  }

  function buildBanner() {
    var banner = document.createElement('section');
    banner.className = 'cookie-consent';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-live', 'polite');
    banner.setAttribute('aria-label', 'Cookie notice');
    banner.innerHTML = [
      '<div class="cookie-consent__copy">',
      '<strong>Cookie notice</strong>',
      '<p>Grafix@Mirror LMS uses essential cookies to keep you signed in, protect your account, and remember basic site preferences. <a href="cookie_policy.php">Learn more</a>.</p>',
      '</div>',
      '<div class="cookie-consent__actions">',
      '<button class="cookie-consent__btn cookie-consent__btn--ghost" type="button" data-cookie-choice="necessary">Necessary only</button>',
      '<button class="cookie-consent__btn cookie-consent__btn--primary" type="button" data-cookie-choice="accepted">Accept cookies</button>',
      '</div>'
    ].join('');
    return banner;
  }

  function init() {
    if (config.hasChoice || getCookie(cookieName)) return;

    var banner = buildBanner();
    document.body.appendChild(banner);

    banner.addEventListener('click', function (event) {
      var button = event.target.closest('[data-cookie-choice]');
      if (!button) return;

      button.disabled = true;
      saveConsent(button.getAttribute('data-cookie-choice'))
        .then(function () {
          banner.classList.add('cookie-consent--hiding');
          window.setTimeout(function () {
            banner.remove();
          }, 180);
        })
        .catch(function () {
          button.disabled = false;
        });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
}());
