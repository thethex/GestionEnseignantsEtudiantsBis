<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>AjoutModule</title>
    </head>

    <body>
    <?php
    require_once('Config.php');
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $idprof = $_POST['idprof'];
    //Selon si un enseignant a été sélectionné ou non, on l'identifie selon l'id de l'enseignant transmis par le forumaire
    //L'ID 0 correspond à la non sélection d'un enseignant, on le distingue alors dans un if qui va réaliser une requete SQL de l'ajout du module sans qu'il ne soit encore lié à un enseignant.
    if ($idprof==0)
        $bdd->query('INSERT INTO '.$bdName.'.module (nom,semestre,heurescm,heurestd,heurestp) VALUES ('.'"'.$_POST['Nom'].'"'.','.'"'.$_POST['semestre'].'"'.','."'".$_POST['heurescm']."'".','.'"'.$_POST['heurestd'].'"'.','.'"'.$_POST['heurestp'].'"'.')');
    else{
        $bdd->query('INSERT INTO '.$bdName.'.module (nom,semestre,heurescm,heurestd,heurestp,idenseignant) VALUES ('.'"'.$_POST['Nom'].'"'.','.'"'.$_POST['semestre'].'"'.','."'".$_POST['heurescm']."'".','.'"'.$_POST['heurestd'].'"'.','.'"'.$_POST['heurestp'].'"'.','.'"'.$idprof.'"'.')');
    }
    echo("Le module a bien été ajouté à la base de données");
    ?>
    </body>
</html>