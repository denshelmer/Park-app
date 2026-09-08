/**
 * ParkApp - Scripts Globales
 */
document.addEventListener('DOMContentLoaded', () => {
    // Auto-ocultar alertas temporales
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 4000);
    });
});
