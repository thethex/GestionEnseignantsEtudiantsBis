<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>SaisieModule</title>
    </head>

    <body>
    	<h1> Saisie du module à ajouter</h1>
    	<h4> 
    		<form method="post" action="AjouterModule.php">
    		Nom : <input type="text" name="Nom" />
    		Semestre : <input type="int" name="semestre" />
    		heurescm : <input type="int" name="heurescm" />
    		heurestd  : <input type="int" name="heurestd" />
    		heurestp  : <input type="int" name="heurestp" />
    		<?php
    		// Ici, on intègre du code PHP dans le but d'extraire tous les noms d'enseignant dans la base de données et de générer une liste déroulante pour sélectionner l'enseignant en charge du module.
    		require_once('Config.php');
    		$bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		$dataprof = $bdd->query('SELECT * FROM '.$bdName.'.enseignant ');
    		$ListeEnseignants = $dataprof->fetchAll();
    		echo'Enseignant responsable  :';
    		echo'<select name="idprof" id="idprof">';
    		echo'<option value='.'0'.'>'.'non défini'.'</option>';
    		//On crée une boucle for pour ajouter chaque enseignant dans la liste déroulante
    		for($i=0; $i<count($ListeEnseignants); $i++){
    			echo'<option value='.($i+1).'>'.$ListeEnseignants[$i]['nom'].' '.$ListeEnseignants[$i]['prenom'].'</option>';
    		}
    		echo'</select>';?>
    		<input type="submit" value="Enregistrer"/>
    		</form>
    	 </h4>
    </body>
</html>