<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>SaisieCours</title>
    </head>

    <body>
    <?php 
    require_once('Config.php');
    if ($_POST['choix']==='cours')
        header("Location: SaisieListeCours.html");
    if ($_POST['choix']==='etudiants')
        header("Location: SaisieListeEtudiant.html");?>
        
    
    </body>
</html>