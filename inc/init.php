
<?php
// Connexion à la BDD boutik
$pdo = new PDO('mysql:host=localhost;dbname=boutik','root','', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//var_dump($pdo);

// variable globale pour rendre le html dynamique, marche pour echo
$content = "";

// Ouverture d'une session
session_start();

// définition de constantes
define("RACINE_SITE", $_SERVER["DOCUMENT_ROOT"] . "/boutik/");
define("URL", "http://" .$_SERVER["HTTP_HOST"] . "/boutik/");

// echo 'Le dossier vers notre site est :' . RACINE_SITE . '<br>';
// echo 'L URL vers notre site est :' . URL;

require_once("fonction.php");

?>