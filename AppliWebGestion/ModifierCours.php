<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>ModifierUnCours</title>
    </head>

    <body>
        <h1>Cours à Modifier</h1>
        <h4>Rappel : seuls les cours incomplets sont affichés
        <br/>

        
    <?php
    require_once('Config.php');
    // Activation des rapports d'erreurs et connexion à la base de données.
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //ModifierCours.php est une copie identique à RechercherCours.php, à l'exception que, avant de charger les cours incomplet, on met à jour le cours que l'on vient de modifier dans le formulaire "RechercherCours.php", de sorte à ce que celui-ci ne devienne plus un cours incomplet, et ne soit plus affiché dans la liste des cours incomplets.
    //MODIFICATION A PARTIR D'ICI, avant de réafficher tous les cours incomplets
    $idprofA = $bdd->query('SELECT idenseignant FROM '.$bdName.'.enseignant WHERE (nom = "'.$_POST['nomenseignant'].'")');
    $idprof2A = $idprofA->fetchAll();
    $idmoduleA = $bdd->query('SELECT idmodule FROM '.$bdName.'.module WHERE (nom = "'.$_POST['nommodule'].'")');
    $idmodule2A = $idmoduleA->fetchAll();
    //Ci-dessus, On va chercher des noms de modules et d'enseignants déjà existant dans la base de données.
    $idduprofA = $idprof2A[0]['idenseignant'];
    $iddumoduleA = $idmodule2A[0]['idmodule'];

    //On met a jour le cours dans la base de données avec les noms de modules et d'enseignants trouvés
    //Remaque : Si une faute a été comise sur le nom de module ou le nom d'enseignant entrée, une erreure SQL apparaîtra et rien ne se passera
    $bdd->query('UPDATE '.$bdName.'.cours SET idmodule = '.$iddumoduleA.', idenseignant = '.$idduprofA.' WHERE enseignementpolytech1.cours.idcours = '.$_POST['idcours'].'');
    // FIN DE MODIFICATION ICI

//REMARQUE : Lorsqu'un champ de formulaire (de type chaîne de caractères) est laissé vide, la chaîne de caractères transféré dans le $_POST est un "/". On le distingue alors dans la condition if ci-dessous :
    if ($_POST['filiere']=='NULL' AND $_POST['Date']!='/'){
        $datacoursincomplets = $bdd->query('SELECT idcours, type, datecours, duree, idmodule, idenseignant FROM '.$bdName.'.cours WHERE (datecours LIKE "'.$_POST['Date'].'%'.'") and ( (idmodule IS NULL) OR (idenseignant IS NULL) )');
        $coursincomplets = $datacoursincomplets->fetchall();
    }
    if ($_POST['filiere']=='NULL' AND $_POST['Date']=='/'){
        $datacoursincomplets = $bdd->query('SELECT idcours, type, datecours, duree, idmodule, idenseignant FROM '.$bdName.'.cours WHERE (idmodule IS NULL) OR (idenseignant IS NULL)');
        $coursincomplets = $datacoursincomplets->fetchall();
    }

    
    echo('<h5>');
    
    for ($i=0; $i<count($coursincomplets);$i++){
        echo('<form method="post" action="ModifierCours.php">');
        echo('<input type="hidden" name="Date" value='.$_POST['Date'].' />');
        echo('<input type="hidden" name="filiere" value='.$_POST['filiere'].' />');
        echo('<input type="hidden" name="idcours" value='.$coursincomplets[$i]['idcours'].' />');
        $nomdumodule='N/A, ajouter :';
        $nomduprof='N/A, ajouter (nom enseignant):';
        echo('Date et heure : '.$coursincomplets[$i]['datecours'].'');
        if ($coursincomplets[$i]['idmodule'] != NULL){
            $datanommodule = $bdd->query('SELECT nom FROM '.$bdName.'.module WHERE (idmodule='.$coursincomplets[$i]['idmodule'].')');
            $nommodule= $datanommodule->fetchall();
            $nomdumodule = $nommodule[0]['nom'];
            echo(' | Module : '.$nomdumodule.'');
            echo('<input type="hidden" name="nommodule" value='.$nomdumodule.' />');
        }
        else {
            echo(' | Module : '.$nomdumodule.'');
            echo('<input type="text" name="nommodule" />');
        }
        
        if ($coursincomplets[$i]['idenseignant'] != NULL){
            $datanomprof = $bdd->query('SELECT nom FROM '.$bdName.'.enseignant WHERE (idenseignant='.$coursincomplets[$i]['idenseignant'].')');
            $nomprof= $datanomprof->fetchall();
            $nomduprof = $nomprof[0]['idenseignant'];
            echo(' | Enseignant : '.$nomduprof.'');
            echo('<input type="hidden" name="nomenseignant" value='.$nomduprof.' />');
            
        }
        else {
            echo(' | Enseignant : '.$nomduprof.'');
            echo('<input type="text" name="nomenseignant" />');
        }
        echo('<input type="submit" value="Modifier"/>');
        echo('</form>');
        echo('<br/>');
        
        
    }

    echo('</h5>');
    echo('Tous les cours incomplets ont été affichés');
    
    
    ?>
    </h4>
    </body>
</html>