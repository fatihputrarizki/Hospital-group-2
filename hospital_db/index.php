<?php
include "config/db.php";
include "includes/header.php";
include "includes/navbar.php";

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<div class="container">
<?php
$allowed = ['dashboard','patients','doctors','appointments','records','medications','prescriptions'];
if (in_array($page, $allowed)) {
    include "pages/{$page}.php";
} else {
    include "pages/dashboard.php";
}
?>
</div>

<!-- ===== MODAL KONFIRMASI HAPUS ===== -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus data <strong id="deleteItemName"></strong>?<br>
           <small style="color:#6b7280">Data yang dihapus tidak bisa dikembalikan.</small></p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <a id="confirmDeleteBtn" class="btn btn-danger" href="#">Hapus</a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
