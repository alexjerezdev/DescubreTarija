<?php
/** @var array $d   destino: id, nombre, ubicacion, descripcion, categoria, foto */
/** @var PDO   $pdo conexión de la página que incluye esta tarjeta */
$foto   = foto_url($d['foto'] ?? null);
$es_fav = in_array((int) $d['id'], favoritos_ids($pdo), true);
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

        <?php if (esta_logueado()): ?>
          <form method="post" action="/favorito.php" class="d-inline">
            <?= csrf_campo() ?>
            <input type="hidden" name="destino_id" value="<?= (int) $d['id'] ?>">
            <input type="hidden" name="accion" value="<?= $es_fav ? 'quitar' : 'agregar' ?>">
            <input type="hidden" name="volver" value="<?= e($_SERVER['REQUEST_URI'] ?? '/destinos.php') ?>">
            <button type="submit" class="btn btn-sm <?= $es_fav ? 'btn-danger' : 'btn-outline-danger' ?>"
                    title="<?= $es_fav ? 'Quitar de favoritos' : 'Guardar en favoritos' ?>"
                    aria-label="<?= $es_fav ? 'Quitar de favoritos' : 'Guardar en favoritos' ?>">
              <i class="bi <?= $es_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            </button>
          </form>
        <?php else: ?>
          <a class="btn btn-outline-danger btn-sm" href="/login.php"
             title="Inicia sesión para guardar favoritos" aria-label="Inicia sesión para guardar favoritos">
            <i class="bi bi-heart"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>