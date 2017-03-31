<?php
/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 12/12/2016
 * Time: 19:45
 */


/* * CHARGEMENT DE NOTRE AUTOLOADER ********* */

require  "classes/Autoloader.php";
Autoloader::register();

/* * FIN ************************************ */

define ("ADD_BD" , "localhost");  //ADRESSE DU SERVER DE LA BASE DE DONNÉES

define ("NOM_BD" , "reporting" ); //NOM DE LA BASE DE DONNÉES

define ("USER_BD" , "root" );     //NOM_UTILISATEUR

define ("PASS_BD" , "root" );     //MOT DE PASSE

define ("URL" , "http://localhost/design_pattern/"); //ADDRESSE INDEX DE L'APPLICATION

$_GLOBALS['vr'] = Database::getDb()->all("formVrByDir");

$_GLOBALS['groupe_intervention'] = Database::getDb()->all("groupe_intervention");

$_GLOBALS['ui'] = Database::getDb()->all("ui");

$_GLOBALS['colonnes_encours']  =  Database::getDb()->all("colonnes" , "encours" , 1);

$_GLOBALS['colonnes_releves']  =  Database::getDb()->all("colonnes" , "releves" , 1);

foreach ($_GLOBALS['colonnes_encours'] as $col )
    $buffer[] = $col['col_label'];

$_GLOBALS['entete_encours'] = $buffer;

foreach ($_GLOBALS['colonnes_releves'] as $col )
    $buffer[] = $col['col_label'];

$_GLOBALS['entete_releves'] = $buffer;
