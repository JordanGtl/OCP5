# OCP5

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/56025b03d3334d639ab01902a0ed94d3)](https://www.codacy.com/app/JordanGtl/OCP5?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=JordanGtl/OCP5&amp;utm_campaign=Badge_Grade)

# Installation
Modifier le fichier de configuration "Config/config.php" et remplacer les informations de connexion SQL (ci-dessous) par vos identifiants de connexion à la base de données
```php
#Paramètre de connexion Mysql
define('SQL_HOST', 				'127.0.0.1');
define('SQL_USER', 				'root');
define('SQL_MDP', 				'');
define('SQL_DB', 				'OCP5');
```
Le blog utilise mailjet afin d'assurer la délivrabilité des emails. Vous devez mettre à jour l'id et le token afin que votre blog utilise mailjet pour l'envoi d'email
```php
# paramètre API mailjet
define('MAILJET_ID', 			'876b017e0628afe3008af7729b3f970a');
define('MAILJET_SECRET', 		'fec6b6c20a525250a2211c361a3bf7d4');
```
Les paramètres ci-dessous sont à modifier à votre convenance afin de personnaliser votre blog.

```php
# Paramètres du site
define('TITLE', 								'Blog - Jordan Guillot');
define('CONTACT_EMAIL',         'thejordan01@gmail.com');
define('CONTACT_NAME',          'Jordan GUILLOT');
```
