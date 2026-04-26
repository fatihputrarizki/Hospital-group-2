<?php
include "config/db.php";
include "includes/header.php";
include "includes/navbar.php";

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<main class="main-content">
    <div class="content-wrapper">
        <?php
        $allowed = ['dashboard','patients','doctors','appointments','records','medications','prescriptions'];
        if (in_array($page, $allowed)) {
            include "pages/{$page}.php";
        } else {
            include "pages/dashboard.php";
        }
        ?>
    </div>
</main>

<!-- ===== DELETE CONFIRMATION MODAL ===== -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <div class="modal-icon">
            <i class='bx bxs-error-circle'></i>
        </div>
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?<br>
           <small>This action cannot be undone.</small></p>
        <div class="modal-actions">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class='bx bx-x'></i> Cancel
            </button>
            <a id="confirmDeleteBtn" class="btn btn-danger" href="#">
                <i class='bx bx-trash'></i> Delete
            </a>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
