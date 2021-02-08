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
      $nomEtu=$_POST['Nom'];
      $nomPre=$_POST['Prenom'];
      $promo=$_POST['promotion'];
      $filiere=$_POST['filiere'];
      $groupetd=$_POST['groupetd'];
      $groupetp=$_POST['groupetp'];

      //Vérification de la validité des informations
      $requeteValide=true;

      //champs vides
      if($nomEtu=='' || $nomPre=='' || $promo=='' || $filiere==''|| $groupetd=='' || $groupetp==''){$requeteValide=false;$erreur.='champs vide.s ';};

      if($requeteValide){
        //Requete SQL pour ajouter l'étudiant à la base de données (selon les informations du formulaire)
        $query='INSERT INTO '.$bdName.'.etudiant (nomEtu,prenomEtu,promo,filiere,groupetd,groupetp) VALUES ("'.$nomEtu.'","'.$nomPre.'","'.$promo.'","'.$filiere.'","'.$groupetd.'","'.$groupetp.'")';
        $result = mysqli_query($conn, $query);
        if($result==1){
          echo '<div class="reponse-bdd">L'."'".'étudiant à bien été ajouté à la BDD<div><br>';
        }
        //Maintenant que nous avons créé l'étudiant on join l'étudiant avec les cours et les modules sur la  filiere avec pour condition d'avoir des groupe de TD/TP et l'année
        //on recupere les id des cours à relier

        //On pourrait généraliser l'opération en retirant les conditions nomEtu et prenomEtu = mais dans ce cas particulier on fais au plus efficace et précis


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
        if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
            $result = mysqli_query($conn, 'INSERT INTO presence(idetudiant,idcours) VALUES ('.$row['idetudiant'].','.$row['idcours'].')');
            if($result==1){
              echo '<div class="reponse-bdd">lien crée : '.$row['idetudiant'].','.$row['idcours'].'<div><br>';
            }
          }
        }
      }else{
        echo 'Erreur : '.$erreur;
      }
    }else{

      echo "<h1> Saisie de l'étudiant à ajouter</h1>";
      echo '<form method="post" action="index.php">
      <input type="hidden" name="page" value="AjouterEtudiant" >
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
      <div class="button-stylized-form"><input type="submit" value="Enregistrer"/></div>
      </form>';

    }
    ?>
  </div>
</div>
