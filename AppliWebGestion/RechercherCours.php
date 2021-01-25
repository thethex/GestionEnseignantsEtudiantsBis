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

    // Le filtrage concernant la classe sélectionnée n'a pas encore été implémentée.
    // On réalise alors une requete SQL pour afficher les cours incomplets correspondant à la date sélectionnée.
    if ($_POST['filiere']=='NULL'){
        $datacoursincomplets = $bdd->query('SELECT idcours, type, datecours, duree, idmodule, idenseignant FROM '.$bdName.'.cours WHERE (datecours LIKE "'.$_POST['Date'].'%'.'") and ( (idmodule IS NULL) OR (idenseignant IS NULL) )');
        $coursincomplets = $datacoursincomplets->fetchall();
    }
    echo('<h5>');
    
    //On prépare l'affichage de tous les cours incomplets obtenus.
    //On prépare également un formulaire propre à chaque cours, de sorte à ce que les modification apportées dans les parties vides soient enregistrées une fois que l'on appui sur le bouton d'envoie. Une page ModifierCours.php mettra a jour le cours.
    for ($i=0; $i<count($coursincomplets);$i++){
        echo('<form method="post" action="ModifierCours.php">');
        //Ci-dessous, on conserve les informations sur la Date, la filière et l'ID du cours lorsque l'on souhaitera modifier le cours.
        echo('<input type="hidden" name="Date" value='.$_POST['Date'].' />');
        echo('<input type="hidden" name="filiere" value='.$_POST['filiere'].' />');
        echo('<input type="hidden" name="idcours" value='.$coursincomplets[$i]['idcours'].' />');
        $nomdumodule='N/A, ajouter :';
        $nomduprof='N/A, ajouter (nom enseignant):';
        echo('Date et heure : '.$coursincomplets[$i]['datecours'].'');
        // On initie le nom du module et le nom de l'enseignant.
        // Puis, on vérifie dans un "if" si cette information n'est pas déjà renseigné, auquel cas, on réalise la requete SQL adéquate pour aller chercher l'information pour l'afficher.
        // Si l'information n'est pas présente, la partie "else" s'exécute et ajoute un champ de texte à remplir pour mettre à jour la donnée.
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
        //Même procédé pour le nom de l'enseignant
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