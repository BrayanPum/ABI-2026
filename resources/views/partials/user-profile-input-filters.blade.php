<script>
    document.addEventListener('DOMContentLoaded', () => {
        const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/u;
        const DEFAULT_EMAIL_MESSAGE = 'Ingrese un correo electronico valido (ejemplo: usuario@dominio.com).';
        const PHONE_LENGTH = 10;
        const DEFAULT_PHONE_MESSAGE = 'El telefono debe tener exactamente 10 digitos (ejemplo: 3158899001).';

        const filters = {
            name: (value) => value.replace(/[^\p{L}\s'-]/gu, ''),
            digits: (value) => value.replace(/\D/g, ''),
            'phone-co': (value) => value.replace(/\D/g, '').slice(0, PHONE_LENGTH),
        };

        function getFieldContainer(input) {
            return input.closest('.mb-3, .col-12') || input.parentElement;
        }

        function getLiveFeedback(input, selector) {
            const container = getFieldContainer(input);
            let feedback = container.querySelector(selector);

            if (!feedback) {
                feedback = container.querySelector('.invalid-feedback');
            }

            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block';
                feedback.dataset.liveEmailFeedback = selector.includes('email') ? '' : undefined;
                feedback.dataset.livePhoneFeedback = selector.includes('phone') ? '' : undefined;
                container.appendChild(feedback);
            }

            if (!feedback.dataset.defaultMessage) {
                feedback.dataset.defaultMessage = feedback.textContent.trim()
                    || (selector.includes('phone') ? DEFAULT_PHONE_MESSAGE : DEFAULT_EMAIL_MESSAGE);
            }

            return feedback;
        }

        function setFieldValidity(input, isValid, feedback, message = null) {
            const displayMessage = message || feedback.dataset.defaultMessage;

            if (isValid) {
                input.classList.remove('is-invalid');
                input.setCustomValidity('');
                feedback.textContent = feedback.dataset.defaultMessage;
                feedback.classList.add('d-none');
                feedback.classList.remove('d-block');
                return;
            }

            input.classList.add('is-invalid');
            input.setCustomValidity(displayMessage);
            feedback.textContent = displayMessage;
            feedback.classList.remove('d-none');
            feedback.classList.add('d-block');
        }

        function isActiveInput(input) {
            return !input.disabled && input.offsetParent !== null;
        }

        function validateEmailInput(input, { showEmptyError = false } = {}) {
            const feedback = getLiveFeedback(input, '[data-live-email-feedback]');

            if (!isActiveInput(input)) {
                setFieldValidity(input, true, feedback);
                return true;
            }

            const value = input.value.trim();

            if (value === '') {
                if (showEmptyError && input.required) {
                    setFieldValidity(input, false, feedback, 'El correo electronico es obligatorio.');
                    return false;
                }

                setFieldValidity(input, true, feedback);
                return !input.required;
            }

            const isValid = EMAIL_PATTERN.test(value);
            setFieldValidity(input, isValid, feedback);
            return isValid;
        }

        function validatePhoneInput(input, { showEmptyError = false } = {}) {
            const feedback = getLiveFeedback(input, '[data-live-phone-feedback]');

            if (!isActiveInput(input)) {
                setFieldValidity(input, true, feedback);
                return true;
            }

            const value = input.value.trim();

            if (value === '') {
                if (showEmptyError && input.required) {
                    setFieldValidity(input, false, feedback, 'El telefono es obligatorio.');
                    return false;
                }

                setFieldValidity(input, true, feedback);
                return !input.required;
            }

            const isValid = /^\d+$/.test(value) && value.length === PHONE_LENGTH;
            setFieldValidity(input, isValid, feedback);
            return isValid;
        }

        document.querySelectorAll('[data-input-filter]').forEach((input) => {
            const filterKey = input.dataset.inputFilter;

            if (!filters[filterKey]) {
                return;
            }

            const sanitize = filters[filterKey];

            input.addEventListener('input', () => {
                const cleaned = sanitize(input.value);

                if (cleaned !== input.value) {
                    input.value = cleaned;
                }

                if (filterKey === 'phone-co' && input.dataset.touched === 'true') {
                    validatePhoneInput(input);
                }
            });

            input.addEventListener('paste', (event) => {
                event.preventDefault();
                const pasted = (event.clipboardData || window.clipboardData).getData('text');
                const start = input.selectionStart ?? input.value.length;
                const end = input.selectionEnd ?? input.value.length;
                const merged = input.value.slice(0, start) + pasted + input.value.slice(end);
                const cleaned = sanitize(merged);

                input.value = cleaned;
                input.setSelectionRange(cleaned.length, cleaned.length);

                if (filterKey === 'phone-co' && input.dataset.touched === 'true') {
                    validatePhoneInput(input);
                }
            });
        });

        document.querySelectorAll('[data-live-validate="email"]').forEach((input) => {
            const validateIfNeeded = () => {
                if (!isActiveInput(input)) {
                    return;
                }

                const value = input.value.trim();
                const shouldValidate = input.dataset.touched === 'true' || value !== '';

                if (!shouldValidate) {
                    return;
                }

                validateEmailInput(input);
            };

            input.addEventListener('input', validateIfNeeded);

            input.addEventListener('blur', () => {
                input.dataset.touched = 'true';
                validateEmailInput(input, { showEmptyError: true });
            });
        });

        document.querySelectorAll('[data-live-validate="phone-co"]').forEach((input) => {
            const validateIfNeeded = () => {
                if (!isActiveInput(input)) {
                    return;
                }

                const value = input.value.trim();
                const shouldValidate = input.dataset.touched === 'true' || value !== '';

                if (!shouldValidate) {
                    return;
                }

                validatePhoneInput(input);
            };

            input.addEventListener('input', validateIfNeeded);

            input.addEventListener('blur', () => {
                input.dataset.touched = 'true';
                validatePhoneInput(input, { showEmptyError: true });
            });
        });

        document.querySelectorAll('form').forEach((form) => {
            const hasLiveValidation = form.querySelector('[data-live-validate]');

            if (!hasLiveValidation) {
                return;
            }

            form.addEventListener('submit', (event) => {
                let isValid = true;
                let firstInvalid = null;

                form.querySelectorAll('[data-live-validate="email"]').forEach((input) => {
                    if (!isActiveInput(input)) {
                        return;
                    }

                    input.dataset.touched = 'true';

                    if (!validateEmailInput(input, { showEmptyError: true })) {
                        isValid = false;
                        firstInvalid = firstInvalid || input;
                    }
                });

                form.querySelectorAll('[data-live-validate="phone-co"]').forEach((input) => {
                    if (!isActiveInput(input)) {
                        return;
                    }

                    input.dataset.touched = 'true';

                    if (!validatePhoneInput(input, { showEmptyError: true })) {
                        isValid = false;
                        firstInvalid = firstInvalid || input;
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                    firstInvalid?.focus();
                }
            });
        });
    });
</script>
