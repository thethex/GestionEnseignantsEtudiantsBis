
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
    <div class="theme-switch-wrapper">
         <label class="theme-switch" for="checkbox">
            <input type="checkbox" id="checkbox" />
              <div class="slider round"></div>
          </label>
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
    </div>
    <div class="section-menu-panel">
      <div class="section-title">Responsables études</div>
      <form class="form-menu-panel" action="index.php" method="post">
        <input type="submit" name="page" value="SuiviAbsences" >
      </form>
    </div>
    <div class="section-menu-panel">
      <div class="section-title">Étudiant</div>
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
  /*On recreer la variable local storage si elle n'existe pas*/
  monStockage = localStorage;

  var s = document.getElementById('menuCroix');
  s.onclick=function(){
    toggleMenu(s.className);
  }

  function toggleMenu(etat){
    /*on rajoute les animations contrairement à l'initialisation*/
    if(etat == "open"){
      s.className = ""
      localStorage.setItem('etatMenu', 'fermé');
      document.getElementById('contentWrap').className ="withoutMenu fast-ease-in-out";
      document.getElementById('menuPanelWrap').className="menuFerme fast-ease-in-out";
    } else {
      s.className = "open"
      localStorage.setItem('etatMenu', 'ouvert');
      document.getElementById('contentWrap').className ="withMenu fast-ease-in-out";
      document.getElementById('menuPanelWrap').className="menuOuvert fast-ease-in-out";
    }
  }

  //switch light/dark mode
  const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
  const currentTheme = localStorage.getItem('theme');

  if (currentTheme) {
      document.documentElement.setAttribute('data-theme', currentTheme);

      if (currentTheme === 'dark') {
          toggleSwitch.checked = true;
      }
  }

  function switchTheme(e) {
      if (e.target.checked) {
          document.documentElement.setAttribute('data-theme', 'dark');
          localStorage.setItem('theme', 'dark');


      }
      else {        document.documentElement.setAttribute('data-theme', 'light');
            localStorage.setItem('theme', 'light');

      }
  }

  toggleSwitch.addEventListener('change', switchTheme, false);


  /*INITIALISATION*/

  //var cookieValue = document.cookie.replace(/\s+/g, '').split(';').find(row => row.startsWith('etatMenu')).split('=')[1];
  var cookieValue = localStorage.getItem('etatMenu');
  
  if(cookieValue=="ouvert"){  document.getElementById('contentWrap').className ="withMenu";document.getElementById('menuPanelWrap').className="menuOuvert";document.getElementById('menuCroix').className="open";
  }else{document.getElementById('contentWrap').className ="withoutMenu";document.getElementById('menuPanelWrap').className="menuFerme";document.getElementById('menuCroix').className="";};

  </script>


</footer>
</html>
