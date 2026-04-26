// ===== DARK MODE =====
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// Apply saved theme on page load (before DOMContentLoaded to prevent flash)
(function() {
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') {
        document.body.classList.add('dark-mode');
    }
})();

// ===== SIDEBAR TOGGLE =====
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('active');
}

// ===== SEARCH TABLE =====
function searchTable(inputId, tableId) {
    const filter = document.getElementById(inputId).value.toLowerCase();
    const rows   = document.querySelectorAll('#' + tableId + ' tbody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
}

// ===== CONFIRM DELETE =====
function confirmDelete(url, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = url;
    document.getElementById('deleteModal').classList.add('active');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

// ===== LIVE CLOCK =====
function updateClock() {
    const el = document.getElementById('liveClock');
    if (!el) return;
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    const s = String(now.getSeconds()).padStart(2, '0');
    el.querySelector('span').textContent = `${h}:${m}:${s}`;
}

// ===== DOM READY =====
document.addEventListener('DOMContentLoaded', () => {
    // Alert auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(a => {
        setTimeout(() => {
            a.style.opacity = '0';
            a.style.transform = 'translateY(-10px)';
            a.style.transition = '.4s ease';
        }, 4000);
        setTimeout(() => a.remove(), 4500);
    });

    // Live clock
    setInterval(updateClock, 1000);

    // Close sidebar on resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
    });
});
