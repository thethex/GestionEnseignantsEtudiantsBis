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
        header("Location: SaisieCours.html");
    if ($_POST['choix']==='module')
        header("Location: SaisieModule.php");
    if ($_POST['choix']==='enseignant')
        header("Location: SaisieEnseignant.html");
    if ($_POST['choix']==='etudiant')
        header("Location: SaisieEtudiant.html");?>

        
    
    </body>
</html>
