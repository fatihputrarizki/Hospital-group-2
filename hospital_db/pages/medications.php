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
        $msg = '<div class="alert alert-success">✅ Obat berhasil ditambahkan.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ Nama obat wajib diisi.</div>';
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
    $msg = '<div class="alert alert-success">✅ Data obat berhasil diperbarui.</div>';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM medications WHERE med_id=$id");
    $msg = '<div class="alert alert-success">✅ Obat berhasil dihapus.</div>';
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
    <h2>💊 Manajemen Obat</h2>
</div>

<?= $msg ?>

<div class="card">
    <h3><?= $editData ? '✏️ Edit Obat' : '➕ Tambah Obat' ?></h3>
    <form method="POST" action="index.php?page=medications<?= $editData ? '&edit='.$editData['med_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Obat *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($editData['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="category" value="<?= htmlspecialchars($editData['category'] ?? '') ?>" placeholder="Analgesik, Antibiotik...">
            </div>
            <div class="form-group">
                <label>Harga (Rp) *</label>
                <input type="number" name="price" min="0" step="500" value="<?= $editData['price'] ?? '0' ?>" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stock" min="0" value="<?= $editData['stock'] ?? '0' ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_med" class="btn btn-warning">💾 Simpan Perubahan</button>
                <a href="index.php?page=medications" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_med" class="btn btn-primary">➕ Tambah Obat</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-top">
        <strong>Daftar Obat (<?= mysqli_num_rows($meds) ?>)</strong>
        <input type="text" id="searchMed" placeholder="🔍 Cari obat..." oninput="searchTable('searchMed','medTable')">
    </div>
    <table id="medTable">
        <thead>
            <tr><th>#</th><th>Nama Obat</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
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
                            🗑️ Hapus
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
