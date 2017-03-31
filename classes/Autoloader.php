<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 09/12/2016
 *
 */

class Autoloader{

    /**
     *  Enregistre notre autoloader
     *  C'est la fonction à appeler pour demarrer l'uploads automatique
     */

    static function register(){
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Inclue le fichier correspondant à notre classe
     * @param $class string Le nom de la classe à charger
     */

    static function autoload($class){
        require '' . $class . '.php';
    }

}
