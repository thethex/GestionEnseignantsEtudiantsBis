<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Scan Feuille Absence</title>
    </head>

    <body>
    	<h1>Méthode d'enregistrement manuel</h1>
    	<h4>
    		<br/>
            <form method="post" action="RechercheFiche.php">
            <?php
                //Implémentation d'un code PHP ici pour préremplir le champ de texte par la date actuelle.
                $currentdate = date('Y-m-d');
            echo('Sélection du jour : [YYYY-MM-DD]: <input type="text" name="Date" value="'.$currentdate.'" /> <input type="submit" value="Chercher une fiche d absence"/>');
            ?>
    		</form>	
    	</h4>
    </body>
</html>