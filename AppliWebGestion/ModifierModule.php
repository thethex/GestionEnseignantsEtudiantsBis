<?php
   require_once('Config.php');
?>


<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/AjouterEtudiant.css" />
    <title>ModifierUnModule</title>
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
      $Idmodule=$_POST['Idmodule'];
      $semestre=$_POST['semestre'];
      $heurescm=$_POST['heurescm'];
      $heurestd=$_POST['heurestd'];
      $heurestp=$_POST['heurestp'];
      $idEns=$_POST['idEns'];
      $filiere=$_POST['filiere'];
      $promo=$_POST['promo'];

      //Vérification de la validité des informations
      $requeteValide=true;

      //champs vides
      if($Idmodule=='' || $semestre=='' || $heurescm=='' || $heurestd==''|| $heurestp=='' || $idEns==''|| $filiere==''|| $promo==''){$requeteValide=false;$erreur.='champs vide.s ';};

      if($requeteValide){
        //Requete SQL pour ajouter l'étudiant à la base de données (selon les informations du formulaire)
        $query="UPDATE module SET semestre='$semestre', heurescm='$heurescm', heurestd='$heurestd', heurestp='$heurestp', idenseignant='$idEns', filiere='$filiere', promo='$promo' WHERE Idmodule='$Idmodule'";
        $result = mysqli_query($conn, $query);
        echo $query;
        if($result==1){
          echo '<div class="reponse-bdd">Le module à bien été ajouté à la BDD<div><br>';
        }
        //Maintenant que nous avons créé l'étudiant on join l'étudiant avec les cours et les modules sur la  filiere avec pour condition d'avoir des groupe de TD/TP et l'année
        //on recupere les id des cours à relier

        //On pourrait généraliser l'opération en retirant les conditions nomEtu et prenomEtu = mais dans ce cas particulier on fais au plus efficace et précis


        
    }else{
        echo 'Erreur : '.$erreur;
    }
    }else{
        $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

        

        echo "<h1> Saisie du module à modifier</h1>";
        echo '<form method="post" action="index.php">
        <input type="hidden" name="page" value="ModifierModule" >
        <input type="hidden" name="formulaire-soumis" value="true" >

        <div class="entry-stylized-form"><div class="title-stylized-form">Nom Module</div>';

        echo '<select name="Idmodule" id="Idmodule">';
        $query='SELECT Idmodule,nomMod FROM module';
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)){
            echo '<option value='.$row["Idmodule"].'>'.$row["nomMod"].'</option>';
        }

        echo '</select></div>';

        echo '

            <div class="entry-stylized-form"><div class="title-stylized-form">Semestre</div> <input type="text" name="semestre"/></div>
            <div class="entry-stylized-form"><div class="title-stylized-form">Nombre d'."'".'heures cm</div> <input type="text" name="heurescm" /></div>
            <div class="entry-stylized-form"><div class="title-stylized-form">Nombre d'."'".'heures td</div> <input type="text" name="heurestd" /></div>

            <div class="entry-stylized-form"><div class="title-stylized-form">Nombre d'."'".'heures tp </div><input type="text" name="heurestp" /></div>
            <div class="entry-stylized-form"><div class="title-stylized-form">Filiere</div> 
                <select name="filiere" id="filiere">
                    <option value="IAI">IAI</option>
                    <option value="MM">MM</option>
                    <option value="EBE">EBE</option>
                    <option value="IDU">IDU</option>
                    <option value="ITII-CM">ITII-CM</option>
                    <option value="ITII-MP">ITII-MP</option>
                </select>
            </div>

            <div class="entry-stylized-form"><div class="title-stylized-form">Promo </div><input type="text" name="promo" /></div>';


        

        echo '<div class="entry-stylized-form"><div class="title-stylized-form">Nom Prenom Enseignant</div>';

        echo '<select name="idEns" id="idEns">';
        $query='SELECT idenseignant,nomEns, prenomEns FROM enseignant';
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_assoc($result)){
            echo '<option value='.$row["idenseignant"].'>'.$row["nomEns"]."    ".$row["prenomEns"].'</option>';
        }

        echo '</select></div>';

        echo '<div class="button-stylized-form"><input type="submit" value="Enregistrer"/></div></form>';
    }
    ?>
  </div>
</div>

