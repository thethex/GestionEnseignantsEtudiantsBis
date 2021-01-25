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
    echo("Veuillez valider l'étudiant à vérifier");
    echo("</h2>");

    $dataetudiants = $bdd->query('SELECT idetudiant, nom, prenom, filiere, promo, groupetd, groupetp FROM '.$bdName.'.etudiant WHERE (nom='.'"'.$_POST['Nom'].'"'.' AND prenom='.'"'.$_POST['Prenom'].'"'.')');
    $etudiants = $dataetudiants->fetchall();


    for ($i=0;$i<count($etudiants);$i++){
        echo('<form method="post" action="CheckEtudiant.php">');
        echo($etudiants[$i]['nom'].' '.$etudiants[$i]['prenom'].' ; '.$etudiants[$i]['filiere'].' ; '.$etudiants[$i]['promo'].' ; groupe TD : '.$etudiants[$i]['groupetd'].' ; groupe TP : '.$etudiants[$i]['groupetp']);
        echo('<input type="hidden" name="nom" value='.$etudiants[$i]['nom'].' />');
        echo('<input type="hidden" name="prenom" value='.$etudiants[$i]['prenom'].' />');
        echo('<input type="hidden" name="idetudiant" value='.$etudiants[$i]['idetudiant'].' />');
        echo('<input type="submit" value="Vérifier"/>');
        echo('</form>');
        echo('</br>');
    }

    

    
    ?>
    </body>
</html>