<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once('Config.php');

if(isset($_POST['submit'])){
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
              WHERE idetudiant=".$_POST['idEtudiant'.$i]." AND idcours=".$_POST['idCours']." ;";

    $result= mysqli_query($conn,$query);
    echo $result;
  }

  //  $query = 'INSERT INTO presence ("idetudiant","idcours","presence") VALUES ('.$idEtudiant.','.$idCours.','.$presence.');';
  //$result = mysqli_query($conn, $query);
  //    echo $query;


}else {
  echo 'Le POST est vide, aucunes données n ont été reçues par cette page';
}

?>
