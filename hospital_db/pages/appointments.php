<?php
// ============================================================
// CRUD: APPOINTMENTS
// ============================================================

$msg = '';

if (isset($_POST['add_appt'])) {
    $patient_id = (int)$_POST['patient_id'];
    $doctor_id  = (int)$_POST['doctor_id'];
    $date       = $_POST['appointment_date'];
    $status     = $_POST['status'];
    $notes      = mysqli_real_escape_string($conn, trim($_POST['notes']));

    if ($patient_id && $doctor_id && $date) {
        mysqli_query($conn, "INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes)
            VALUES ($patient_id, $doctor_id, '$date', '$status', '$notes')");
        $msg = '<div class="alert alert-success">✅ Jadwal berhasil ditambahkan.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ Pasien, dokter, dan tanggal wajib diisi.</div>';
    }
}

if (isset($_POST['edit_appt'])) {
    $id         = (int)$_POST['appointment_id'];
    $patient_id = (int)$_POST['patient_id'];
    $doctor_id  = (int)$_POST['doctor_id'];
    $date       = $_POST['appointment_date'];
    $status     = $_POST['status'];
    $notes      = mysqli_real_escape_string($conn, trim($_POST['notes']));

    mysqli_query($conn, "UPDATE appointments SET
        patient_id=$patient_id, doctor_id=$doctor_id,
        appointment_date='$date', status='$status', notes='$notes'
        WHERE appointment_id=$id");
    $msg = '<div class="alert alert-success">✅ Jadwal berhasil diperbarui.</div>';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM appointments WHERE appointment_id=$id");
    $msg = '<div class="alert alert-success">✅ Jadwal berhasil dihapus.</div>';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM appointments WHERE appointment_id=$id");
    $editData = mysqli_fetch_assoc($res);
}

$patients = mysqli_query($conn, "SELECT patient_id, name FROM patients ORDER BY name");
$doctors  = mysqli_query($conn, "SELECT doctor_id, name, specialization FROM doctors ORDER BY name");
$appts    = mysqli_query($conn, "
    SELECT a.*, p.name AS patient_name, d.name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    JOIN doctors  d ON a.doctor_id  = d.doctor_id
    ORDER BY a.appointment_date DESC
");
?>

<div class="page-header">
    <h2>📅 Jadwal Perjanjian</h2>
</div>

<?= $msg ?>

<div class="card">
    <h3><?= $editData ? '✏️ Edit Jadwal' : '➕ Tambah Jadwal' ?></h3>
    <form method="POST" action="index.php?page=appointments<?= $editData ? '&edit='.$editData['appointment_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Pasien *</label>
                <select name="patient_id" required>
                    <option value="">-- Pilih Pasien --</option>
                    <?php
                    $plist = mysqli_query($conn, "SELECT patient_id, name FROM patients ORDER BY name");
                    while ($p = mysqli_fetch_assoc($plist)):
                        $sel = ($editData['patient_id'] ?? 0) == $p['patient_id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $p['patient_id'] ?>" <?= $sel ?>><?= htmlspecialchars($p['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Dokter *</label>
                <select name="doctor_id" required>
                    <option value="">-- Pilih Dokter --</option>
                    <?php
                    $dlist = mysqli_query($conn, "SELECT doctor_id, name, specialization FROM doctors ORDER BY name");
                    while ($d = mysqli_fetch_assoc($dlist)):
                        $sel = ($editData['doctor_id'] ?? 0) == $d['doctor_id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $d['doctor_id'] ?>" <?= $sel ?>><?= htmlspecialchars($d['name']) ?> (<?= $d['specialization'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal & Jam *</label>
                <input type="datetime-local" name="appointment_date"
                    value="<?= $editData ? date('Y-m-d\TH:i', strtotime($editData['appointment_date'])) : '' ?>" required>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <?php foreach (['Scheduled','Completed','Cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= ($editData['status'] ?? 'Scheduled') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="grid-column: 1 / -1">
                <label>Catatan</label>
                <input type="text" name="notes" value="<?= htmlspecialchars($editData['notes'] ?? '') ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_appt" class="btn btn-warning">💾 Simpan Perubahan</button>
                <a href="index.php?page=appointments" class="btn btn-secondary">Batal</a>
            <?php else: ?>
                <button type="submit" name="add_appt" class="btn btn-primary">➕ Tambah Jadwal</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-top">
        <strong>Semua Jadwal</strong>
        <input type="text" id="searchAppt" placeholder="🔍 Cari..." oninput="searchTable('searchAppt','apptTable')">
    </div>
    <table id="apptTable">
        <thead>
            <tr><th>#</th><th>Pasien</th><th>Dokter</th><th>Tanggal & Jam</th><th>Status</th><th>Catatan</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $badges = ['Scheduled'=>'badge-info','Completed'=>'badge-success','Cancelled'=>'badge-danger'];
        while ($row = mysqli_fetch_assoc($appts)):
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['appointment_date'])) ?></td>
                <td><span class="badge <?= $badges[$row['status']] ?? 'badge-info' ?>"><?= $row['status'] ?></span></td>
                <td><?= htmlspecialchars($row['notes'] ?: '-') ?></td>
                <td>
                    <div class="action-btns">
                        <a href="index.php?page=appointments&edit=<?= $row['appointment_id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmDelete('index.php?page=appointments&delete=<?= $row['appointment_id'] ?>','jadwal <?= addslashes($row['patient_name']) ?>')">
                            🗑️ Hapus
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
