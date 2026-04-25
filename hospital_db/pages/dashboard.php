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

// --- CHART DATA PREPARATION ---
// 1. Patient Demographics (Gender)
$gender_res = mysqli_query($conn, "SELECT gender, COUNT(*) as count FROM patients GROUP BY gender");
$gender_labels = [];
$gender_data = [];
while ($row = mysqli_fetch_assoc($gender_res)) {
    $gender_labels[] = $row['gender'];
    $gender_data[] = $row['count'];
}

// 2. Doctors by Specialization
$spec_res = mysqli_query($conn, "SELECT specialization, COUNT(*) as count FROM doctors GROUP BY specialization");
$spec_labels = [];
$spec_data = [];
while ($row = mysqli_fetch_assoc($spec_res)) {
    $spec_labels[] = $row['specialization'];
    $spec_data[] = $row['count'];
}
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

<!-- CHARTS SECTION -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; margin-bottom: 24px;">
    <!-- Chart 1 -->
    <div class="card" style="margin-bottom: 0;">
        <h3>Patient Demographics</h3>
        <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
            <canvas id="patientChart"></canvas>
        </div>
    </div>
    <!-- Chart 2 -->
    <div class="card" style="margin-bottom: 0;">
        <h3>Doctors by Specialization</h3>
        <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
            <canvas id="doctorChart"></canvas>
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

<!-- INIT CHARTS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared chart options for dark mode support
    const isDark = document.body.classList.contains('dark-mode');
    const textColor = isDark ? '#e2e8f0' : '#1e293b';

    // 1. Patient Chart (Pie)
    const ctxPatient = document.getElementById('patientChart').getContext('2d');
    new Chart(ctxPatient, {
        type: 'pie',
        data: {
            labels: <?= json_encode($gender_labels) ?>,
            datasets: [{
                data: <?= json_encode($gender_data) ?>,
                backgroundColor: ['#0d9488', '#f59e0b', '#6366f1'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { family: 'Inter' } } }
            }
        }
    });

    // 2. Doctor Chart (Doughnut)
    const ctxDoctor = document.getElementById('doctorChart').getContext('2d');
    new Chart(ctxDoctor, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($spec_labels) ?>,
            datasets: [{
                data: <?= json_encode($spec_data) ?>,
                backgroundColor: ['#0891b2', '#8b5cf6', '#ec4899', '#f97316', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { color: textColor, font: { family: 'Inter' } } }
            }
        }
    });
});
</script>
