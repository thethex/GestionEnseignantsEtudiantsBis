<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Impression feuille d'absence</title>
    </head>

    <body>
        <h1>Liste des cours à imprimer</h1>
        <h4>Vérifiez si les cours correspondent bien à ceux de la journée, puis cliquez sur "Générer le PDF"</h4>
        <br/>

        
    <?php
    require_once('Config.php');
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $lesmois=['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    //Cette page permet de visualiser l'ensemble des cours de la journée avant de générer le PDF à imprimer.
    //Ci-dessous, processus pour convertir correctement l'année de promotion à l'année d'étude
    $anneecourante = (int)date('Y');
    $moiscourrant = (int)date('m');
    if ($moiscourrant >=$nouvelanneescolaire){//paramètre $nouvelanneescolaire dans Config.php
        $anneecourante = $anneecourante + 1;
    }

    $promo = $anneecourante + 5 - $_POST['annee'];


    //On relève un identifiant d'étudiant "modèle" pour ensuite relever tous les cours que suit cet élève, qui représente son groupe.
    //Nous souhaitions obtenir une liste de cours qui concernent les cours suivits par un groupe d'étudiants pour la journée.
    //Toutefois, on ne peut pas obtenir cette liste de cours avec une requête SQL. J'étais obligé de devoir passer par 3 requêtes SQL pour obtenir tous les cours relatifs à un groupe d'étudiants qui correspondent à la date donnée.
    //Les 2 premières requetes ci-dessous
    $dataidetudiant = $bdd->query('SELECT MIN(idetudiant) FROM '.$bdName.'.etudiant WHERE (filiere="'.$_POST['filiere'].'" and promo='.$promo.')');
    $idetudiant = $dataidetudiant->fetchall();

    $dataidcours = $bdd->query('SELECT idcours FROM '.$bdName.'.presence WHERE (idetudiant='.$idetudiant[0]['MIN(idetudiant)'].')');
    $idcours = $dataidcours->fetchall();

    //On prépare les informations à transmettre lors de l'impression de la fiche d'absence
    echo('<h5>');
    echo('Jour : '.substr($_POST['Date'],8,2).' '.$lesmois[(int)substr($_POST['Date'],5,2)].' '.substr($_POST['Date'],0,4));
    echo(' | Classe : '.$_POST['filiere'].' '.$_POST['annee']);
    echo('<br/>');
    echo('<form method="post" action="Impression.php">');
    echo('<input type="hidden" name=filiere value='.$_POST['filiere'].' />');
    echo('<input type="hidden" name=promo value='.$promo.' />');
    echo('<input type="hidden" name=annee value='.$_POST['annee'].' />');
    echo('<input type="hidden" name=Date value='.$_POST['Date'].' />');

    //On va transmettre par la méthode POST les informations relatifs à chaque cours, pour les faires afficher sur la fiche d'absence à des endroits différents. La variable $ncours permet de différencier les 5 cours.
    $ncours=0;
    
    for ($i=0; $i<count($idcours);$i++){
        //La 3eme requete SQL est réalisée ici, une fois pour chaque cours
        $datalecours = $bdd->query('SELECT idcours, type, datecours, duree, idmodule, idenseignant FROM '.$bdName.'.cours WHERE (idcours='.$idcours[$i]['idcours'].') and (datecours LIKE "'.$_POST['Date'].'%'.'") ORDER BY datecours');

        $lecours = $datalecours->fetchall();
        
        if (count($lecours)!=0){
            $ncours+=1;
            $nomdumodule='N/A';//Le nom du module et de l'enseignant ne sont pas forcément indiqués
            $nomduprof='N/A';
            $typecours = $lecours[0]['type'];
            $duration = $lecours[0]['duree']*60; //Calcul de la durée du cours
            $durationheure = intdiv($duration,60);
            $durationminutes = $duration%60;
            $finminute = $durationminutes + (int)substr($lecours[0]['datecours'],14,2);
            if ($finminute >=60){
                $finminute = $finminute-60;
                $durationheure = $durationheure + 1;
            }
            $heurefin = (int)substr($lecours[0]['datecours'],11,2) + $durationheure;
            echo(''.substr($lecours[0]['datecours'],11,2).'h'.substr($lecours[0]['datecours'],14,2).' - '.$heurefin.'h'.$finminute.'');
            if ($lecours[0]['idmodule'] != NULL){//Attribution du nom du module si il est précisé
                $datanommodule = $bdd->query('SELECT nom FROM '.$bdName.'.module WHERE (idmodule='.$lecours[0]['idmodule'].')');
                $nommodule= $datanommodule->fetchall();
                $nomdumodule = $nommodule[0]['nom'];
            }
            echo(' | Module : '.$nomdumodule.'');
        
            if ($lecours[0]['idenseignant'] != NULL){//Attribution du nom de l'enseignant si il est précisé
                $datanomprof = $bdd->query('SELECT nom FROM '.$bdName.'.enseignant WHERE (idenseignant='.$lecours[0]['idenseignant'].')');
                $nomprof= $datanomprof->fetchall();
                $nomduprof = $nomprof[0]['nom'];    
            }
            echo(' | Enseignant : '.$nomduprof.'');
            echo('<br/>');
            echo('<input type="hidden" name='.'idcours'.$ncours.' value='.$lecours[0]['idcours'].' />');
            echo('<input type="hidden" name='.'type'.$ncours.' value='.$typecours.' />');
            echo('<input type="hidden" name='.'module'.$ncours.' value='.$nomdumodule.' />');
            echo('<input type="hidden" name='.'enseignant'.$ncours.' value='.$nomduprof.' />');
            echo('<input type="hidden" name='.'heure'.$ncours.' value='.substr($lecours[0]['datecours'],11,2).'h'.substr($lecours[0]['datecours'],14,2).' />');

        }
        
    }



    echo('<input type="hidden" name=ncours value='.$ncours.' />');
    
    echo('</h5>');
    echo('Tous les cours pour la fiche absence ont été affichés ');
    echo('<input type="submit" value="Générer le PDF" />');
    echo('<br/>');
    echo("Veuillez patienter lors de la génération du PDF");
    echo('</form>');
    
    
    ?>
    </h4>
    </body>
</html>