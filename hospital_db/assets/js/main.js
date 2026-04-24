// ===== NAVBAR TOGGLE =====
function toggleMenu() {
    document.getElementById('navMenu').classList.toggle('open');
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

// ===== AUTO HIDE ALERT =====
document.addEventListener('DOMContentLoaded', () => {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(a => {
        setTimeout(() => { a.style.opacity = '0'; a.style.transition = '.5s'; }, 3000);
        setTimeout(() => a.remove(), 3600);
    });
});
