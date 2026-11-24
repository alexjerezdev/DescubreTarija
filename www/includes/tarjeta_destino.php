<?php
// Tarjeta de destino reutilizable. Se incluye dentro de un foreach con la variable $d.
$foto = foto_url($d['foto'] ?? null);
?>
<div class="col-12 col-md-6 col-lg-4">
  <div class="card tarjeta-destino">
    <?php if ($foto): ?>
      <div class="foto" style="background-image:url('<?= e($foto) ?>')" role="img" aria-label="<?= e($d['nombre']) ?>"></div>
    <?php else: ?>
      <div class="foto"><i class="bi bi-image fs-1"></i></div>
    <?php endif; ?>

    <div class="card-body d-flex flex-column">
      <div><span class="chip-categoria"><?= e($d['categoria'] ?? 'Sin categoría') ?></span></div>
      <h3 class="card-title mt-2"><?= e($d['nombre']) ?></h3>
      <p class="small text-muted mb-1">
        <i class="bi bi-geo-alt"></i> <?= e(dato($d['ubicacion'], 'Ubicación por verificar')) ?>
      </p>
      <p class="small"><?= e(mb_strimwidth(dato($d['descripcion'], 'Descripción por verificar'), 0, 110, '…')) ?></p>
      <div class="mt-auto d-flex gap-2">
        <a href="/detalle.php?id=<?= (int) $d['id'] ?>" class="btn btn-verde btn-sm">Ver detalles</a>
        <button class="btn btn-outline-danger btn-sm" type="button" disabled
                title="Se activa en la Fase 5"><i class="bi bi-heart"></i></button>
      </div>
    </div>
  </div>
</div>