<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Liste fiches</title>
    </head>

    <body>
    	<h1>Liste des fiches</h1>
    	<h4>
    		<br/>
            
            <?php
            require_once('Config.php');
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $ladate = $_POST['Date']." 00:00:00";

            //Le code ci-dessous va relever tous les identifiants de cours associé à la date rentrée, et va tous les afficher, et proposer de le sélectionner avec un bouton
            $dataid = $bdd->query('SELECT idfiche, filiere, promo, iddescours FROM '.$bdName.'.fichesabsences WHERE (dateday = '.'"'.$ladate.'"'.')');
            $id = $dataid->fetchAll();

            for ($i = 0; $i < count($id); $i++){
                echo('<form method="post" action="Synthese.php">');
                echo('Fiche : '.$id[$i]['idfiche'].' <input type="submit" value="Sélectionner"/>');
                echo('<input type="hidden" name="filiere" value='.$id[$i]['filiere'].' />');
                echo('<input type="hidden" name="promo" value='.$id[$i]['promo'].' />');
                echo('<input type="hidden" name="iddescours" value='.$id[$i]['iddescours'].' />');
                echo('<br/>');
                echo('</form>');
            }

            ?>
    		
    	</h4>
    </body>
</html>