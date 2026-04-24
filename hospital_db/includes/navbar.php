<?php $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; ?>

<nav>
    <div class="logo">🏥 RS Medika Nusantara</div>
    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    <ul id="navMenu">
        <li><a class="<?= $page=='dashboard'     ?'active':'' ?>" href="index.php">Dashboard</a></li>
        <li><a class="<?= $page=='patients'      ?'active':'' ?>" href="index.php?page=patients">Pasien</a></li>
        <li><a class="<?= $page=='doctors'       ?'active':'' ?>" href="index.php?page=doctors">Dokter</a></li>
        <li><a class="<?= $page=='appointments'  ?'active':'' ?>" href="index.php?page=appointments">Jadwal</a></li>
        <li><a class="<?= $page=='records'       ?'active':'' ?>" href="index.php?page=records">Rekam Medis</a></li>
        <li><a class="<?= $page=='medications'   ?'active':'' ?>" href="index.php?page=medications">Obat</a></li>
        <li><a class="<?= $page=='prescriptions' ?'active':'' ?>" href="index.php?page=prescriptions">Resep</a></li>
    </ul>
</nav>
