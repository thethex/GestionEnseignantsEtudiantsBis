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
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //Requete SQL simple qui ajoute l'enseignant à la base de données (selon les informations du formulaire)
    $bdd->query('INSERT INTO '.$bdName.'.enseignant (nom,prenom,type,service,heuresup) VALUES ('.'"'.$_POST['Nom'].'"'.','.'"'.$_POST['Prenom'].'"'.','."'".$_POST['type']."'".','.'"'.$_POST['service'].'"'.','.'"'.$_POST['heuresupp'].'"'.')');
    echo("L'enseignant a bien été ajouté à la base de données");
    ?>
    </body>
</html>