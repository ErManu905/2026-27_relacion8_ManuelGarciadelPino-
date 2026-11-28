<?php
define("RUTABASE", dirname(__FILE__));
define("MODO_TRABAJO", "desarrollo");

// Configuración del manejo de errores
if (MODO_TRABAJO == "produccion") {error_reporting(0);} 
else {error_reporting(E_ALL);}

// Configuración de los colores del texto
define("TEXTCOLOR", array("Negro"=>"black", "Azul"=>"blue", "Blanco"=>"white", "Rojo"=>"red"));

// Configuración de los colores de fondo
define("BACKGROUNDCOLOR", array("Blanco"=>"white", "Rojo"=>"red", "Verde"=>"green", "Azul"=>"blue", "Amarillo"=>"yellow"));

// Inicio de sesión
if (session_status() == PHP_SESSION_NONE) {session_start();}

// Cargar las funciones de la plantilla
require_once(RUTABASE . "/aplicacion/plantilla/plantilla.php");
?>