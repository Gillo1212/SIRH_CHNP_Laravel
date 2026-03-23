import './bootstrap';
// Bootstrap JS et Alpine.js sont chargés via CDN dans master.blade.php
// Ne pas les importer ici pour éviter le double chargement et les conflits

// ============================================
// GESTION DE LA LANGUE
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const langToggle = document.getElementById('langToggle');
    if (langToggle) {
        langToggle.addEventListener('change', function () {
            window.location.href = `/lang/${this.value}`;
        });
    }
});
