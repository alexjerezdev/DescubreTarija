<?php /** @var string $admin_activo */ ?>
<ul class="nav nav-pills admin-menu mb-4">
  <li class="nav-item"><a class="nav-link<?= ($admin_activo ?? '') === 'dashboard' ? ' active' : '' ?>" href="/admin/index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
  <li class="nav-item"><a class="nav-link<?= ($admin_activo ?? '') === 'destinos' ? ' active' : '' ?>" href="/admin/destinos.php"><i class="bi bi-geo-alt"></i> Destinos</a></li>
  <li class="nav-item"><a class="nav-link<?= ($admin_activo ?? '') === 'categorias' ? ' active' : '' ?>" href="/admin/categorias.php"><i class="bi bi-tags"></i> Categorías</a></li>
</ul>