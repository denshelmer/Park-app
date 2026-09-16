/**
 * ParkApp - Scripts Globales
 */

// Función global para alternar visibilidad de contraseñas
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Auto-ocultar alertas de éxito temporales tras 4 segundos
    const successAlerts = document.querySelectorAll('.classic-alert-success, .alert-success');
    successAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            setTimeout(() => {
                if (alert.parentNode) alert.parentNode.removeChild(alert);
            }, 400);
        }, 4000);
    });

    // Filtro en tiempo real para inputs numéricos estrictos (Teléfono, CI número)
    const numericInputs = document.querySelectorAll('input[type="tel"], input.numeric-only');
    numericInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
        input.addEventListener('keypress', (e) => {
            if (e.which < 48 || e.which > 57) {
                if (e.which !== 8 && e.which !== 0) {
                    e.preventDefault();
                }
            }
        });
    });
});
