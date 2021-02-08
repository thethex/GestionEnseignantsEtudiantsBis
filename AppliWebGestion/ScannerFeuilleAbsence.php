
<?php
   $temp = tmpfile();
   require_once('Config.php');
    print_r($_POST);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
          <link rel="stylesheet" href="./css/ScannerFeuille.css" />
        <title>Scan Feuille Absence</title>
    </head>

    <body>
      <div class="contenu-centre">
        <div id="Methode-enregistrement-manuel" class="blured-big-panel">
        <h1>Méthode d'enregistrement manuel</h1>
        <h4>
          <br/>
              <form method="post" action="RechercheFiche.php">
              <?php
                  //Implémentation d'un code PHP ici pour préremplir le champ de texte par la date actuelle.
                //  $currentdate = date('Y-m-d');
              //echo('Sélection du jour : [YYYY-MM-DD]: <input type="text" name="Date" value="'.$currentdate.'" /> <input type="submit" value="Chercher une fiche d absence"/>');
              ?>
          </form>
        </h4>
      </div>

      <div id="Methodes-scan" class="blured-big-panel">

      <div  id="effectuer-scan">



      </div>

      <div  id="recuperer-scan">

        <h1>Méthode de recuperation de scan</h1>


          <div id="traitement-scan">
          <form id="formulaire-upload-scan" action="index.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="page" value="ScannerFeuilleAbsence" >
            <?php
                    //pour la transmition des valeur du POST actuel seul la valeur du pdf envoie est à garder car changer de cours change tout le traitement
                    //comme l'upload de l'image se fait en dessous on rajoute une rustine à la fin de la fonction d'upload (par id donc unpeu sal mais bon ...)







                    if(isset($_POST['target_file'])){
                      echo '<input type="hidden" name="target_file" value='.$_POST['target_file'].' />';
                    };
                    echo '  <input type="datetime-local" id="datecours" name="datecours" value="2021-01-18T10:15"';if(isset($_POST['datecours'])){echo $_POST['datecours'];}else{echo 'date cours';};echo '" />';

                      echo '<select name="nomMod" id="nomMod">';
                      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

                      // Check connection
                      if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                      }

                      $query = 'SELECT nomMod FROM module';
                      
                      $result = mysqli_query($conn, $query);
                      foreach($result as $key => $val){
                        foreach($val as $key2 => $res){
                          if ((isset($_POST['nomMod'])) && $_POST['nomMod']==$res){
                            echo'<option selected value="'.$res.'">'.$res.'</option>';
                          }else{

                        echo'<option value="'.$res.'">'.$res.'</option>';

                          }
                        }
                      }
                      echo '</select>';

                    
                          echo '<input  class="grosBoutonBleu bouton-formulaire-upload" type="submit" value="valider cours" name="submit">';

                ?>
                </form>
              </div>


        <form id="formulaire-upload-scan" action="index.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="page" value="ScannerFeuilleAbsence" >
          <div id="conteneur-formulaire-upload-scan">
            <?php
            //transmition des valeur du POST actuel


            if(isset($_POST['datecours'])){
                      echo '<input type="hidden" name="datecours" value='.$_POST['datecours'].' />';
                    };
            if(isset($_POST['nomMod'])){
                      echo '<input type="hidden" name="nomMod" value='.$_POST['nomMod'].' />';
                    };
              // echo '<input type="hidden" value="'.$_POST["datecours"].'" name="datecours">';
              // echo '<input type="hidden" value="'.$_POST["nomMod"].'" name="nomMod">';
            ?>
            <input type="file" class="input-file" name="fileToUpload" id="fileToUpload">
            <label id="label-fileToUpload" for="fileToUpload">Choose a file</label>
            <input class="grosBoutonBleu bouton-formulaire-upload" type="submit" value="Valider" name="submit">
            <input type="hidden" name="uploadImage" value="true" />
          </div>
        </form>

        <script>
        document.getElementById('fileToUpload').onchange = function () {
          document.getElementById("label-fileToUpload" ).innerHTML = this.value.split(/(\\|\/)/g).pop();
          document.getElementById("preview").src=this.value;

        };
        </script>

        <?php

        //si l'image n'a pas été upload on essaie de l'upload
        if(isset($_POST['uploadImage']) && $_POST['uploadImage']=='true'){
          $target_dir = "uploads/";
          $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
          $uploadOk = 1;
          $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

          //remove spaces for upcoming display
          $target_file = str_replace(' ', '', $target_file);



          // Check if file already exists
          //  if (file_exists($target_file)) {
          //    echo "Sorry, file already exists.";
          //    $uploadOk = 0;
          //  }



          // Allow certain file formats
          //  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" &&
          if( $imageFileType != "pdf" ) {
          //  echo "Sorry, only JPG, JPEG, PNG & PDF files are allowed.";
          echo "Sorry, only PDF files are allowed.";
            $uploadOk = 0;
          }

          // Check if $uploadOk is set to 0 by an error
          if ($uploadOk == 0) {
            echo "Désoler le fichier n'est pas valide";
          // if everything is ok, try to upload file
          } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
              echo "Le fichier ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). "est valide";
              $file_uploaded=true;
            } else {
              echo 'Erreur pendant la récupération du fichier';
            }
          }
          //ligne permetant de garder l'image en changeant de cours si on vient de l'upload
          echo '<script>
          document.getElementById("formulaire-upload-scan").innerHTML =  document.getElementById("formulaire-upload-scan").innerHTML + "<input type=\"hidden\" name=\"target_file\" value=\"'.$target_file.'\" />";
              </script>';
        }else{
            if (isset($_POST["target_file"])){
              $target_file = $_POST["target_file"];
            }
        }

        ?>



      </div>

      <div id="voir-scan">
        <?php


        //si un scan a été envoyer on peu l'afficher pour vérification
        if(isset($target_file) && $target_file !== "uploads/"){


          echo'<div id="boutons-affichage">
                  <button id="bouton-scan" onclick="toggleScan()">Cacher scan</button>';
                    if(isset($_POST['scanTraite'])){
                      echo '<button id="bouton-JSON" onclick="toggleJSON()">Cacher JSON</button>';
                    };
          echo '</div>';


          echo '<div id="zone-affichage">

                    <div id="scan" class="sous-zone-affichage">
                    <iframe src='.$target_file.' style="width:100%;height:700px;"></iframe>
                    </div>
                    <script> function toggleScan() {  var x = document.getElementById("scan");var y = document.getElementById("bouton-scan");if (x.style.display === "none") {  x.style.display = "block";  y.innerHTML="Cacher scan";} else {x.style.display = "none";  y.innerHTML="Voir scan";  }  } </script>
                    ';

                  //si le scan à été traité par le python on peut l'afficher
                  if(isset($_POST['scanTraite'])){
                  echo '<div id="JSON" class="sous-zone-affichage">';


                      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

                      // Check connection
                      if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                      }

                      $query = 'SELECT * FROM cours JOIN module ON cours.idModule=module.IdModule  WHERE datecours="'.str_replace('T', ' ', $_POST['datecours']).':00" AND nomMod="'.$_POST['nomMod'].'"';
                      echo $query;
                      $result = mysqli_query($conn, $query);
                      //fonctionnel au dernières nouvelles check plus bas
                      $row = mysqli_fetch_assoc($result);

                      $filiere=$row["filiere"];
                      $promo=$row["promo"];
                      $groupetd=$row["groupetd"];
                      $groupetp=$row["groupetp"];
                      $idcours=$row["idcours"];

                      //on recupere les etudiants qui appartiennent à la promo et si besoin le groupe de TD/TP
                      $query = 'SELECT * FROM etudiant WHERE promo='.$promo.' AND filiere="'.$filiere.'"';

                      if($groupetd!==NULL){
                          $query=$query.' AND groupetd="'.$groupetd.'"';
                      }
                      if($groupetp!==NULL){
                          $query=$query.' AND groupetp= '.$groupetp.'"';
                      }
                          $query=$query.'ORDER BY groupetd, groupetp, nomEtu, prenomEtu';

                      $result = mysqli_query($conn, $query);

                      //si on a demandé à traiter l'image on lance le script python associé

                      //echo print_r($_POST);
                      if(isset($_POST['scanTraite'])){

                          $command = 'py/test2Python.py ';

                          exec($command, $out, $status);
                          print_r($out[0]);
                          $out = str_replace('\'', '"', $out);
                          $array = json_decode($out[0], true);

                      }

                      if (mysqli_num_rows($result) > 0) {
                        echo '<table style="width:100%"; border="1">
                        <thead>
                        <tr>
                          <th>Cours : '.$row["nomMod"].'</th>
                          <th>datecours : '.$row["datecours"].'</th>

                        </tr>
                        <tr>
                          <th></th>
                          <th>Nom</th>
                          <th>Prénom</th>
                        <th>Présence</th>
                        </tr>
                        </thead>';


                        echo '<form class="BoutonFlottant" action="index.php" method="post">';
                        echo '<input type="hidden" name="page" value="ScannerFeuilleAbsence" >';
                        echo '<input type="hidden" name="idcours" value="'.$row["idcours"].'"></input>';
                        echo '<input type="hidden" name="envoieBDD" value=""></input>';

                        $i=1;
                         while($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';


                            echo '<td   id="'.$row["idetudiant"].'">' . $i ."</td>";
                            echo '<input type="hidden" name="idEtudiant'.$i.'" value="'.$row['idetudiant'].'"></input>';


                            echo '<td>' . $row["prenomEtu"] ."</td>";

                            echo '<td>' . $row["nomEtu"] ."</td>";
                            if(isset($array) && $array[$i]=="present"){
                                echo '<td ><select name="presence'.$i.'" class="ediTable"><option value="present" selected>present</option> <option value="absent">absent</option></select></td>';
                            }else{
                                echo '<td ><select name="presence'.$i.'" class="ediTable"><option value="present" >present</option> <option value="absent" selected>absent</option></select></td>';
                            }
                            echo "</tr>";
                            $i++;
                         }
                         if($_POST['scanTraite']=='true'){
                           echo   '<div class="BoutonFlottant"><input class="grosBoutonBleu" type="submit" value="Envoyer la feuille dans la BDD" name="submit"></div>';
                         }
                          echo '<input type="hidden" name="nbIndices" value="'.$i.'"></input>';
                        echo '</form>';

                        echo "</table>";

                         } else {
                             echo "0 results";
                         }

                         mysqli_close($conn);




                         echo '</div>
                         <script> function toggleJSON() {  var x = document.getElementById("JSON");var y = document.getElementById("bouton-JSON");if (x.style.display === "none") {  x.style.display = "block";  y.innerHTML="Cacher JSON";} else {x.style.display = "none";  y.innerHTML="Voir JSON";  }  } </script>
                         ';
                    };

                echo '</div>';

                if(isset($_POST['scanTraite'])){

                }else{$_POST['scanTraite']=false; }

                if( $_POST['scanTraite']!=='true'){
                echo '<div id="traitement-scan">

                  <form class="BoutonFlottant" action="index.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="page" value="ScannerFeuilleAbsence" >';
                        //transmition des valeur du POST actuel
                        foreach($_POST as $key => $val) {
                          if ($key!=="uploadImage"){
                            echo '<input type="hidden" value="'.$val.'" name="'.$key.'">';
                          };
                        };
                        echo  '<input type="hidden" name="target_file" value='.$target_file.' />
                        <input type="hidden" name="scanTraite" value="true" />
                        <input class="grosBoutonBleu" type="submit" value="Traiter le scan" name="submit">

                  </form>

              </div>  ';
              }


        }
        ?>

        <div>
          <?php
            ini_set('display_errors',1);
            error_reporting(E_ALL);
            require_once('Config.php');

            if(isset($_POST['envoieBDD'])){
              $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

              // Check connection
              if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
              }
              echo "Connected successfully<br>";

              echo print_r($_POST);
              $nbIndices=$_POST['nbIndices'];

              for ($i=1; $i < $nbIndices; $i++) {
                echo $i."<br>";
                echo $_POST['idEtudiant'.$i].'<br>';
                echo $_POST['presence'.$i].'<br>';

                //départ si on veut ajouter les lignes au fur et à mesure que l'on met les fiches (pas conseillé à mon avis)
                /*$query="If Not Exists(select * from tablename where code='1448523')
                        Begin
                        insert into tablename (code) values ('1448523')
                        End
                ";*/

                $query = "UPDATE presence SET presence = '".$_POST['presence'.$i]."'
                          WHERE idetudiant=".$_POST['idEtudiant'.$i]." AND idcours=".$_POST['idcours'];


                $result= mysqli_query($conn,$query);
                echo $query;
              }

              //  $query = 'INSERT INTO presence ("idetudiant","idcours","presence") VALUES ('.$idEtudiant.','.$idCours.','.$presence.');';
              //$result = mysqli_query($conn, $query);
              //    echo $query;


            }else {
              echo 'Le POST est vide, aucunes données n ont été reçues par cette page';
            }

          ?>

        </div>

      </div>


      </div>
    </body>
</html>
