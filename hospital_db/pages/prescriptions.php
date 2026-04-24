<?php
// ============================================================
// CRUD: PRESCRIPTIONS
// ============================================================

$msg = '';

if (isset($_POST['add_prescription'])) {
    $record_id = (int)$_POST['record_id'];
    $med_id    = (int)$_POST['med_id'];
    $dosage    = mysqli_real_escape_string($conn, trim($_POST['dosage']));
    $duration  = (int)$_POST['duration_days'];

    if ($record_id && $med_id && $dosage) {
        mysqli_query($conn, "INSERT INTO prescriptions (record_id, med_id, dosage, duration_days)
            VALUES ($record_id, $med_id, '$dosage', $duration)");
        $msg = '<div class="alert alert-success">✅ Resep berhasil ditambahkan.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ Semua kolom wajib diisi.</div>';
    }
}

if (isset($_POST['edit_prescription'])) {
    $id        = (int)$_POST['prescription_id'];
    $record_id = (int)$_POST['record_id'];
    $med_id    = (int)$_POST['med_id'];
    $dosage    = mysqli_real_escape_string($conn, trim($_POST['dosage']));
    $duration  = (int)$_POST['duration_days'];

    mysqli_query($conn, "UPDATE prescriptions SET
        record_id=$record_id, med_id=$med_id, dosage='$dosage', duration_days=$duration
        WHERE prescription_id=$id");
    $msg = '<div class="alert alert-success">✅ Resep berhasil diperbarui.</div>';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM prescriptions WHERE prescription_id=$id");
    $msg = '<div class="alert alert-success">✅ Resep berhasil dihapus.</div>';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM prescriptions WHERE prescription_id=$id");
    $editData = mysqli_fetch_assoc($res);
}

$prescriptions = mysqli_query($conn, "
    SELECT pr.*, p.name AS patient_name, m.name AS med_name,
           mr.diagnosis, mr.record_date
    FROM prescriptions pr
    JOIN medical_records mr ON pr.record_id = mr.record_id
    JOIN patients p ON mr.patient_id = p.patient_id
    JOIN medications m ON pr.med_id = m.med_id
    ORDER BY pr.created_at DESC
");
$records = mysqli_query($conn, "
    SELECT mr.record_id, p.name, mr.diagnosis, mr.record_date
    FROM medical_records mr JOIN patients p ON mr.patient_id = p.patient_id
    ORDER BY mr.record_date DESC
");
$meds = mysqli_query($conn, "SELECT med_id, name FROM medications ORDER BY name");
?>

<div class="page-header">
    <h2>📝 Resep Obat</h2>
</div>

<?= $msg ?>

<div class="card">
    <h3><?= $editData ? '✏️ Edit Resep' : '➕ Tambah Resep' ?></h3>
    <form method="POST" action="index.php?page=prescriptions<?= $editData ? '&edit='.$editData['prescription_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Rekam Medis (Pasien - Diagnosa) *</label>
                <select name="record_id" required>
                    <option value="">-- Pilih Rekam Medis --</option>
                    <?php
                    $rlist = mysqli_query($conn, "
                        SELECT mr.record_id, p.name, mr.diagnosis, mr.record_date
                        FROM medical_records mr JOIN patients p ON mr.patient_id=p.patient_id
                        ORDER BY mr.record_date DESC
                    ");
                    while ($r = mysqli_fetch_assoc($rlist)):
                        $sel = ($editData['record_id'] ?? 0) == $r['record_id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $r['record_id'] ?>" <?= $sel ?>>
                            <?= htmlspecialchars($r['name']) ?> - <?= htmlspecialchars($r['diagnosis']) ?>
                            (<?= date('d M Y', strtotime($r['record_date'])) ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Obat *</label>
                <select name="med_id" required>
                    <option value="">-- Pilih Obat --</option>
                    <?php
                    $mlist = mysqli_query($conn, "SELECT med_id, name FROM medications ORDER BY name");
                    while ($m = mysqli_fetch_assoc($mlist)):
                        $sel = ($editData['med_id'] ?? 0) == $m['med_id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $m['med_id'] ?>" <?= $sel ?>><?= htmlspecialchars($m['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dosis *</label>
                <input type="text" name="dosage" value="<?= htmlspecialchars($editData['dosage'] ?? '') ?>" placeholder="3x1 sehari" required>
            </div>
            <div class="form-group">
                <label>Durasi (hari)</label>
                <input type="number" name="duration_days" min="1" value="<?= $editData['duration_days'] ?? 7 ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_prescription" class="btn btn-warning">💾 Simpan Perubahan</button>
                <a href="index.php?page=prescriptions" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_prescription" class="btn btn-primary">➕ Tambah Resep</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-top">
        <strong>Semua Resep</strong>
        <input type="text" id="searchPres" placeholder="🔍 Cari..." oninput="searchTable('searchPres','presTable')">
    </div>
    <table id="presTable">
        <thead>
            <tr><th>#</th><th>Pasien</th><th>Diagnosa</th><th>Obat</th><th>Dosis</th><th>Durasi</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php $no=1; while ($row = mysqli_fetch_assoc($prescriptions)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                <td><?= htmlspecialchars($row['med_name']) ?></td>
                <td><?= htmlspecialchars($row['dosage']) ?></td>
                <td><?= $row['duration_days'] ?> hari</td>
                <td>
                    <div class="action-btns">
                        <a href="index.php?page=prescriptions&edit=<?= $row['prescription_id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmDelete('index.php?page=prescriptions&delete=<?= $row['prescription_id'] ?>','resep <?= addslashes($row['patient_name']) ?>')">
                            🗑️ Hapus
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
