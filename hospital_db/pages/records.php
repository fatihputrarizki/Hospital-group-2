<?php
// ============================================================
// CRUD: MEDICAL RECORDS
// ============================================================

$msg = '';

if (isset($_POST['add_record'])) {
    $patient_id = (int)$_POST['patient_id'];
    $doctor_id  = (int)$_POST['doctor_id'];
    $diagnosis  = mysqli_real_escape_string($conn, trim($_POST['diagnosis']));
    $treatment  = mysqli_real_escape_string($conn, trim($_POST['treatment']));
    $date       = $_POST['record_date'];

    if ($patient_id && $doctor_id && $diagnosis && $date) {
        mysqli_query($conn, "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment, record_date)
            VALUES ($patient_id, $doctor_id, '$diagnosis', '$treatment', '$date')");
        $msg = '<div class="alert alert-success">✅ Medical record added successfully.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ All required fields must be filled in.</div>';
    }
}

if (isset($_POST['edit_record'])) {
    $id         = (int)$_POST['record_id'];
    $patient_id = (int)$_POST['patient_id'];
    $doctor_id  = (int)$_POST['doctor_id'];
    $diagnosis  = mysqli_real_escape_string($conn, trim($_POST['diagnosis']));
    $treatment  = mysqli_real_escape_string($conn, trim($_POST['treatment']));
    $date       = $_POST['record_date'];

    mysqli_query($conn, "UPDATE medical_records SET
        patient_id=$patient_id, doctor_id=$doctor_id,
        diagnosis='$diagnosis', treatment='$treatment', record_date='$date'
        WHERE record_id=$id");
    $msg = '<div class="alert alert-success">✅ Medical record updated successfully.</div>';
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM medical_records WHERE record_id=$id");
    $msg = '<div class="alert alert-success">✅ Medical record deleted successfully.</div>';
}

$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM medical_records WHERE record_id=$id");
    $editData = mysqli_fetch_assoc($res);
}

$records = mysqli_query($conn, "
    SELECT mr.*, p.name AS patient_name, d.name AS doctor_name
    FROM medical_records mr
    JOIN patients p ON mr.patient_id = p.patient_id
    JOIN doctors  d ON mr.doctor_id  = d.doctor_id
    ORDER BY mr.record_date DESC
");
?>

<div class="page-header">
    <h2>📋 Medical Records</h2>
</div>

<?= $msg ?>

<div class="card">
    <h3><?= $editData ? '✏️ Edit Medical Record' : '➕ Add Medical Record' ?></h3>
    <form method="POST" action="index.php?page=records<?= $editData ? '&edit='.$editData['record_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" required>
                    <option value="">-- Select Patient --</option>
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
                <label>Doctor *</label>
                <select name="doctor_id" required>
                    <option value="">-- Select Doctor --</option>
                    <?php
                    $dlist = mysqli_query($conn, "SELECT doctor_id, name FROM doctors ORDER BY name");
                    while ($d = mysqli_fetch_assoc($dlist)):
                        $sel = ($editData['doctor_id'] ?? 0) == $d['doctor_id'] ? 'selected' : '';
                    ?>
                        <option value="<?= $d['doctor_id'] ?>" <?= $sel ?>><?= htmlspecialchars($d['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Diagnosis *</label>
                <input type="text" name="diagnosis" value="<?= htmlspecialchars($editData['diagnosis'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="record_date" value="<?= $editData['record_date'] ?? date('Y-m-d') ?>" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1">
                <label>Treatment / Procedure</label>
                <input type="text" name="treatment" value="<?= htmlspecialchars($editData['treatment'] ?? '') ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_record" class="btn btn-warning">💾 Save Changes</button>
                <a href="index.php?page=records" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_record" class="btn btn-primary">➕ Add Medical Record</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <div class="table-top">
        <strong>All Medical Records</strong>
        <input type="text" id="searchRec" placeholder="🔍 Search..." oninput="searchTable('searchRec','recTable')">
    </div>
    <table id="recTable">
        <thead>
            <tr><th>#</th><th>Patient</th><th>Doctor</th><th>Diagnosis</th><th>Treatment</th><th>Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php $no=1; while ($row = mysqli_fetch_assoc($records)): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                <td><?= htmlspecialchars($row['treatment'] ?: '-') ?></td>
                <td><?= date('d M Y', strtotime($row['record_date'])) ?></td>
                <td>
                    <div class="action-btns">
                        <a href="index.php?page=records&edit=<?= $row['record_id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmDelete('index.php?page=records&delete=<?= $row['record_id'] ?>','medical record of <?= addslashes($row['patient_name']) ?>')">
                            🗑️ Delete
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
