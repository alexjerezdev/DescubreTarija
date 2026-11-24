</main>

<footer class="footer-tarija mt-5">
  <div class="container py-4">
    <div class="row gy-3">
      <div class="col-md-6">
        <div class="logo mb-1">Descubre<span>Tarija</span></div>
        <p class="small mb-0">Guía turística digital del departamento de Tarija, Bolivia.</p>
      </div>
      <div class="col-md-6 text-md-end small">
        Proyecto académico · Tecnología Web I<br>
        Universidad Privada Domingo Savio (UPDS) · Tarija<br>
        &copy; <?= date('Y') ?> DescubreTarija
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?= $extra_scripts ?? '' ?>
</html>