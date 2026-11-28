<?php

class Acceso
{
    // Variables de instancia
    private $validated;
    private $nickname;
    private $userName;
    private $permissions;


    // Constructor
    public function __construct()
    {
        $this->validated = false;
        $this->nickname = "";
        $this->userName = "";
        $this->permissions = array();

        $this->loadFromSession();
    }

    /**
     * Funcion privada para guardar la información en la sesión
     *
     * @return boolean Devuelve true si se ha podido hacer. False en cualquier otro caso
     */
    private function saveToSession(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        if ($this->validated) {
            $_SESSION["acceso"]["validado"] = true;
            $_SESSION["acceso"]["nick"] = $this->nickname;
            $_SESSION["acceso"]["nombre"] = $this->userName;
            $_SESSION["acceso"]["permisos"] = $this->permissions;
        } else {
            $_SESSION["acceso"]["validado"] = false;
            $_SESSION["acceso"]["nick"] = "";
            $_SESSION["acceso"]["nombre"] = "";
            $_SESSION["acceso"]["permisos"] = array();
        }

        return true;
    }

    /**
     * Función privada que recoje la información de la sesión
     *
     * @return boolean Devuelve true si se ha podido recoger
     */
    private function loadFromSession(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE ||
            !isset($_SESSION["acceso"]) ||
            !isset($_SESSION["acceso"]["validado"]) ||
            $_SESSION["acceso"]["validado"] == false) {
            return false;
        }

        $accessData = $_SESSION["acceso"];

        if (!isset($accessData["nick"]) ||
            !isset($accessData["nombre"]) ||
            !isset($accessData["permisos"]) ||
            !is_array($accessData["permisos"])) {
            return false;
        }

        $this->validated = true;
        $this->nickname = $accessData["nick"];
        $this->userName = $accessData["nombre"];
        $this->permissions = $accessData["permisos"];

        return true;
    }

    /**
     * Sirve para registrar un usuario en la aplicación. Almacena
     * los valores en las propiedades apropiadas y en la sesión 
     * para guardar en la sesión la información del usuario validado.
     *
     * @param string $nick nick del usuario a registrar
     * @param string $nombre nombre del usuario a registrar
     * @param array $permisos permisos del usuario a registrar
     * @return boolean Devuelve true si ha podido registrar el usuario
     */
    public function registrarUsuario(string $nick, string $nombre, array $permisos): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE || trim($nick) == "") {
            return false;
        }

        $this->validated = true;
        $this->nickname = $nick;
        $this->userName = $nombre;
        $this->permissions = $permisos;

        if (!$this->saveToSession()) {
            return false;
        }

        return true;
    }


    /**
     * Elimina la información de registro de un usuario
     *
     * @return boolean Devuelve true si ha podido hacerlo
     */
    public function quitarRegistroUsuario(): bool
    {
        $this->validated = false;
        $this->nickname = "";
        $this->userName = "";
        $this->permissions = array();

        if (!$this->saveToSession()) {
            return false;
        }

        return true;
    }


    /**
     * Función que devuelve si hay o no un usuario registrado
     *
     * @return boolean Devuelve true si hay usuario registrado. False en caso contrario
     */
    public function hayUsuario(): bool
    {
        return $this->validated;
    }


    /**
     * Función que devuelve si el usuario registrado tiene o no el permiso indicado
     *
     * @param integer $numero Numero de permiso a comprobar
     * @return bool Devuelve true si hay usuario registrado y tiene el permiso indicado
     */
    public function puedePermiso(int $numero): bool
    {
        if (!$this->validated || $numero < 1 || $numero > 10) {
            return false;
        }

        return isset($this->permissions[$numero]) && $this->permissions[$numero] == true;
    }


    /*
     * Métodos get
     */

    /**
     * Devuelve el nick del usuario indicado o false si no hay usuario
     *
     * @return string|false
     */
    public function getNick(): string|false
    {
        if (!$this->hayUsuario()) {
            return false;
        }

        return $this->nickname;
    }

    /**
     * Devuelve el nombre del usuario registrado o false si no hay usuario
     *
     * @return string|false
     */
    public function getNombre(): string|false
    {
        if (!$this->hayUsuario()) {
            return false;
        }

        return $this->userName;
    }
}

?>
