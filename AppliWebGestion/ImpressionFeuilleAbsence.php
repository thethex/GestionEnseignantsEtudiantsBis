<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Impression feuille d'absence</title>
    </head>

    <body>
    	<h1> Impression d'une feuille d'absence</h1>
    	<h4>
    		<br/>
    		<form method="post" action="Apercu.php">
    			<?php
    			//Implémentation d'un code PHP ici pour préremplir le champ de texte par la date actuelle.
    			$currentdate = date('Y-m-d');
    		echo('Sélection du jour : [YYYY-MM-DD]: <input type="text" name="Date" value="'.$currentdate.'" /> <input type="submit" value="Aperçu"/>');
    		?>
    		<br/>
    		Classe concernée : <select name="filiere" id="filiere">
    			<option value='IAI'>IAI</option>
    			<option value='MM'>MM</option>
    			<option value='EBE'>EBE</option>
    			<option value='IDU'>IDU</option>
    			<option value='ITII-CM'>ITII-CM</option>
    			<option value='ITII-MP'>ITII-MP</option>
    			</select>
    		<select name="annee" id="annee">
    			<option value=1>1</option>
    			<option value=2>2</option>
    			<option value=3>3</option>
    			<option value=4>4</option>
    			<option value=5>5</option>
    			</select>
    		</form>	
    	</h4>
    </body>
</html>

<!-- Brouillon
<img src="LogoPolytech.jfif" class="logofloat" alt="Logo flotant"/>
 -->