<?php
// includes/sidebar.php — Sidebar comune a tutte le pagine polizia
$current = basename($_SERVER['PHP_SELF']);
$pdo_s   = get_db();
$n_msg   = $pdo_s->query("SELECT COUNT(*) FROM messaggi_interni WHERE letto=0")->fetchColumn();
?>
<nav class="sidebar">
    <div class="sidebar-logo">
        <svg viewBox="0 0 50 50" width="40">
            <polygon points="25,3 45,15 45,35 25,47 5,35 5,15" fill="none" stroke="#c8a84b" stroke-width="2"/>
            <text x="25" y="30" text-anchor="middle" fill="#c8a84b" font-size="9" font-weight="bold">PS</text>
        </svg>
        <span>P.d.S.</span>
    </div>
    <ul class="nav-links">
        <li><a href="dashboard.php"  class="<?= $current==='dashboard.php'  ?'active':'' ?>"><span class="nav-icon">⌂</span>Dashboard</a></li>
        <li><a href="anagrafica.php" class="<?= $current==='anagrafica.php' ?'active':'' ?>"><span class="nav-icon">◉</span>Anagrafica Agenti</a></li>
        <li><a href="mandati.php"    class="<?= $current==='mandati.php'    ?'active':'' ?>"><span class="nav-icon">⊞</span>Mandati</a></li>
        <li><a href="report.php"     class="<?= $current==='report.php'     ?'active':'' ?>"><span class="nav-icon">⚠</span>Report Incidenti</a></li>
        <li><a href="messaggi.php"   class="<?= $current==='messaggi.php'   ?'active':'' ?>">
            <span class="nav-icon">✉</span>Messaggi
            <?php if ($n_msg > 0): ?><span class="badge"><?= $n_msg ?></span><?php endif; ?>
        </a></li>
        <li><a href="terminale.php"  class="<?= $current==='terminale.php'  ?'active':'' ?>"><span class="nav-icon">█</span>Terminale DB</a></li>
    </ul>
    <div class="sidebar-footer">
        <span class="user-info">◈ <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="logout.php" class="logout-btn">Disconnetti</a>
    </div>
</nav>
