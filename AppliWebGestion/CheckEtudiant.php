<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>AjoutEtudiant</title>
    </head>

    <body>
    <?php
    require_once('Config.php');
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo("<h2>");
    echo("L étudiant ".$_POST['nom']." ".$_POST['prenom']." a manqué les cours suivants :");
    echo("</h2>");
    echo("</br>");

    $datacoursmanque = $bdd->query('SELECT idcours FROM '.$bdName.'.presence WHERE (idetudiant='.(int)$_POST['idetudiant'].' AND presence="absent")');
    $coursmanque = $datacoursmanque->fetchall();

    for ($i=0;$i<count($coursmanque);$i++){
        $datalecoursmanque = $bdd->query('SELECT datecours FROM '.$bdName.'.cours WHERE (idcours='.(int)$coursmanque[$i]['idcours'].')');
        $lecoursmanque = $datalecoursmanque->fetchall();
        echo('Date et heure du cours manqué : '.$lecoursmanque[0]['datecours'].'');
        echo('</br>');
    }

    

    
    ?>
    </body>
</html>