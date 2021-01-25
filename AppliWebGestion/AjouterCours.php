<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>AjoutEnseignant</title>
    </head>

    <body>
    <?php
    require_once('Config.php');
    error_reporting(E_ERROR | E_WARNING | E_PARSE);  //N'autoriser que les retours d'erreurs menant à un arrêt obligatoire du script
    //Connexion à la base de données
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd); 
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Activer le retour d'erreurs sur les requêtes SQL

    //Insertion du cours dans la base de données.
    $bdd->query('INSERT INTO '.$bdName.'.cours (type,datecours,duree) VALUES ('.'"'.$_POST['type'].'"'.','.'"'.$_POST['Date'].'"'.','."'".(float)$_POST['heure']."'".')');
    echo("Le cours a bien été ajouté à la base de données");
    ?>
    </body>
</html>