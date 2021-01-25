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

    //Requete SQL pour ajouter l'étudiant à la base de données (selon les informations du formulaire)
    $bdd->query('INSERT INTO '.$bdName.'.etudiant (nom,prenom,promo,filiere,groupetd,groupetp) VALUES ('.'"'.$_POST['Nom'].'"'.','.'"'.$_POST['Prenom'].'"'.','."'".$_POST['promotion']."'".','.'"'.$_POST['filiere'].'"'.','.'"'.$_POST['groupetd'].'"'.','.'"'.$_POST['groupetp'].'"'.')');

    //Une fois l'étudiant ajouté, il doit alors participer aux mêmes cours que les étudiants appartenant à la même filière de la même année.
    //Il faut alors réaliser une série de requêtes SQL dans le but d'identifier les Identifiants de cours auquel le nouvel étudiant doit participer, afin d'ajouter ces éléments dans la table "Présence" de la base de données.
    //Ci-dessous, on extrait l'ID d'un étudiant appartenant à la même filière de la même année que l'édutiant ajouté (qui ne soit pas ce nouvel étudiant).
    $datasameetudiant = $bdd->query('SELECT MIN(idetudiant) FROM '.$bdName.'.etudiant WHERE (promo='.$_POST['promotion'].' AND filiere='."'".$_POST['filiere']."'".' AND groupetd='."'".$_POST['groupetd']."'".' AND groupetp='."'".$_POST['groupetp']."'".')');
    $sameetudiant = $datasameetudiant->fetchall();
    
    //Ci-dessous, on extrait l'ID du nouvel étudiant ajouté.
    //En théorie, entre l'ajout de l'étudiant plus haut et l'extraction de son ID ci-dessous, aucun autre étudiant n'a été ajouté pendant la procédure.
    $datadernieridetudiant = $bdd->query('SELECT MAX(idetudiant) FROM '.$bdName.'.etudiant');
    $dernieridetudiant = $datadernieridetudiant->fetchAll();
    
    //Ci-dessous, grâce à l'ID d'un étudiant "modèle", on va pouvoir trouver les ID des cours que le nouvel étudiant doit suivre.
    $datasamecours = $bdd->query('SELECT idcours FROM '.$bdName.'.presence WHERE (idetudiant='.$sameetudiant[0]['MIN(idetudiant)'].')');
    $samecours = $datasamecours->fetchall();
    
    //Enfin, pour chaque ID cours obtenu, on va créer un élément de la table "Présence" qui va relier l'ID du cours à l'ID du nouvel étudiant ajouté.
    for($i=0; $i<count($samecours);$i++){
        $bdd->query('INSERT INTO '.$bdName.'.presence (idetudiant,idcours) VALUES ('.$dernieridetudiant[0]['MAX(idetudiant)'].','.$samecours[$i]['idcours'].')');
    }

    echo("L'étudiant a bien été ajouté à la base de données");
    ?>
    </body>
</html>