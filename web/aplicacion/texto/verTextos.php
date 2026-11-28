<?php
require_once(dirname(__FILE__) . "/../../cabecera.php");
require_once(dirname(__FILE__) . "/../clases/RegistroTexto.php");

// Recuperar textos de la sesión
$texts = array();

if (isset($_SESSION["texts"]) && is_array($_SESSION["texts"])) {$texts = $_SESSION["texts"];}

// Registrar texto
if (isset($_POST["register"])) {
    if (isset($_POST["text"]) && trim($_POST["text"]) != "") {
        $text = trim($_POST["text"]);
        $texts[] = new RegistroTexto($text);
        $_SESSION["texts"] = $texts;
    }
    header("Location: verTextos.php");
    exit;
}

// Limpiar textos
if (isset($_POST["clear"])) {
    $texts = array();
    $_SESSION["texts"] = $texts;
    header("Location: verTextos.php");
    exit;
}

// Mostrar página
inicioCabecera("Registro de textos");
finCabecera();
inicioCuerpo("Registro de textos", "Registro de textos");

?>

<h2>Registrar textos</h2>
<form method="post" action="">
    <p>
        <label for="text">Escribe un texto:</label>
        <input type="text" name="text" id="text" required>
    </p>
    <button type="submit" name="register">Registrar</button>
</form>

<h3>Textos registrados</h3>

<textarea rows="12" cols="60" readonly>
<?php
    foreach ($texts as $record) {
        echo htmlspecialchars($record->getText(), ENT_QUOTES, "UTF-8");
        echo " - ";
        echo $record->getDateTime()->format("d/m/Y H:i:s");
        echo "\n";
    }
?>
</textarea>

<form method="post" action="">
    <button type="submit" name="clear">Limpiar</button>
</form>

<?php
finCuerpo();
?>