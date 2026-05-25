<?php
// includes/h-sidebar.php — Sidebar comune sito hacker
$current = basename($_SERVER['PHP_SELF']);
$livello = $_SESSION['hacker_level'] ?? 1;
$alias   = $_SESSION['hacker_user'] ?? '???';
?>
<nav class="h-sidebar">
    <div class="h-logo">
        <span class="logo-bracket">[</span>NEXUS<span class="logo-bracket">]</span>
    </div>
    <div class="h-user">
        <span class="h-alias">&gt; <?= htmlspecialchars($alias) ?></span>
        <span class="h-level">LVL <?= $livello === 2 ? 'ADMIN' : 'MEMBER' ?></span>
    </div>
    <ul class="h-nav">
        <li><a href="dashboard.php"  class="<?= $current==='dashboard.php'  ?'active':'' ?>"><span>//</span> Dashboard</a></li>
        <li><a href="identita.php"   class="<?= $current==='identita.php'   ?'active':'' ?>"><span>//</span> Identità Reali</a></li>
        <li><a href="operazioni.php" class="<?= $current==='operazioni.php' ?'active':'' ?>"><span>//</span> Operazioni</a></li>
        <li><a href="drops.php"      class="<?= $current==='drops.php'      ?'active':'' ?>"><span>//</span> File Rubati</a></li>
        <?php if ($livello == 2): ?>
        <li class="admin-only"><a href="admin.php" class="<?= $current==='admin.php' ?'active':'' ?>"><span>//</span> ⚠ Admin Console</a></li>
        <?php endif; ?>
    </ul>
    <a href="logout.php" class="h-logout">[ DISCONNETTI ]</a>
</nav>
