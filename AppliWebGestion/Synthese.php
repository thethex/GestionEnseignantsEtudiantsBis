<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Etudiants absents</title>
    </head>

    <body>
    	<p>
            <h2>Veuillez ne cochez que les élèves absents, puis cliquer sur Valider</h2>
            <h2>L'ordre des cours est le même que sur la fiche d'absence</h2>  
            <?php
            require_once('Config.php');
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //Selon l'ID de la fiche sélectionné, des requetes SQL vont être réalisés dans le but de reproduire la fiche d'absence, en affichant les noms et prénoms des étudiants, avec une case à cocher pour chaque cours et chaque étudiant
            $dataetudiants = $bdd->query('SELECT idetudiant, nom, prenom FROM '.$bdName.'.etudiant WHERE (filiere = '.'"'.$_POST['filiere'].'"'.' AND promo = '.$_POST['promo'].')');
            $etudiants = $dataetudiants->fetchAll();

            //La liste de l'ensemble des cours est enregistrée dans une chaîne de caractère dans la base de données. Cela permet d'associer une fiche d'absence a tous les cours.
            //Le code ci-dessous permet de convertir cette chaîne de caractère en un array qui contient un élément pour chaque cours, son identifiant.
            $iddescours = $_POST['iddescours'];
            $parcoureur = '';
            $arrayID = array();
            for ($i=0; $i<strlen($iddescours);$i++){
                if ($iddescours[$i]!=';'){
                    $parcoureur .=$iddescours[$i];
                }
                else{
                    array_push($arrayID,$parcoureur);
                    $parcoureur = '';
                }
            }
            echo('<form method="post" action="UpdateBDD.php">');
            //Pour chaque étudiant et chaque cours, on va générer une case a cocher (par formulaire), dont la valeur est une chaîne de caractère qui concatène l'ID de l'étudiant d'une part, puis l'ID du cours d'autre part, séparé par un point virgule
            //On met également une case a coché au cas ou l'étudiant a été absent de la journée, a tous les cours
            for ($i = 0; $i < count($etudiants); $i++){
                for($j=0; $j < count($arrayID); $j++){
                    if($j==0){
                        echo(' Absent au cours '.($j+1).'');
                    }
                    else{
                        echo('au cours '.($j+1).'');
                    }
                    echo('<input type="checkbox" name="'.$etudiants[$i]['idetudiant'].';'.$arrayID[$j].'" value="'.$etudiants[$i]['idetudiant'].';'.$arrayID[$j].';'.'">');
                }
                echo(' Absent à tous les cours de la journée');
                echo('<input type="checkbox" name="'.$etudiants[$i]['idetudiant'].'" value="'.$etudiants[$i]['idetudiant'].';'.$_POST['iddescours'].'">');
                echo(''.$etudiants[$i]['nom'].' '.$etudiants[$i]['prenom'].'');
                echo('<br/>');
                echo('<br/>');    
            }
            $listcours=serialize($arrayID);//Permet de transmettre un array par la méthode POST
            echo('<input type="hidden" name="filiere" value='.$_POST['filiere'].' />');
            echo('<input type="hidden" name="promo" value='.$_POST['promo'].' />');
            echo('<input type="hidden" name="iddescours" value='.$listcours.' />');
            echo('<h2>');
            echo('<input type="submit" value="Valider"/>');
            echo('</h2>');
            echo('</form>');
            ?>
    		
    	</p>
    </body>
</html>