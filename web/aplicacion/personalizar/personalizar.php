<?php

require_once(dirname(__FILE__) . "/../../cabecera.php");

// Colores por defecto
$backgroundColor = "white";
$textColor = "black";

// Recuperar colores de cookies
if (isset($_COOKIE["colorFondo"]) && in_array($_COOKIE["colorFondo"], BACKGROUNDCOLOR)) {$backgroundColor = $_COOKIE["colorFondo"];}
if (isset($_COOKIE["colorTexto"]) && in_array($_COOKIE["colorTexto"], TEXTCOLOR)) {$textColor = $_COOKIE["colorTexto"];}

// Guardar colores seleccionados
if (isset($_POST["save"])) {
    if (isset($_POST["backgroundColor"]) && in_array($_POST["backgroundColor"], BACKGROUNDCOLOR)) {
        $backgroundColor = $_POST["backgroundColor"];
        setcookie("colorFondo", $backgroundColor, time() + 31536000, "/");
    }

    if (isset($_POST["textColor"]) && in_array($_POST["textColor"], TEXTCOLOR)) {
        $textColor = $_POST["textColor"];
        setcookie("colorTexto", $textColor, time() + 31536000, "/");
    }
}

// Mostrar la página
inicioCabecera("Personalizar");
?>

<style>
    #documento {
        background-color: <?php echo $backgroundColor; ?>;
        color: <?php echo $textColor; ?>;
    }
</style>

<?php

finCabecera();

inicioCuerpo("Personalizar", "Personalización");

?>

<h2>Personalizar página</h2>
<p>Selecciona los colores de la página</p>

<form method="post" action="">

    <p>
        <label for="backgroundColor">Color de fondo:</label>
        <select name="backgroundColor" id="backgroundColor">
            <?php foreach (BACKGROUNDCOLOR as $name => $value) { ?>
                <option value="<?php echo $value; ?>"
                    <?php if ($backgroundColor == $value) echo "selected"; ?>>
                    <?php echo $name; ?>
                </option>
            <?php } ?>
        </select>
    </p>
    <p>
        <label for="textColor">Color del texto:</label>
        <select name="textColor" id="textColor">
            <?php foreach (TEXTCOLOR as $name => $value) { ?>
                <option value="<?php echo $value; ?>"
                    <?php if ($textColor == $value) echo "selected"; ?>>
                    <?php echo $name; ?>
                </option>
            <?php } ?>
        </select>
    </p>

    <button type="submit" name="save" value="1">
        Guardar
    </button>
</form>

<?php

finCuerpo();
?>