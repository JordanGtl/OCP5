<?php
# Initialisation des sessions
session_start();

# Affichage des erreur (pour le DEV)
ini_set('display_errors', '1');

# Connexion sql
try 
{
    $db = new PDO('mysql:host=localhost;dbname=OCP5;charset=utf8', 'root', '');
}
catch(Exception $e) 
{
    die('Erreur : '.$e->getMessage());
}

# Controler
include('controler/Parseur.class.php');
include('controler/Frontend.class.php');

# Initialisation des classes

$parseur = new Parseur();
$front = new Frontend($db, $parseur);

# variables du parseurs
$parseur->classes = array('front' => $front);
$parseur->vars = array('title' => 'Jordan GUILLOT - Développeur');

# Template
echo $parseur->ParseTemplate('template.html');
?>

