<?php
require_once('Config.php');
?>
<!DOCTYPE html>


<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/AjouterEtudiant.css" />
  <title>SaisieEtudiant</title>
</head>


<div class="contenu-centre">
  <div id="content" class="stylized-form">
    <?php

    if(isset($_POST['formulaire-soumis'])){
      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      echo 'connected succesfully<br>';

      //Récuperation des valeurs du post
      $nomEns=$_POST['Nom'];
      $prenomEns=$_POST['Prenom'];
      $type=$_POST['type'];
      $service=$_POST['service'];
      $heuresup=$_POST['heuresup'];


      //Vérification de la validité des informations
      $requeteValide=true;

      //champs vides
      if($nomEns=='' || $prenomEns=='' || $type=='' || $service==''|| $heuresup=='' ){$requeteValide=false;$erreur.='champs vide.s ';};

      if($requeteValide){
        //Requete SQL pour ajouter l'étudiant à la base de données (selon les informations du formulaire)
        $query='INSERT INTO '.$bdName.'.enseignant (nomEns,prenomEns,type,service,heuresup) VALUES ("'.$nomEns.'","'.$prenomEns.'","'.$type.'","'.$service.'","'.$heuresup.'")';
        $result = mysqli_query($conn, $query);
        if($result==1){
          echo '<div class="reponse-bdd">L'."'".'enseignant à bien été ajouté à la BDD</div><br>';
        }
        //Maintenant que nous avons créé l'étudiant on join l'étudiant avec les cours et les modules sur la  filiere avec pour condition d'avoir des groupe de TD/TP et l'année
        //on recupere les id des cours à relier

        //On pourrait généraliser l'opération en retirant les conditions nomEtu et prenomEtu = mais dans ce cas particulier on fais au plus efficace et précis
    }
    }else{

      echo "<h1> Saisie de l'étudiant à ajouter</h1>";
      echo '<form method="post" action="index.php">
      <input type="hidden" name="page" value="AjouterEnseignant" >
      <input type="hidden" name="formulaire-soumis" value="true" >

      <div class="entry-stylized-form"><div class="title-stylized-form">Nom </div>  <input type="text" name="Nom" /></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Prenom</div> <input type="text" name="Prenom" /></div>


    <div class="entry-stylized-form"><div class="title-stylized-form">Type</div> <select name="type" id="type">
      <option value="permanant">permanant</option>
      <option value="vacataire">vacataire</option>
    </select></div>

      <div class="entry-stylized-form"><div class="title-stylized-form">Service</div> <input type="text" name="service" /></div>      
    <div class="entry-stylized-form"><div class="title-stylized-form">Heure supplémentaire</div> <select name="heuresup" id="type">
      <option value="oui">oui</option>
      <option value="non">non</option>
    </select></div>

      <div class="button-stylized-form"><input type="submit" value="Enregistrer"/></div>
      </form>';

    }
    ?>
  </div>
</div>






