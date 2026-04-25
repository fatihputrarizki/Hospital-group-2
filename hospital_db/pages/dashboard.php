<?php
// Count statistics
$total_patients     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM patients"))[0];
$total_doctors      = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM doctors"))[0];
$total_appointments = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM appointments"))[0];
$total_medications  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM medications"))[0];

// Today's appointments
$today_appts = mysqli_query($conn, "
    SELECT a.*, p.name AS patient_name, d.name AS doctor_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    JOIN doctors  d ON a.doctor_id  = d.doctor_id
    WHERE DATE(a.appointment_date) = CURDATE()
    ORDER BY a.appointment_date
");
?>

<div class="page-header">
    <h2>📊 Dashboard</h2>
    <span style="font-size:13px;color:#6b7280"><?= date('l, d F Y') ?></span>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🧑‍⚕️</div>
        <div class="stat-info">
            <h3><?= $total_patients ?></h3>
            <p>Total Patients</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">👨‍⚕️</div>
        <div class="stat-info">
            <h3><?= $total_doctors ?></h3>
            <p>Total Doctors</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-info">
            <h3><?= $total_appointments ?></h3>
            <p>Total Appointments</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💊</div>
        <div class="stat-info">
            <h3><?= $total_medications ?></h3>
            <p>Medication Types</p>
        </div>
    </div>
</div>

<!-- TODAY'S APPOINTMENTS -->
<div class="table-container">
    <div class="table-top">
        <strong>📅 Today's Appointments</strong>
        <a href="index.php?page=appointments" class="btn btn-primary btn-sm">View All</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($today_appts) == 0): ?>
            <tr><td colspan="4" style="text-align:center;color:#6b7280;padding:20px">No appointments today</td></tr>
        <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($today_appts)): ?>
            <tr>
                <td><?= htmlspecialchars($row['patient_name']) ?></td>
                <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                <td><?= date('H:i', strtotime($row['appointment_date'])) ?></td>
                <td>
                    <?php
                    $badges = ['Scheduled'=>'badge-info','Completed'=>'badge-success','Cancelled'=>'badge-danger'];
                    $cls = $badges[$row['status']] ?? 'badge-info';
                    ?>
                    <span class="badge <?= $cls ?>"><?= $row['status'] ?></span>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
