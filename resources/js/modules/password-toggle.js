/**
 * Toggles a password field between type="password" and type="text" when
 * its adjacent [data-password-toggle] button is clicked.
 */
export function initPasswordToggle() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const input = button.closest('.av-field__input-group')?.querySelector('[data-password-toggle-input]');
        if (!input) return;

        button.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    });
}
