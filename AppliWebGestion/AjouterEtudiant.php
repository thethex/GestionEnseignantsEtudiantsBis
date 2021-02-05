<?php
  require_once('Config.php');
?>
<!DOCTYPE html>

    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>SaisieEtudiant</title>
    </head>
    <div class="contenu-centre">
      <div id="content" class="blured-big-panel">
       <?php

       if(isset($_POST['formulaire-soumis'])){
         $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

         // Check connection
         if ($conn->connect_error) {
           die("Connection failed: " . $conn->connect_error);
         }
         echo 'connected succesfully<br>';
         //Requete SQL pour ajouter l'étudiant à la base de données (selon les informations du formulaire)
         $query='INSERT INTO '.$bdName.'.etudiant (nomEtu,prenomEtu,promo,filiere,groupetd,groupetp) VALUES ("'.$_POST['Nom'].'","'.$_POST['Prenom'].'","'.$_POST['promotion'].'","'.$_POST['filiere'].'","'.$_POST['groupetd'].'","'.$_POST['groupetp'].'")';
         $result = mysqli_query($conn, $query);
         echo $result.'<br>';
         //Maintenant que nous avons créé l'étudiant on join l'étudiant avec les cours et les modules sur la  filiere avec pour condition d'avoir des groupe de TD/TP et l'année
         //on recupere les id des cours à relier

         //On pourrait généraliser l'opération en retirant les conditions nomEtu et prenomEtu = mais dans ce cas particulier on fais au plus efficace et précis


         $query='SELECT cours.idcours etudiant.idetudiant FROM cours
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
           }
         }
        }else{

          echo '<h1> Saisie de l etudiant à ajouter</h1>
          <h4>
            <form method="post" action="index.php">
            <input type="hidden" name="page" value="AjouterEtudiant" >
            <input type="hidden" name="formulaire-soumis" value="true" >
            Nom : <input type="text" name="Nom" />
            Prenom : <input type="text" name="Prenom" />
            promotion : <input type="text" name="promotion" />
            filiere  : <select name="filiere" id="filiere">
              <option value="IAI">IAI</option>
              <option value="MM">MM</option>
              <option value="EBE">EBE</option>
              <option value="IDU">IDU</option>
              <option value="ITII-CM">ITII-CM</option>
              <option value="ITII-MP">ITII-MP</option>
              </select>
            groupetd  : <input type="text" name="groupetd" />
            groupetp  : <input type="text" name="groupetp" />
            </select>
          <input type="submit" value="Enregistrer"/>
          </form>
           </h4>';

         }
         ?>
     </div>
   </div>
