<?php
// ============================================================
// CRUD: DOCTORS
// ============================================================

$msg = '';

if (isset($_POST['add_doctor'])) {
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $spec  = mysqli_real_escape_string($conn, trim($_POST['specialization']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    if ($name && $spec) {
        mysqli_query($conn, "INSERT INTO doctors (name, specialization, phone, email)
            VALUES ('$name','$spec','$phone','$email')");
        $msg = '<div class="alert alert-success">✅ Dokter berhasil ditambahkan.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ Nama dan spesialisasi wajib diisi.</div>';
    }
}

if (isset($_POST['edit_doctor'])) {
    $id    = (int)$_POST['doctor_id'];
    $name  = mysqli_real_escape_string($conn, trim($_POST['name']));
    $spec  = mysqli_real_escape_string($conn, trim($_POST['specialization']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    mysqli_query($conn, "UPDATE doctors SET
        name='$name', specialization='$spec', phone='$phone', email='$email'
        WHERE doctor_id=$id");
    $msg = '<div class="alert alert-success">✅ Data dokter berhasil diperbarui.</div>';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM doctors WHERE doctor_id=$id");
    $msg = '<div class="alert alert-success">✅ Dokter berhasil dihapus.</div>';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM doctors WHERE doctor_id=$id");
    $editData = mysqli_fetch_assoc($res);
}

$doctors = mysqli_query($conn, "SELECT * FROM doctors ORDER BY name");
?>

<div class="page-header">
    <h2>👨‍⚕️ Manajemen Dokter</h2>
</div>

<?= $msg ?>

<div class="card">
    <h3><?= $editData ? '✏️ Edit Dokter' : '➕ Tambah Dokter' ?></h3>
    <form method="POST" action="index.php?page=doctors<?= $editData ? '&edit='.$editData['doctor_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Dokter *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($editData['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Spesialisasi *</label>
                <input type="text" name="specialization" value="<?= htmlspecialchars($editData['specialization'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($editData['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($editData['email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_doctor" class="btn btn-warning">💾 Simpan Perubahan</button>
                <a href="index.php?page=doctors" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_doctor" class="btn btn-primary">➕ Tambah Dokter</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-top">
        <strong>Daftar Dokter (<?= mysqli_num_rows($doctors) ?>)</strong>
        <input type="text" id="searchDoc" placeholder="🔍 Cari dokter..." oninput="searchTable('searchDoc','doctorTable')">
    </div>
    <table id="doctorTable">
        <thead>
            <tr><th>#</th><th>Nama</th><th>Spesialisasi</th><th>Telepon</th><th>Email</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php $no=1; while ($row = mysqli_fetch_assoc($doctors)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><span class="badge badge-info"><?= htmlspecialchars($row['specialization']) ?></span></td>
                <td><?= htmlspecialchars($row['phone'] ?: '-') ?></td>
                <td><?= htmlspecialchars($row['email'] ?: '-') ?></td>
                <td>
                    <div class="action-btns">
                        <a href="index.php?page=doctors&edit=<?= $row['doctor_id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmDelete('index.php?page=doctors&delete=<?= $row['doctor_id'] ?>','<?= addslashes($row['name']) ?>')">
                            🗑️ Hapus
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
