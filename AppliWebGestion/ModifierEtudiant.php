<?php
require_once('Config.php');
?>
<!DOCTYPE html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/AjouterEtudiant.css" />
  <title>ModifierEtudiant</title>
</head>
<div class="contenu-centre">
  <div id="content" class="stylized-form">

  	<?php

      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      echo 'connected succesfully<br>
      		<div> <h1>Ajouter une année a tout les eleves</h1></div>
      		<form method="post" action="index.php">
      		<input type="hidden" name="page" value="ModifierEtudiant" >
      		<input type="hidden" name="ajoueannee" value="true" >
      		<input class="grosBoutonBleu" type="submit" value="Ajouter année" name="ajoueannee">
      		</form>

';
	if(isset($_POST['ajoueannee']) && $_POST['ajoueannee']==true){
      echo '
      		<div> En etes vous sur?</div>
      		<form method="post" action="index.php">
      		<input type="hidden" name="page" value="ModifierEtudiant" >
      		<div><input  type="radio" value="oui" name="valideajoue">oui</input>
      		<input type="radio" value="non" name="valideajoue" checked>non</input></div>
      		<div></br>
      		<input class="grosBoutonBleu" type="submit" ></div>
      		</form>
    		
    	';

    	}

    	if(isset($_POST['valideajoue']) && $_POST['valideajoue']=="oui"){
    		  $query = "UPDATE etudiant SET promo = promo+1";
    		  $result= mysqli_query($conn,$query);
    		  $query = "DELETE FROM presence WHERE 1";
    		  $result= mysqli_query($conn,$query);

    		  $query = "SELECT * FROM etudiant";
    		  $res= mysqli_query($conn,$query);

    		  while($rowa = mysqli_fetch_assoc($res)){
    		  	$_POST['Nom']=$rowa['nomEtu'];
    		  	$_POST['Prenom']=$rowa['prenomEtu'];
				$_POST['promotion']=$rowa['promo'];
				$_POST['groupetd']=$rowa['groupetd'];
				$_POST['groupetp']=$rowa['groupetp'];


    		  $query='SELECT cours.idcours, etudiant.idetudiant FROM cours
        				INNER JOIN module ON module.idmodule = cours.Idmodule
        				INNER JOIN etudiant ON etudiant.filiere = module.filiere
				        WHERE nomEtu="'.$_POST['Nom'].'" AND prenomEtu="'.$_POST['Prenom'].'" AND
				        module.promo='.$_POST['promotion'].' AND
				        (cours.groupetd="'.$_POST['groupetd'].'" OR cours.groupetd IS NULL) AND
				        (cours.groupetp='.$_POST['groupetp'].' OR cours.groupetp IS NULL);';
				        echo $query.'<br>';
				        echo 'test<br>';
				        $result = mysqli_query($conn, $query);
				        //Enfin, pour chaque ID cours obtenu, on va créer un élément de la table "Présence" qui va relier l'ID du cours à l'ID du nouvel étudiant ajouté.
				        echo print_r($result);
				        
				          while($row = mysqli_fetch_assoc($result)) {
				          	echo print_r($row);
				            $resulty = mysqli_query($conn, 'INSERT INTO presence(idetudiant,idcours) VALUES ('.$row['idetudiant'].','.$row['idcours'].')');

          }
        
    }
    		  echo "une année a été ajouter a tout les eleves";

    	}

echo "<div> <h1> Modification d'un étudiant </h1></div>";

echo "<div>Etudiant a modfier</div>";

      echo '<form method="post" action="index.php">
      <input type="hidden" name="page" value="ModifierEtudiant" >
      <input type="hidden" name="formulaire-soumis" value="true" >

      <div class="entry-stylized-form"><div class="title-stylized-form">Nom </div>  <input type="text" name="Nom" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Prenom</div> <input type="text" name="Prenom" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Promotion</div> <input type="text" name="promotion" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Filiere</div> <select name="filiere" id="filiere">
      <option value="IAI">IAI</option>
      <option value="MM">MM</option>
      <option value="EBE">EBE</option>
      <option value="IDU">IDU</option>
      <option value="ITII-CM">ITII-CM</option>
      <option value="ITII-MP">ITII-MP</option>
      </select></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Groupe TD </div><input type="text" name="groupetd" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Groupe TP </div><input type="text" name="groupetp" /></div>
      </select>


      <div>Modfier par :</br></div></br>

 	  <div class="entry-stylized-form"><div class="title-stylized-form">Nom </div>  <input type="text" name="newNom" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Prenom</div> <input type="text" name="newPrenom" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Promotion</div> <input type="text" name="newpromotion" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Filiere</div> <select name="newfiliere" id="newfiliere">
      <option value="IAI">IAI</option>
      <option value="MM">MM</option>
      <option value="EBE">EBE</option>
      <option value="IDU">IDU</option>
      <option value="ITII-CM">ITII-CM</option>
      <option value="ITII-MP">ITII-MP</option>
      </select></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Groupe TD </div><input type="text" name="newgroupetd" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Groupe TP </div><input type="text" name="newgroupetp" /></div>
      </select><div></br></div>





      <div class="button-stylized-form"><input type="submit" value="Enregistrer"/></div>
      </form>';
		if(isset($_POST['formulaire-soumis'])){
      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      echo 'connected succesfully<br>';

      //Récuperation des valeurs du post
      $nomEtu=$_POST['Nom'];
      $nomPre=$_POST['Prenom'];
      $promo=$_POST['promotion'];
      $filiere=$_POST['filiere'];
      $groupetd=$_POST['groupetd'];
      $groupetp=$_POST['groupetp'];
      $newnomEtu=$_POST['newNom'];
      $newnomPre=$_POST['newPrenom'];
      $newpromo=$_POST['newpromotion'];
      $newfiliere=$_POST['newfiliere'];
      $newgroupetd=$_POST['newgroupetd'];
      $newgroupetp=$_POST['newgroupetp'];


       $query='UPDATE etudiant SET nomEtu="'.$newnomEtu.'",prenomEtu="'.$newnomPre.'",promo="'.$newpromo.'",filiere="'.$newfiliere.'",groupetd="'.$newgroupetd.'",groupetp="'.$newgroupetp.'" WHERE nomEtu="'.$nomEtu.'" AND prenomEtu="'.$_POST['Prenom'].'" AND promo="'.$_POST['promotion'].'" AND filiere="'.$_POST['filiere'].'" AND groupetd="'.$_POST['groupetd'].'" AND groupetp="'.$_POST['groupetp'].'"';
		$result = mysqli_query($conn, $query);


 		$queryy = 'SELECT idetudiant FROM etudiant WHERE nomEtu="'.$newnomEtu.'" AND prenomEtu="'.$newnomPre.'" AND promo="'.$newpromo.'" AND filiere="'.$newfiliere.'" AND groupetd="'.$newgroupetd.'" AND groupetp="'.$newgroupetp.'"';
 		echo $queryy;
		$resulta = mysqli_query($conn, $queryy);


		while($row = mysqli_fetch_assoc($resulta)) {
			$querry='DELETE FROM presence WHERE idetudiant="'.$row['idetudiant'].'"';
			$resulty = mysqli_query($conn, $querry);
		}


		$query='SELECT cours.idcours, etudiant.idetudiant FROM cours
        INNER JOIN module ON module.idmodule = cours.Idmodule
        INNER JOIN etudiant ON etudiant.filiere = module.filiere
        WHERE nomEtu="'.$_POST['newNom'].'" AND prenomEtu="'.$_POST['newPrenom'].'" AND
        module.promo='.$_POST['newpromotion'].' AND
        (cours.groupetd="'.$_POST['newgroupetd'].'" OR cours.groupetd IS NULL) AND
        (cours.groupetp='.$_POST['newgroupetp'].' OR cours.groupetp IS NULL);';
        echo $query.'<br>';
        echo 'test<br>';
        $result = mysqli_query($conn, $query);
        //Enfin, pour chaque ID cours obtenu, on va créer un élément de la table "Présence" qui va relier l'ID du cours à l'ID du nouvel étudiant ajouté.
        echo print_r($result);
        if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
            $resulty = mysqli_query($conn, 'INSERT INTO presence(idetudiant,idcours) VALUES ('.$row['idetudiant'].','.$row['idcours'].')');
            if($resulty==1){
              echo '<div class="reponse-bdd">lien crée : '.$row['idetudiant'].','.$row['idcours'].'<div><br>';
            }
          }
        }

	}

?>