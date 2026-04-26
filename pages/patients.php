<?php
// ============================================================
// CRUD: PATIENTS
// ============================================================

$msg = '';

// --- CREATE ---
if (isset($_POST['add_patient'])) {
    $name   = mysqli_real_escape_string($conn, trim($_POST['name']));
    $gender = $_POST['gender'];
    $birth  = $_POST['birth_date'];
    $addr   = mysqli_real_escape_string($conn, trim($_POST['address']));
    $phone  = mysqli_real_escape_string($conn, trim($_POST['phone']));

    if ($name && $gender && $birth) {
        mysqli_query($conn, "INSERT INTO patients (name, gender, birth_date, address, phone)
            VALUES ('$name','$gender','$birth','$addr','$phone')");
        $msg = '<div class="alert alert-success">✅ Patient added successfully.</div>';
    } else {
        $msg = '<div class="alert alert-error">❌ Name, gender, and date of birth are required.</div>';
    }
}

// --- UPDATE ---
if (isset($_POST['edit_patient'])) {
    $id     = (int)$_POST['patient_id'];
    $name   = mysqli_real_escape_string($conn, trim($_POST['name']));
    $gender = $_POST['gender'];
    $birth  = $_POST['birth_date'];
    $addr   = mysqli_real_escape_string($conn, trim($_POST['address']));
    $phone  = mysqli_real_escape_string($conn, trim($_POST['phone']));

    mysqli_query($conn, "UPDATE patients SET
        name='$name', gender='$gender', birth_date='$birth',
        address='$addr', phone='$phone'
        WHERE patient_id=$id");
    $msg = '<div class="alert alert-success">✅ Patient data updated successfully.</div>';
}

// --- DELETE ---
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM patients WHERE patient_id=$id");
    $msg = '<div class="alert alert-success">✅ Patient deleted successfully.</div>';
}

// --- GET EDIT DATA ---
$editData = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id=$id");
    $editData = mysqli_fetch_assoc($res);
}

// --- READ ---
$patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY created_at DESC");
?>

<div class="page-header">
    <h2>🧑‍⚕️ Patient Management</h2>
</div>

<?= $msg ?>

<!-- ADD / EDIT FORM -->
<div class="card">
    <h3><?= $editData ? '✏️ Edit Patient' : '➕ Add Patient' ?></h3>
    <form method="POST" action="index.php?page=patients<?= $editData ? '&edit='.$editData['patient_id'] : '' ?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($editData['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Gender *</label>
                <select name="gender" required>
                    <option value="">-- Select --</option>
                    <option value="Male"   <?= ($editData['gender']??'')==='Male'   ? 'selected':'' ?>>Male</option>
                    <option value="Female" <?= ($editData['gender']??'')==='Female' ? 'selected':'' ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth *</label>
                <input type="date" name="birth_date" value="<?= $editData['birth_date'] ?? '' ?>" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($editData['phone'] ?? '') ?>">
            </div>
            <div class="form-group" style="grid-column: 1 / -1">
                <label>Address</label>
                <input type="text" name="address" value="<?= htmlspecialchars($editData['address'] ?? '') ?>">
            </div>
        </div>
        <div class="form-actions" style="margin-top:14px">
            <?php if ($editData): ?>
                <button type="submit" name="edit_patient" class="btn btn-warning">💾 Save Changes</button>
                <a href="index.php?page=patients" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <button type="submit" name="add_patient" class="btn btn-primary">➕ Add Patient</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- DATA TABLE -->
<div class="table-container">
    <div class="table-top">
        <strong>Patient List (<?= mysqli_num_rows($patients) ?>)</strong>
        <input type="text" id="searchInput" placeholder="🔍 Search patients..." oninput="searchTable('searchInput','patientTable')">
    </div>
    <table id="patientTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($patients)):
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= $row['gender'] === 'Male' ? '👨 Male' : '👩 Female' ?></td>
                <td><?= date('d M Y', strtotime($row['birth_date'])) ?></td>
                <td><?= htmlspecialchars($row['phone'] ?: '-') ?></td>
                <td><?= htmlspecialchars($row['address'] ?: '-') ?></td>
                <td>
                    <div class="action-btns">
                        <a href="index.php?page=patients&edit=<?= $row['patient_id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                        <button class="btn btn-danger btn-sm"
                            onclick="confirmDelete('index.php?page=patients&delete=<?= $row['patient_id'] ?>','<?= addslashes($row['name']) ?>')">
                            🗑️ Delete
                        </button>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
