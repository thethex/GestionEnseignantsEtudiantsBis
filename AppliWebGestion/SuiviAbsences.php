
<?php
   require_once('Config.php');
?>

<!DOCTYPE html>

<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/SuiviAbsences.css" />
  <title>Suivi des absences</title>
</head>

<?php
/*INITIALISATION DE LA PAGE SI PREMIER CHARGEMENT*/
if(!isset($_POST['modeAffichageAbsences'])){
  $_POST['modeAffichageAbsences']='Alertes';
}
?>
<div class="contenu-centre">
  <div id="content" class="blured-big-panel">
    <div class="conteneur-selecteur">
      <div class="selection-actuelle">
      <?php
      echo '<h2>'.$_POST['modeAffichageAbsences'].'</h2>';
      ?>
      </div>
      <div class="conteneur-choix">
        <?php
        if($_POST['modeAffichageAbsences']!=='Alertes'){
          echo '<form class="form-menu-panel" action="index.php" method="post">
          <input type="hidden" name="page" value="SuiviAbsences" >
          <input type="submit" name="modeAffichageAbsences" value="Alertes" >
          </form>';
        };
        if($_POST['modeAffichageAbsences']!=='Calendrier'){
          echo '<form class="form-menu-panel" action="index.php" method="post">
          <input type="hidden" name="page" value="SuiviAbsences" >
          <input type="submit" name="modeAffichageAbsences" value="Calendrier" >
          </form>';
        };
        if($_POST['modeAffichageAbsences']!=='Eleves'){
          echo '<form class="form-menu-panel" action="index.php" method="post">
          <input type="hidden" name="page" value="SuiviAbsences" >
          <input type="submit" name="modeAffichageAbsences" value="Eleves" >
          </form>';
        };
        if($_POST['modeAffichageAbsences']!=='Matière'){
          echo '<form class="form-menu-panel" action="index.php" method="post">
          <input type="hidden" name="page" value="SuiviAbsences" >
          <input type="submit" name="modeAffichageAbsences" value="Matière" >
          </form>';
        };
        ?>
      </div>
    </div>

    <div class="conteneur-suivi">
      <?php
      if($_POST['modeAffichageAbsences']=='Alertes'){
      /*  <div class="alerte">
        </div>*/
      }
      if($_POST['modeAffichageAbsences']=='Calendrier'){

      }
      if($_POST['modeAffichageAbsences']=='Eleves'){

        $conn = new mysqli($bdServer, $bdUser, $bdUserPasswd,$bdName);

        // Check connection
        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $query = 'SELECT * FROM PresenceEtudiants;';
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
          //INITIALISATION DES VARIABLES
          $filiere='initialisationFiliere';
          $promo='initialisationPromo';
          $etudiant='initialisationEtudiant';
          $absenteisme=0.0;
          $coursPasse=0.0;

          while($row = mysqli_fetch_assoc($result)) {

            //on re-ouvre une div de filière dès qu'elle change vu qu'on tri les lignes en fonction des filieres
            if($row['filiere']!==$filiere){
              if($filiere!=='initialisationFiliere'){
                  echo '</div></div>';
              }
              echo '<div class="etudiant-filiere"><div onclick="toggleContenu(this)" class="titre">'.$row['filiere'].'</div><div class="contenu cacher">';
              $filiere=$row['filiere'];
            }
              //on re-ouvre une div de promo dès qu'elle change vu qu'on tri ensuite les lignes en fonction des promo
              if($row['promo']!==$promo){
                if($promo!=='initialisationPromo'){

                  //on crée l'absentéisme général du dernier élève de la liste
                  echo  '<div class="indicateur-general" style="background-color:';
                    if($absenteisme==0){echo 'rgb(144,146,152)';}else if($absenteisme>0.5){echo '#e63141';}else if($absenteisme>0.2){echo '#efb64d';}else{echo '#4e9bef';};
                  echo '">'.$absenteisme.'</div>';

                  echo '</div></div>';
                }
                echo '<div class="etudiant-promo"><div onclick="toggleContenu(this)" class="titre">'.$row['promo'].'</div><div class="contenu cacher">';
                $promo=$row['promo'];
              }
              //on re-ouvre une div d'etudiant dès qu'elle change vu qu'on tri ensuite les lignes en fonction des noms d'etudiant
              if($row['nomEtu']!==$etudiant){
                if($etudiant!=='initialisationEtudiant'){
                  /*  BUG */
                  /*ATTENTION ON A 4 DIV POUR FERMER LE DERNIER MODULE, SI PAS DE MODULE POTENTIEL BUG IL FAUDRAIT DONC S'Y PENCHER*/
                  /*  BUG */
                    echo '</div></div></div>';
                    $absenteisme=$absenteisme/$coursPasse;
                    echo  '<div class="indicateur-general" style="background-color:';
                      if($absenteisme==0){echo 'rgb(144,146,152)';}else if($absenteisme>0.5){echo '#e63141';}else if($absenteisme>0.2){echo '#efb64d';}else{echo '#4e9bef';};
                    echo '">'.$absenteisme.'</div>';
                    echo '</div>';
                    $absenteisme=0.0;
                    $coursPasse=0.0;
                }
                echo  '<div class="etudiant-etudiant"><div onclick="toggleContenu(this)" class="titre">'.$row['nomEtu'].' - '.$row['prenomEtu'].'</div><div class="contenu cacher">';
                $etudiant=$row['nomEtu'];
                //on reinitialise les modules pour chaques etudiants
                $module='initialisationModule';
              }
                //on re-ouvre une div de module pour chaque nouveau module
                if($row['nomMod']!==$module){
                  if($module!=='initialisationModule'){
                      echo  '</div></div>';
                  }
                  echo  '<div class="etudiant-module"><div class="titre-mod">'.$row['nomMod'].'</div><div class="contenu">';
                  $module=$row['nomMod'];
                }
                  //on re-ouvre une div de cours pour chaque cours du module chaque cours est unique donc pas d'initialisation ou autres

                  echo '<div class="block-cours" style="flex :'.$row['duree'].' 1 100%;background-color:';
                  if($row['presence']==NULL){echo 'rgb(144,146,152)';}else{ $coursPasse+=1; ; if($row['presence']=='present'){echo '#4e9bef';}else if($row['justificatif']=='OUI'){echo '#efb64d';$absenteisme+=0.5;}else{echo '#e63141';$absenteisme+=1;}};
                  echo  ';">';
                  echo  '<div class="info-cours">'.$row['nomMod'].' '.$row['type'].' '.$row['groupetd'].'-'.$row['groupetp'].'<br>'.$row['datecours'].'<br>'.$row['nomEns'].' '.$row['prenomEns'].'</div>';
                  echo '</div>';


          }
          //on crée l'absentéisme général du dernier élève de la liste et on ferme la cascade de div du dernier contenu d'eleve créé
          echo  '</div></div></div><div class="indicateur-general" style="background-color:';
            if($absenteisme==0){echo 'rgb(144,146,152)';}else if($absenteisme>0.5){echo '#e63141';}else if($absenteisme>0.2){echo '#efb64d';}else{echo '#4e9bef';};
          echo '">'.$absenteisme.'</div>';

          echo '</div></div>';
        }

        mysqli_close($conn);
      }
      if($_POST['modeAffichageAbsences']=='Matière'){
      /*  <div class="matiere-module">
          <div class="matiere-cours">
            <div class="matiere-eleve">
              <div class="resumer-presence">
              </div>
            </div>
          </div>
        </div>*/
      }
      ?>
      <script>
        function toggleContenu(element){

            var contenu =  element.nextElementSibling;

            if(contenu!=null & contenu.classList.contains("cacher")){
              contenu.classList.remove("cacher");
            }else{
              contenu.classList.add("cacher");

            }
        }
      </script>
    </div>
  </div>
</div>
