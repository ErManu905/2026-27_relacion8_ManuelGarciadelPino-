<?php
require_once(dirname(__FILE__) . "/../../cabecera.php");

// Colores por defecto
$backgroundColor = "white";
$textColor = "black";

// Recuperar colores de cookies
if (isset($_COOKIE["backgroundColor"]) && in_array($_COOKIE["backgroundColor"], BACKGROUNDCOLOR)) {$backgroundColor = $_COOKIE["backgroundColor"];}
if (isset($_COOKIE["textColor"]) && in_array($_COOKIE["textColor"], TEXTCOLOR)) {$textColor = $_COOKIE["textColor"];}

// Contador de visitas
$views = 1;

if (isset($_COOKIE["views"]) && is_numeric($_COOKIE["views"])) {$views = (int) $_COOKIE["views"] + 1;}

// Guardar contador
setcookie("views", $views, time() + 31536000, "/");

// Mostrar página
inicioCabecera("Personalización");
?>

<style>
    #documento {
        background-color: <?php echo $backgroundColor; ?>;
        color: <?php echo $textColor; ?>;
    }
</style>

<?php

finCabecera();

inicioCuerpo("Personalización", "Personalización");

?>

<h2>Página Personalizable</h2>

<p>
    Número de visitas a esta página: <?php echo $views; ?>
</p>

<?php

finCuerpo();

?>