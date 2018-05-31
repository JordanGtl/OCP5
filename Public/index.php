<?php
# Affichage des erreur (pour le DEV)
ini_set('display_errors', '1');

# Fichier de configuration
require('../Config/Config.php');
require('../App/App.class.php');

# Include des API
require ('../vendor/autoload.php');

# Variables
App::Load();

# Chargement du template
App::renderTemplate();
?>

