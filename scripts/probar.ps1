# Prueba rapida: pide cada ruta SIN sesion y compara el codigo HTTP esperado.
# 200 = pagina publica, 302 = redirige a login (protegida), 404 = no existe.
$base = 'http://localhost:8090'

function Estado($ruta) {
    $req = [System.Net.HttpWebRequest]::Create("$base$ruta")
    $req.AllowAutoRedirect = $false      # no seguir la redireccion: queremos ver el 302
    $req.Timeout = 10000
    try {
        $resp = $req.GetResponse()
        $codigo = [int]$resp.StatusCode
        $resp.Close()
        return $codigo
    } catch [System.Net.WebException] {
        if ($_.Exception.Response) { return [int]$_.Exception.Response.StatusCode }
        return 0
    }
}

$esperado = [ordered]@{
    '/index.php'                  = 200
    '/destinos.php'               = 200
    '/destinos.php?q=casa'        = 200
    '/destinos.php?cat=1'         = 200
    '/detalle.php?id=1'           = 200
    '/detalle.php?id=9999'        = 404
    '/mapa.php'                   = 200
    '/login.php'                  = 200
    '/registro.php'               = 200
    '/favoritos.php'              = 302
    '/recorridos.php'             = 302
    '/recorrido.php?id=1'         = 302
    '/admin/index.php'            = 302
    '/admin/destinos.php'         = 302
    '/admin/categorias.php'       = 302
    '/favorito.php'               = 302
    '/recorrido_accion.php'       = 302
    '/logout.php'                 = 302
    '/no-existe.php'              = 404
}

$fallos = 0
foreach ($ruta in $esperado.Keys) {
    $real = Estado $ruta
    if ($real -eq $esperado[$ruta]) {
        Write-Host ("OK     {0,-28} {1}" -f $ruta, $real) -ForegroundColor Green
    } else {
        Write-Host ("FALLA  {0,-28} esperado {1}, obtuvo {2}" -f $ruta, $esperado[$ruta], $real) -ForegroundColor Red
        $fallos++
    }
}
Write-Host ""
if ($fallos -eq 0) { Write-Host "Todas las rutas responden como se espera." -ForegroundColor Green }
else { Write-Host "$fallos ruta(s) con problemas." -ForegroundColor Red }