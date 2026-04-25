<?php
// ============================================================
// CRUD: MEDICATIONS
// ============================================================

$msg = '';

if (isset($_POST['add_med'])) {
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $cat   = mysqli_real_escape_string($conn, trim($_POST['category']));
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if ($name && $price >= 0) {
        mysqli_query($conn, "INSERT INTO medications (name, category, price, stock)
            VALUES ('$name','$cat',$price,$stock)");
        $msg = '<div class="alert alert-success">✅ Medication added successfully.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ Medication name is required.</div>';
    }
}

if (isset($_POST['edit_med'])) {
    $id    = (int)$_POST['med_id'];
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $cat   = mysqli_real_escape_string($conn, trim($_POST['category']));
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    mysqli_query($conn, "UPDATE medications SET
        name='$name', category='$cat', price=$price, stock=$stock
        WHERE med_id=$id");
    $msg = '<div class="alert alert-success">✅ Medication data updated successfully.</div>';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM medications WHERE med_id=$id");
    $msg = '<div class="alert alert-success">✅ Medication deleted successfully.</div>';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM medications WHERE med_id=$id");
    $editData = mysqli_fetch_assoc($res);
}

$meds = mysqli_query($conn, "SELECT * FROM medications ORDER BY name");
?>

<div class="page-header">
    <h2>💊 Medication Management</h2>
</div>

<?= $msg ?>

<div class="card">
    <h3><?= $editData ? '✏️ Edit Medication' : '➕ Add Medication' ?></h3>
    <form method="POST" action="index.php?page=medications<?= $editData ? '&edit='.$editData['med_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Medication Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($editData['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" value="<?= htmlspecialchars($editData['category'] ?? '') ?>" placeholder="Analgesic, Antibiotic...">
            </div>
            <div class="form-group">
                <label>Price (Rp) *</label>
                <input type="number" name="price" min="0" step="500" value="<?= $editData['price'] ?? '0' ?>" required>
            </div>
            <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" min="0" value="<?= $editData['stock'] ?? '0' ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_med" class="btn btn-warning">💾 Save Changes</button>
                <a href="index.php?page=medications" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_med" class="btn btn-primary">➕ Add Medication</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-top">
        <strong>Medication List (<?= mysqli_num_rows($meds) ?>)</strong>
        <input type="text" id="searchMed" placeholder="🔍 Search medications..." oninput="searchTable('searchMed','medTable')">
    </div>
    <table id="medTable">
        <thead>
            <tr><th>#</th><th>Medication Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php $no=1; while ($row = mysqli_fetch_assoc($meds)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category'] ?: '-') ?></td>
                <td>Rp <?= number_format($row['price'], 0, ',', '.') ?></td>
                <td>
                    <?php $cls = $row['stock'] < 10 ? 'badge-danger' : ($row['stock'] < 50 ? 'badge-warning' : 'badge-success'); ?>
                    <span class="badge <?= $cls ?>"><?= $row['stock'] ?></span>
                </td>
                <td>
                    <div class="action-btns">
                        <a href="index.php?page=medications&edit=<?= $row['med_id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmDelete('index.php?page=medications&delete=<?= $row['med_id'] ?>','<?= addslashes($row['name']) ?>')">
                            🗑️ Delete
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
