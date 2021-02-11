<?php
require_once('Config.php');
?>
<!DOCTYPE html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/AjouterEtudiant.css" />
  <title>SaisieCours</title>
</head>
<div class="contenu-centre">
  <div id="content" class="stylized-form">
    <?php

    if(isset($_POST['formulaire-soumis'])){
      print_r($_POST);
      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      echo 'connected succesfully<br>';

      //Récuperation des valeurs du post
      $type=$_POST["type"];
      $groupetd=$_POST["groupetd"];
      $groupetp=$_POST["groupetp"];
      $dateCours=$_POST['dateCours'];
      $dureeCours=$_POST['dureeCours'];
      $heure=$_POST["heureCours"];


      $infosModule=json_decode($_POST["infosModule"],true);
      $idModule=$infosModule['Idmodule'];
      $filiere=$infosModule['filiere'];

      $idEnseignant=$_POST["idEns"];

      $dateCoursComplete=$dateCours.' '.$heure.':00';

      $tabHeure=explode(':',$dureeCours);


      $duree=((float) $tabHeure[0])+(((float) $tabHeure[1])/60);

      //Vérification de la validité des informations
      $requeteValide=true;


      //champs vides
      if(false){$requeteValide=false;$erreur.='champs vide.s ';};



      if($requeteValide){

        //Requete SQL pour ajouter l'étudiant à la base de données (selon les informations du formulaire)
        $query='INSERT INTO '.$bdName.'.cours (type,datecours,duree,idmodule,idenseignant,groupetd,groupetp) VALUES ("'.$type.'","'.$dateCoursComplete.'","'.$duree.'","'.$idModule.'","'.$idEnseignant.'","'.$groupetd.'","'.$groupetp.'")';
        $result = mysqli_query($conn, $query);
        if($result==1){
          echo '<div class="reponse-bdd">Le cours à bien été ajouté à la BDD<div><br>';
        }
        //Maintenant que nous avons créé l'étudiant on join l'étudiant avec les cours et les modules sur la  filiere avec pour condition d'avoir des groupe de TD/TP et l'année
        //on recupere les id des cours à relier

        //On pourrait généraliser l'opération en retirant les conditions nomEtu et prenomEtu = mais dans ce cas particulier on fais au plus efficace et précis

        //en enlevant les deux premières lignes de conditions cette requete génere toute les lignes de presence.
        $query='SELECT cours.idcours, etudiant.idetudiant FROM cours
        INNER JOIN module ON module.idmodule = cours.Idmodule
        INNER JOIN etudiant ON etudiant.filiere = module.filiere
        WHERE module.filiere="'.$filiere.'" AND
        cours.type="'.$type.'" AND cours.datecours="'.$dateCoursComplete.'"  AND cours.duree='.$duree.' AND cours.idmodule='.$idModule.' AND cours.idenseignant='.$idEnseignant.' AND cours.groupetd="'.$groupetd.'" AND cours.groupetp="'.$groupetp.'" AND
        ( etudiant.groupetd=cours.groupetd OR cours.groupetd IS NULL OR cours.groupetd="") AND
        ( etudiant.groupetp=cours.groupetp OR cours.groupetp IS NULL OR cours.groupetp="")';
        echo $query.'<br>';
        echo '<br>';
        $result = mysqli_query($conn, $query);
        //Enfin, pour chaque ID cours obtenu, on va créer un élément de la table "Présence" qui va relier l'ID du cours à l'ID du nouvel étudiant ajouté.
        echo print_r($result);
        if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
            $resultj = mysqli_query($conn, 'INSERT INTO presence(idetudiant,idcours) VALUES ('.$row['idetudiant'].','.$row['idcours'].')');
            if($resultj==1){
              echo '<div class="reponse-bdd">lien crée : '.$row['idetudiant'].','.$row['idcours'].'<div><br>';
            }
          }
        }


      $conn -> close();

      }else{
        echo 'Erreur : '.$erreur;
      }
    }else{
      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }
      echo 'connected succesfully<br>';

      echo "<h1> Saisie du cours à ajouter</h1>";
      echo '<form method="post" action="index.php">
      <input type="hidden" name="page" value="AjouterCours" >
      <input type="hidden" name="formulaire-soumis" value="true" >



      <div class="entry-stylized-form"><div class="title-stylized-form">Nom Prenom enseignant</div> <select name="idEns" id="idEns">';
      $query='SELECT idenseignant,nomEns, prenomEns FROM enseignant';
      $result = mysqli_query($conn, $query);
      while($row = mysqli_fetch_assoc($result)){
        echo '<option value='.$row["idenseignant"].'>'.$row["nomEns"]."    ".$row["prenomEns"].'</option>';
      }
      echo'</select></div>

      <div class="entry-stylized-form"><div class="title-stylized-form">Nom Prenom enseignant</div> <select name="infosModule" id="infosModule">';
      $query='SELECT Idmodule,nomMod,filiere FROM module';
      $result = mysqli_query($conn, $query);
      while($row = mysqli_fetch_assoc($result)){
        echo '<option value={"Idmodule":'.$row["Idmodule"].',"filiere":"'.$row["filiere"].'"}>'.$row["nomMod"].'</option>';
      }
      echo'</select></div>

      <div class="entry-stylized-form"><div class="title-stylized-form">Type de cours</div><select name="type" >
      <option selected value="CM">CM</option>
      <option value="TD">TD</option>
      <option value="TP">TP</option>
      </select></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Groupe TD </div><select name="groupetd" >
      <option selected value=""></option>
      <option value="A">A</option>
      <option value="B">B</option>
      </select></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Groupe TP </div><select name="groupetp" >
      <option selected value=""></option>
      <option value="1">1</option>
      <option value="2">2</option>
      </select></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Date</div><input type="date" name="dateCours"  value="2017-06-01"/></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Heure début</div><input type="time" name="heureCours"  value="08:30"/></div>
      <div class="entry-stylized-form"><div class="title-stylized-form">Durée</div><input type="time" name="dureeCours"  value="01:30"/></div>
      <div class="button-stylized-form"><input type="submit" value="Enregistrer"/></div>
      </form>';

      $conn -> close();
    }


    ?>
  </div>
</div>
