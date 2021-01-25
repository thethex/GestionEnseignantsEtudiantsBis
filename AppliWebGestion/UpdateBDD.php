<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>BDD mise a jour</title>
    </head>

    <body>
    	<p>
            <?php
            require_once('Config.php');
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //On génère de nouveau tous les ID d'étudiants à partir de la filière et de la promo
            $dataetudiants = $bdd->query('SELECT idetudiant FROM '.$bdName.'.etudiant WHERE (filiere = '.'"'.$_POST['filiere'].'"'.' AND promo = '.$_POST['promo'].')');
            $etudiants = $dataetudiants->fetchAll();

            $listedetouslescours=unserialize($_POST['iddescours']);//Reconvertit l'array transmis par la méthode POST pour être exploitable et lisible
            //ci-dessous, on parcours chaque élément $_POST, qui contient un élément pour chaque case cochée, correspond à un élève absent a un cours.
            //On sépare l'ID de l'étudiant à (ou aux) ID du cours a l'aide du caractère point virgule qui sert de repère et de distinction des ID. Ainsi, les autres éléments présents dans la méthode POST (la filière, la promo, puis plus tard, groupe TD et groupe TP) ne prendront pas en compte ces oppérations, car ils ne contiennent pas le caractère "point virgule"
            foreach($_POST as $k){
                $parcoureur = '';
                $idetu=0;
                $iddescours=array();
                for ($i=0; $i<strlen($k);$i++){
                    if ($k[$i]!=';'){
                        $parcoureur .=$k[$i];
                    }
                    else{
                        if ($idetu==0){
                            $idetu=(int)$parcoureur;//Le premier élément trouvé est l'ID de l'étudiant
                        }
                        else{
                            array_push($iddescours,$parcoureur);//Les éléments suivants sont des ID de cours
                        }
                        $parcoureur = '';
                    }
                    
                }
                //On met a jour la table présence selon les étudiants absents
                if ($idetu!=0){
                    foreach($iddescours as $c){
                        $bdd->query('UPDATE presence SET presence = "absent" WHERE idetudiant='.$idetu.' and idcours='.$c.' and presence is NULL');
                    } 
                }
              
            }
            
            //Gestion des autres étudiants non marqués absent sur le formulaire, qui sont donc tous présents
            for($i=0;$i<count($etudiants);$i++){
                
                foreach($listedetouslescours as $cc){
                    $bdd->query('UPDATE presence SET presence = "present" WHERE idetudiant='.$etudiants[$i]['idetudiant'].' and idcours='.$cc.' and presence is NULL');
                }
                
                
            }
            
            echo('La présence des étudiants à bien été mise a jour sur le serveur');
            ?>
    		
    	</p>
    </body>
</html>