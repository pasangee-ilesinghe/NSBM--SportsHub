
   
(function () {
    'use strict';

    var NAME_RE  = /^[A-Za-z\u00C0-\u024F][A-Za-z\u00C0-\u024F .'\-]{1,99}$/;
    var EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    var ID_RE    = /^\d{4,10}$/;

    function getFeedback(field) {
        var next = field.nextElementSibling;
        if (next && next.classList.contains('invalid-feedback')) { return next; }
        var el = document.createElement('div');
        el.className = 'invalid-feedback';
        field.parentNode.insertBefore(el, field.nextSibling);
        return el;
    }

    
    function check(field, form) {
        if (field.type === 'hidden' || field.disabled || field.readOnly) { return ''; }
        if (field.type !== 'hidden' && field.offsetParent === null) { return ''; } // hidden section of the form

        var isPassword = field.type === 'password';
        var value = isPassword ? field.value : field.value.trim();
        var mode = form.getAttribute('data-validate');

        if (field.required && value === '') { return 'This field is required.'; }
        if (value === '') { return ''; }

        if (field.type === 'email' && !EMAIL_RE.test(value)) {
            return 'Enter a valid email address.';
        }
        if (field.minLength > 0 && value.length < field.minLength) {
            return 'Must be at least ' + field.minLength + ' characters.';
        }
        if (field.type === 'date' && field.min && value < field.min) {
            return 'The date cannot be before ' + field.min + '.';
        }
        if (mode === 'student') {
            if (field.name === 'name' && !NAME_RE.test(value)) {
                return 'Use letters only (2-100 characters).';
            }
            if (field.name === 'student_id' && !ID_RE.test(value)) {
                return 'Student ID must be 4-10 digits.';
            }
        }
        if (field.name === 'confirm_password') {
            var other = form.querySelector('[name="new_password"], [name="password"]');
            if (other && other.value !== field.value) { return 'Passwords do not match.'; }
        }
        return '';
    }

    function show(field, message) {
        var feedback = getFeedback(field);
        if (message) {
            field.classList.add('is-invalid');
            feedback.textContent = message;
        } else {
            field.classList.remove('is-invalid');
            feedback.textContent = '';
        }
    }

    function fields(form) {
        return form.querySelectorAll('input, select, textarea');
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.setAttribute('novalidate', ''); // we show our own messages

        fields(form).forEach(function (field) {
            var recheck = function () { show(field, check(field, form)); };
            field.addEventListener('blur', function () {
                if (field.value !== '' || field.classList.contains('is-invalid')) { recheck(); }
            });
            field.addEventListener('input', function () {
                if (field.classList.contains('is-invalid')) { recheck(); }
            });
            field.addEventListener('change', recheck);
        });

        form.addEventListener('submit', function (event) {
            var firstBad = null;
            fields(form).forEach(function (field) {
                if (field.type !== 'password' && field.type !== 'hidden' && field.type !== 'file' &&
                    typeof field.value === 'string' && !field.readOnly) {
                    field.value = field.value.trim();
                }
                var message = check(field, form);
                show(field, message);
                if (message && !firstBad) { firstBad = field; }
            });
            if (firstBad) {
                event.preventDefault();
                firstBad.focus();
            }
        });
    });
})();
