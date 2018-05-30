<?php
# Initialisation des sessions
session_start();

# Affichage des erreur (pour le DEV)
ini_set('display_errors', '1');

use \Blog\Autoloader;

# Autoloader
require ('Class/Autoloader.class.php');
Autoloader::Register();









////////////////////////////////////////

# Connexion sql (erreur fatale si connexion impossible)
try 
{
    $db = new PDO('mysql:host=localhost;dbname=OCP5;charset=utf8', 'root', '');
}
catch(Exception $e) 
{
    die('Erreur : '.$e->getMessage());
}

# Classes principales
include('model/Membre.class.php');
include('controler/Parseur.class.php');
include('controler/Frontend.class.php');

# Initialisation des classes

$parseur 			= new Blog\Html\Template();
$front 				= new Frontend($db, $parseur);

# variables du parseurs
$parseur->classes 	= array('front' => $front, 'membres' => new Membres($db));
$parseur->vars 		= array('title' => 'Jordan GUILLOT - Développeur');

# Template
echo $parseur->ParseTemplate('template.html');
?>

