/**
 * Lightweight client-side validation for forms marked .js-validate
 * Server-side validation remains mandatory in PHP handlers.
 */
(function () {
  function showError(input, message) {
    var row = input.closest('.form-row');
    if (!row) return;
    var existing = row.querySelector('.form-error');
    if (existing) existing.remove();
    var el = document.createElement('div');
    el.className = 'form-error';
    el.setAttribute('role', 'alert');
    el.textContent = message;
    row.appendChild(el);
  }

  function clearErrors(form) {
    form.querySelectorAll('.form-error').forEach(function (n) {
      n.remove();
    });
  }

  function validateField(input) {
    if (input.disabled) return true;
    if (input.hasAttribute('required') && !String(input.value || '').trim()) {
      showError(input, 'This field is required.');
      return false;
    }
    if (input.type === 'email' && input.value && !input.validity.valid) {
      showError(input, 'Enter a valid email address.');
      return false;
    }
    return true;
  }

  document.querySelectorAll('form.js-validate').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      clearErrors(form);
      var ok = true;
      var fields = form.querySelectorAll('input, select, textarea');
      fields.forEach(function (input) {
        if (!validateField(input)) ok = false;
      });
      var confirmPw = form.querySelector('input[name="password_confirm"]');
      if (confirmPw && ok) {
        var pw = form.querySelector('input[name="password"]');
        if (pw && String(pw.value) !== String(confirmPw.value)) {
          showError(confirmPw, 'Passwords must match.');
          ok = false;
        }
      }
      if (!ok) {
        e.preventDefault();
        var first = form.querySelector('.form-error');
        if (first) first.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      }
    });
  });
})();
