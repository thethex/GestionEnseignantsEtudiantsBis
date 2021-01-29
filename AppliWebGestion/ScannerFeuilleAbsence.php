
<?php
   $temp = tmpfile();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="./style.css" />
          <link rel="stylesheet" href="./css/ScannerFeuille.css" />
        <title>Scan Feuille Absence</title>
    </head>

    <body>
      <div class="contenu-centre">
      <div  id="effectuer-scan">

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

      <div  id="recuperer-scan">

        <h1>Méthode de recuperation de scan</h1>




        <form id="formulaire-upload-scan" action="ScannerFeuilleAbsence.php" method="post" enctype="multipart/form-data">
          <div id="conteneur-formulaire-upload-scan">
            <input type="file" class="input-file" name="fileToUpload" id="fileToUpload">
            <label id="label-fileToUpload" for="fileToUpload">Choose a file</label>
            <input class="bouton-formulaire-upload" type="submit" value="Valider" name="submit">
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
        if(!isset($_POST['target_file'])){
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
              echo "Erreur pendant la récupération du fichier";
            }
          }

        }else{
          $target_file = $_POST["target_file"];
        }

        ?>



      </div>

      <div id="voir-scan">
        <?php


        //si un scan a été envoyer on peu l'afficher pour vérification
        echo $target_file;
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

                      ini_set('display_errors',1);
                      error_reporting(E_ALL);
                      require_once('Config.php');


                      $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

                      // Check connection
                      if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                      }
                      echo "Connected successfully<br>";

                      $query = 'SELECT * FROM cours JOIN module ON cours.idModule=module.IdModule  WHERE idcours=2';
                      $result = mysqli_query($conn, $query);


                      //fonctionnel au dernières nouvelles check plus bas
                      $row = mysqli_fetch_assoc($result);

                      $filiere=$row["filiere"];
                      $promo=$row["promo"];
                      $groupetd=$row["groupetd"];
                      $groupetp=$row["groupetp"];

                      //on recupere les etudiants qui appartiennent à la promo et si besoin le groupe de TD/TP
                      $query = 'SELECT * FROM etudiant WHERE promo='.$promo.' AND filiere="'.$filiere.'"';

                      if($groupetd!==NULL){
                          $query=$query.$groupetd;
                      }
                      if($groupetp!==NULL){
                          $query=$query.$groupetp;
                      }


                      $result = mysqli_query($conn, $query);

                      //si on a demandé à traiter l'image on lance le script python associé

                      //echo print_r($_POST);
                      if(isset($_POST['scanTraite'])){

                          $command = 'python3 /home/thethex/Documents/www/AppliWebGestion.com/py/test2.py ';

                          exec($command, $out, $status);

                          print_r($out[0]);
                          $array = json_decode($out[0], true);

                      }

                      if (mysqli_num_rows($result) > 0) {
                        echo '<table style="width:100%"; border="1">
                        <thead>
                        <tr>
                          <th>Cours : '.$row["nom"].'</th>
                          <th>idCours : '.$row["idcours"].'</th>

                        </tr>
                        <tr>
                          <th></th>
                          <th>Nom</th>
                          <th>Prénom</th>
                        <th>Présence</th>
                        </tr>
                        </thead>';
                        $i=1;
                         while($row = mysqli_fetch_assoc($result)) {
                            echo '<tr>';

                            echo '<td  id="'.$row["idetudiant"].'">' . $i ."</td>";

                            echo '<td>' . $row["prenom"] ."</td>";

                            echo '<td>' . $row["nom"] ."</td>";

                            echo '<td ><div  class="ediTable" onclick="editeAbsence(this);">' ."$array[$i]" ."</div></td>";

                            echo "</tr>";
                            $i++;
                         }
                        echo "</table>";

                         } else {
                             echo "0 results";
                         }
                         //script permettant de modifier le contenu des cases de présence
                         echo '<script>
                                      function editeAbsence(elem){
                                          var valeur = elem.innerHTML;
                                          console.log(valeur);
                                          if(valeur=="absent"){input="<select class=\" editTable-entry\"> <option value=\"present\">present</option> <option value=\"absent\" selected>absent</option></select>";};
                                          if(valeur=="present"){input="<select class=\" editTable-entry\"> <option value=\"present\" selected>present</option> <option value=\"absent\">absent</option></select>";};
                                          elem.parentElement.innerHTML=input+"<div onclick=\"valideEditeAbsence(this);\" class=\"Here-s-a-little-lesson-of-trickery\"></div>";
                                      }
                                      function valideEditeAbsence(elem){
                                          console.log("got you!");
                                          e=elem.parentElement.firstChild;
                                          elem.parentElement.innerHTML="<div  class=\"ediTable\" onclick=\"editeAbsence(this);\">"+e.options[e.selectedIndex].text+"</div></td>";
                                      }

                               </script>';
                         mysqli_close($conn);




                         echo '</div>
                         <script> function toggleJSON() {  var x = document.getElementById("JSON");var y = document.getElementById("bouton-JSON");if (x.style.display === "none") {  x.style.display = "block";  y.innerHTML="Cacher JSON";} else {x.style.display = "none";  y.innerHTML="Voir JSON";  }  } </script>
                         ';
                    };

                echo '</div>';




        echo '<div id="traitement-scan">

                  <form id="formulaire-upload-scan" action="ScannerFeuilleAbsence.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="target_file" value='.$target_file.' />
                        <input type="hidden" name="scanTraite" value="true" />
                        <input class="bouton-formulaire-upload" type="submit" value="Traiter le scan" name="submit">

                  </form>

              </div>  ';


              echo '<div id="traitement-scan">

                        <form id="formulaire-upload-scan" action="EnvoiFeuilleAbsence.php" method="post" enctype="multipart/form-data">
                              <input type="hidden" name="target_file" value='.$target_file.' />
                              <input type="hidden" name="scanTraite" value="true" />
                              <input class="bouton-formulaire-upload" type="submit" value="Envoyer la feuille dans la BDD" name="submit">

                        </form>

                    </div>  ';


        }
        ?>

      </div>



        <div>

      		<?php



        	$ip=file_get_contents("ipServ.txt");
        	$ip = substr($ip,0,-1);
        	$servername = "".$ip.":3306";
        	//$servername = "localhost";
        	$username = "invite";
        	$password = "azerty";
        	$database = "detection_visages";

        	// Create connection
        	$conn = new mysqli($servername, $username, $password,$database);

        	// Check connection
        	if ($conn->connect_error) {
        	  die("Connection failed: " . $conn->connect_error);
        	}
        	echo "Connected successfully<br>";

        	$query = 'SELECT * FROM Echanges';


        	$result = mysqli_query($conn, $query);

                 if (mysqli_num_rows($result) > 0) {
        			echo '<div style="margin: 10px auto;width:100%;
            		max-width:1200px"><table style="width:100%" border="1" class="minimalistBlack">
        			<thead>
        			<tr>
        			<th>Id</th>
        			<th>Nanotime</th>
        			<th>Etat</th>
        			<th>Nom</th>
           		<th>Distance_Euclidienne</th>
        			<th>NomImage</th>
        			<th>ImageDansDossier</th>
        			<th>Del</th>
        			</tr>
        			</thead>';
                    while($row = mysqli_fetch_assoc($result)) {
        				echo "<tr>";
                        echo "<td>" . $row["Id"] ."</td>";
        				echo "<td>" . $row["Nanotime"] ."</td>";
        				if($row["Etat"]=="Traite"){
        					echo "<td>" . $row["Etat"] ." vire cette majuscule Anar poopoopeepoo</td>";
        				}else{
        					echo "<td>" . $row["Etat"] ."</td>";
        				}
        				echo "<td>" . $row["Nom"] ."</td>";
        				echo "<td>" . $row["Distance_Euclidienne"] ."</td>";
        				echo "<td>" . $row["NomImage"] ."</td>";
        				echo "<td>".'<div style="max-width:300px;min-width:100px;"><img src="./'.$row["NomImage"].'" style="display:block; max-width: 100%;"/></div>'."</td>";
        				echo "<td><a href='delete.php?did=".$row['Id']."'> X </a></td>";
        				echo "</tr>";
                    }
        			 echo "</table></div>";

                 } else {
                    echo "0 results";
                 }
                 mysqli_close($conn);

        	?>

      	</div>
      </div>
    </body>
</html>
