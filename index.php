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
include('controler/Frontend.class.php');

# Initialisation des classes
$front 			= new Frontend($db);

# variables du site
$title = 'Jordan GUILLOT - Développeur';

# Template
require('template.php');
?>

