<?php

require_once(dirname(__FILE__) . "/ACLBase.php");

class ACLArray extends ACLBase
{
    /**
     * $_roles contiene los roles definidos en la ACL
     * Cada role sera un array asociativo de 
     * cod => [ "cod"=> ,
     *            "nombre" =>  ,
     *            "permisos" => [1=>false, 2=> XX, .........10=> XXX]
     *          ]
     * 
     */
    private $_roles = array();

    /**
     * $_usuarios contiene los usuarios definidos en la ACL
     * Cada usuario sera un array asociativo de
     * 
     * cod => [
     *          "cod" => ,
     *          "nick" =>  ,
     *          "nombre" => ,
     *          "contrasenia" =>  , usamos una función de cifrado.
     *          "cod_role" => ,
     *          "borrado" =>        
     * ]
     */
    private $_usuarios = array();


    public function __construct()
    {
        //añade los roles
        $this->anadirRole("normales", [1 => true]);
        $this->anadirRole("administradores", [1 => true, 2 => true]);

        //añade los usuario
        $this->anadirUsuario(
            "Es usuario alumno",
            "alumno",
            "alum",
            $this->getCodRole("normales")
        );
        $this->anadirUsuario(
            "Es usuario profesor",
            "profesor",
            "profe",
            $this->getCodRole("administradores")
        );
        $this->anadirUsuario(
            "Vicente Tejero",
            "vicente",
            "profesor",
            $this->getCodRole("administradores")
        );
    }

    /**
     * Añade un role a nuesta ACL
     * 
     * @param string $nombre Nombre del role a añadir 
     * @param array $permisos Permisos que tendrá el role. Array con hasta 10 permisos
     * @return bool True si se ha podido crear, false en caso contrario
     */
    public function anadirRole(string $nombre, array $permisos = array()): bool
    {
        //pongo el nombre en minuscula
        $roleName = strtolower(trim($nombre));

        //inicializo un arracon todos los permisos a false
        $rolePermissions = [
            1 => false,
            2 => false,
            3 => false,
            4 => false,
            5 => false,
            6 => false,
            7 => false,
            8 => false,
            9 => false,
            10 => false
        ];

        //compruebo si ya existe el role
        if ($roleName == "" || $this->getCodRole($roleName) !== false) {
            return false;
        }

        //reviso los permisos
        foreach ($rolePermissions as $key => $value) {
            if (isset($permisos[$key])) {
                $rolePermissions[$key] = boolval($permisos[$key]);
            }
        }

        //busco cual es la ultima clave
        $lastId = 0;
        foreach ($this->_roles as $role) {
            if ($role["cod"] > $lastId) {
                $lastId = $role["cod"];
            }
        }

        $lastId++;

        //creo el role
        $this->_roles[$lastId] = [
            "cod" => $lastId,
            "nombre" => $roleName,
            "permisos" => $rolePermissions
        ];

        return true;
    }

    /**
     * Función que localiza un role y devuelve el código de ese role o false si no
     * lo encuentra
     *
     * @param string $nombre nombre del role
     * @return integer|false Devuelve el código de role para el nombre indicado o false si no encuentra el role
     */
    public function getCodRole(string $nombre): int|false
    {
        //pongo el nombre en minuscula
        $roleName = strtolower(trim($nombre));

        foreach ($this->_roles as $role) {
            if ($role["nombre"] == $roleName) {
                return $role["cod"];
            }
        }

        return false;
    }

    /**
     * Función que comprueba la existencia de un role
     *
     * @param integer $codRole role a buscar
     * @return boolean Devuelve true si lo encuentra o false en caso contrario 
     */
    public function existeRole(int $codRole): bool
    {
        return isset($this->_roles[$codRole]);
    }

    /**
     * Función que devuelve los permisos de un role dado
     *
     * @param integer $codRole Role a buscar
     * @return array|false Devuelve los permisos o false si no encuentra el role
     */
    public function getPermisosRole(int $codRole): array|false
    {
        //compruebo que existe el codigo de role
        if (!$this->existeRole($codRole)) {
            return false;
        }

        return $this->_roles[$codRole]["permisos"];
    }

    /**
     * Función que devuelve si un role tiene o no un permiso concreto
     *
     * @param integer $codRole Role a buscar
     * @param integer $numero Número de permiso
     * @return boolean True si encuentra el role y lo tiene. False en cualquier otro caso
     */
    public function getPermisoRole(int $codRole, int $numero): bool
    {
        if (!$this->existeRole($codRole) || $numero < 1 || $numero > 10) {
            return false;
        }

        return $this->_roles[$codRole]["permisos"][$numero];
    }

    /**
     * Función que añade un nuevo usuario a nuestra ACL
     *
     * @param string $nombre Nombre del usuario
     * @param string $nick Nick unico para el usuario
     * @param string $contrasena contraseña del usuario
     * @param integer $codRole Role a asignarle
     * @return boolean Devuelve true si puede crearlo. False en caso contrario
     */
    public function anadirUsuario(string $nombre, string $nick, string $contrasena, int $codRole): bool
    {
        //pongo el nick as minuscula
        $nickname = strtolower(trim($nick));

        //el nick debe ser único compruebo que no 
        //exista ya
        if ($nickname == "" || $this->existeUsuario($nickname)) {
            return false;
        }

        //compruebo que exista el role
        if (!$this->existeRole($codRole) || $contrasena == "") {
            return false;
        }

        //la contraseña la guardo encriptada
        //establecer el método
        $passwordHash = password_hash($contrasena, PASSWORD_BCRYPT);

        if ($passwordHash === false) {
            return false;
        }

        //busco cual es el ultimo codigo
        $lastId = 0;
        foreach ($this->_usuarios as $user) {
            if ($user["cod"] > $lastId) {
                $lastId = $user["cod"];
            }
        }

        $lastId++;

        //añado el usuario
        $this->_usuarios[$lastId] = [
            "cod" => $lastId,
            "nick" => $nickname,
            "nombre" => $nombre,
            "contrasenia" => $passwordHash,
            "cod_role" => $codRole,
            "borrado" => false
        ];

        return true;
    }

    /**
     * Obtiene el código de usuario para un nick dado
     *
     * @param string $nick nick del usuario a buscar
     * @return integer|false Devuelve el codigo del usuario o false si no lo encuentra
     */
    public function getCodUsuario(string $nick): int|false
    {
        //pongo el nick n minuscula
        $nickname = strtolower(trim($nick));

        foreach ($this->_usuarios as $key => $user) {
            if ($user["nick"] == $nickname) {
                //se encuentra el usuario
                return $key;
            }
        }

        //no se encuentra
        return false;
    }

    /**
     * Verifica si existe un usuario dado un código
     *
     * @param integer $codUsuario Código del usuario a verificar
     * @return boolean Devuelve si existe o no el usuario
     */
    public function existeCodUsuario(int $codUsuario): bool
    {
        return isset($this->_usuarios[$codUsuario]);
    }

    /**
     * Verifica si existe o no un usuario con el nick dado
     *
     * @param string $nick Nick del usuario a comprobar
     * @return boolean Devuelve true si encuentra el usuario y 
     * false en caso contrario
     */
    public function existeUsuario(string $nick): bool
    {
        return $this->getCodUsuario($nick) !== false;
    }

    /**
     * Función que comprueba que existe un usuario y la contraseña indicada es la correcta
     *
     * @param string $nick Nick del usuario a comprobar
     * @param string $contrasena Contraseña del usuario a comprobar
     * @return boolean Devuelve true si existe el usuario y tiene la contraseña indicada. 
     * False en otro caso
     */
    public function esValido(string $nick, string $contrasena): bool
    {
        //pongo el nick n minuscula
        $nickname = strtolower(trim($nick));

        //compruebo si existe el nick
        if (!$this->existeUsuario($nickname)) {
            return false;
        }

        //recojo cual es el codigo
        $userId = $this->getCodUsuario($nickname);

        if ($userId === false || $this->getBorrado($userId)) {
            return false;
        }

        //establecer el metodo de encriptado
        if (!password_verify($contrasena, $this->_usuarios[$userId]["contrasenia"])) {
            return false;
        }

        // es valido
        return true;
    }

    /**
     * Función que comprueba si un usuario tiene un permiso concreto
     *
     * @param integer $codUsuario Usuario a buscar
     * @param integer $numero Permiso a buscar
     * @return boolean Devuelve true si existe el usuario y tiene el permiso. 
     * False en otro caso
     */
    public function getPermiso(int $codUsuario, int $numero): bool
    {
        if (!$this->existeCodUsuario($codUsuario) || $this->getBorrado($codUsuario)) {
            return false;
        }

        $roleId = $this->getUsuarioRole($codUsuario);

        if ($roleId === false) {
            return false;
        }

        return $this->getPermisoRole($roleId, $numero);
    }

    /**
     * Función que devuelve los permisos de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @return array|false Devuelve los permisos del usuario o false si 
     * no existe el usuario
     */
    public function getPermisos(int $codUsuario): array|false
    {
        if (!$this->existeCodUsuario($codUsuario) || $this->getBorrado($codUsuario)) {
            return false;
        }

        $roleId = $this->getUsuarioRole($codUsuario);

        if ($roleId === false) {
            return false;
        }

        return $this->getPermisosRole($roleId);
    }

    /**
     * Función que devuelve el nombre de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @return string|false Devuelve el nombre del usuario o false si no existe
     */
    public function getNombre(int $codUsuario): string|false
    {
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        return $this->_usuarios[$codUsuario]["nombre"];
    }

    /**
     * Devuelve si un usuario está borrado
     *
     * @param integer $codUsuario Usuario a buscar.
     * @return boolean true si el usuario existe y no está borrado.
     * False en otro caso
     */
    public function getBorrado(int $codUsuario): bool
    {
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        return $this->_usuarios[$codUsuario]["borrado"];
    }

    /**
     * Devuelve el role que tiene un usuario concreto
     *
     * @param integer $codUsuario Usuario a buscar
     * @return integer|false Devuelve el role del usuario o false si no existe.
     */
    public function getUsuarioRole(int $codUsuario): int|false
    {
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        return $this->_usuarios[$codUsuario]["cod_role"];
    }

    /**
     * Función que asigna un nombre a un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param string $nombre Nombre a asignar
     * @return boolean Devuelve true si ha podido asignar el nombre, false en otro caso
     */
    public function setNombre(int $codUsuario, string $nombre): bool
    {
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        $this->_usuarios[$codUsuario]["nombre"] = $nombre;

        return true;
    }

    /**
     * Función que asigna una contraseña a un usuario
     *
     * @param integer $codUsuario usuario a buscar
     * @param string $contrasenia contraseña a asignar
     * @return boolean Devuelve true si ha podido asignar la contraseña
     * False en otro caso
     */
    public function setContrasenia(int $codUsuario, string $contrasenia): bool
    {
        if (!$this->existeCodUsuario($codUsuario) || $contrasenia == "") {
            return false;
        }

        //la contraseña la guardo encriptada
        $passwordHash = password_hash($contrasenia, PASSWORD_BCRYPT);

        if ($passwordHash === false) {
            return false;
        }

        $this->_usuarios[$codUsuario]["contrasenia"] = $passwordHash;

        return true;
    }

    /**
     * Función que borra/desborra lógicamente un usuario 
     *
     * @param integer $codUsuario Usuario a buscar
     * @param boolean $borrado Estado a asignar
     * @return boolean Devuelve true si ha podido asignar el estado. 
     * False en otro caso
     */
    public function setBorrado(int $codUsuario, bool $borrado): bool
    {
        if (!$this->existeCodUsuario($codUsuario)) {
            return false;
        }

        $this->_usuarios[$codUsuario]["borrado"] = $borrado;

        return true;
    }

    /**
     * Función que cambia el role de un usuario
     *
     * @param integer $codUsuario Usuario a buscar
     * @param integer $role Role a asignar
     * @return boolean Devuelve true si ha podido asignar el role al usuario.
     * False si no existe el usuario, role o no ha podido asignarlo
     */
    public function setUsuarioRole(int $codUsuario, int $role): bool
    {
        if (!$this->existeCodUsuario($codUsuario) || !$this->existeRole($role)) {
            return false;
        }

        $this->_usuarios[$codUsuario]["cod_role"] = $role;

        return true;
    }

    /**
     * Devuelve un array con todos los usuarios existentes. 
     * La clave es el codigo de usuario, el valor es el nick del usuario 
     *
     * @return array Array con todos los usuarios existentes
     */
    public function dameUsuarios(): array
    {
        $users = array();

        foreach ($this->_usuarios as $key => $user) {
            $users[$key] = $user["nick"];
        }

        return $users;
    }

    /**
     * Devuelve un array con todos los roles existentes. 
     * La clave es el codigo de role, el valor es el nombre del role 
     *
     * @return array Array con todos los roles existentes
     */
    public function dameRoles(): array
    {
        $roles = array();

        foreach ($this->_roles as $key => $role) {
            $roles[$key] = $role["nombre"];
        }

        return $roles;
    }
}

?>
