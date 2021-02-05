
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./style.css" />
  <link rel="stylesheet" href="./css/menuGeneral.css" />
  <title>Scan Feuille Absence</title>
</head>
<body>
  <div id="menuTopWrap">

    <div id="menuCroix" onclick="togglePanelMenu()">
      <span></span>
      <span></span>
      <span></span>
      <span></span>
    </div>


    <div id="titre-menu-top">
      <h1>
        <?php
        if(isset($_POST['page']) ){ echo $_POST['page'];}else{echo acceuil;};
        ?>
      </h1>
    </div>

  </div>
  <div id="menuPanelWrap">

    <div class="section-menu-panel">
      <div class="section-title">Général</div>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="acceuil" >
      </form>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="AjouterEtudiant" >
      </form>
    </div>
    <div class="section-menu-panel">
      <div class="section-title">Scolarité</div>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="ScannerFeuilleAbsence" >
      </form>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="acceuil" >
      </form>
    </div>
    <div class="section-menu-panel">
      <div class="section-title">Responsables études</div>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="SuiviAbsences" >
      </form>
    </div>
    <div class="section-menu-panel">
      <div class="section-title">Étudiant</div>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="ScannerFeuilleAbsence" >
      </form>
    </div>
    <div class="fondGlass">
    </div>
  </div>
</div>


<div id="contentWrap" class="withoutMenu">
  <?php
  if(file_exists ('./'.$_POST['page'].'.php' )){
    include './'.$_POST['page'].'.php';
  }else{
    include './acceuil.html';
  }
  ?>
</div>
</div>
</body>
<footer>
  <script>
  var s = document.getElementById('menuCroix');
  s.onclick=function(){
    toggleMenu(s.className);
  }

  function toggleMenu(etat){
    /*on rajoute les animations contrairement à l'initialisation*/
    if(etat == "open"){
      s.className = ""
      document.cookie="etatMenu=fermé;";
      document.getElementById('contentWrap').className ="withoutMenu fast-ease-in-out";
      document.getElementById('menuPanelWrap').className="menuFerme fast-ease-in-out";
    } else {
      s.className = "open"
      document.cookie="etatMenu=ouvert;";
      document.getElementById('contentWrap').className ="withMenu fast-ease-in-out";
      document.getElementById('menuPanelWrap').className="menuOuvert fast-ease-in-out";
    }
  }
  /*INITIALISATION*/
  var cookieValue = document.cookie.split(';').find(row => row.startsWith('etatMenu')).split('=')[1];
  if(cookieValue=="ouvert"){  document.getElementById('contentWrap').className ="withMenu";document.getElementById('menuPanelWrap').className="menuOuvert";document.getElementById('menuCroix').className="open";
}else{document.getElementById('contentWrap').className ="withoutMenu";document.getElementById('menuPanelWrap').className="menuFerme";document.getElementById('menuCroix').className="";};
  </script>


</footer>
</html>
