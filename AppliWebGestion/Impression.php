<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>Impression feuille d'absence</title>
    </head>

    <body>
        <p>
        <br/>
        <h4>

        
    <?php
    
    echo('<br/>');
    require_once('Config.php');
    error_reporting(E_ERROR | E_WARNING | E_PARSE);

    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //print_r($_POST);

    $dataetudiants = $bdd->query('SELECT nom, prenom FROM '.$bdName.'.etudiant WHERE (filiere="'.$_POST['filiere'].'") and (promo='.$_POST['promo'].')');
    $etudiants = $dataetudiants->fetchall();

    //print_r($etudiants);

    require_once('lib/PhpSpreadsheet-master/vendor/autoload.php');

    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Reader\Xls;
    use PhpOffice\PhpSpreadsheet\Style\Style;
    use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
    
    $inputFileType = IOFactory::identify($NomFichierModele);
    $reader = IOFactory::createReader($inputFileType);
    $spreadsheet = $reader->load($NomFichierModele);
    $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

    $loadedSheetNames = $spreadsheet->getSheetNames();

    $spreadsheet->setActiveSheetIndexByName($loadedSheetNames[0]);

    $spreadsheet->getActiveSheet()->setCellValue($CellDate, $_POST['Date']);

    $idlescours='';//Variable pour stocker l'ensemble des ID des cours, dans l'ordre, pour l'associer à la fiche d'absence dans la table fichesabsences.
    for($i=1; $i<=$_POST['ncours'];$i++){
        if($i==1){
            //Position des cellules à remplacer
            //$CellFiliere = $CellFiliereCours1;
            $CellAnnee = $CellAnneeCours1;
            $CellModule = $CellModuleCours1;
            $CellHeure = $CellHeureCours1;
            $CellType = $CellTypeCours1;
            $CellEnseignant = $CellEnseignantCours1;
            //Position des Informations relatifs a chaque cours dans le $_POST
            $type='type1';
            $module='module1';
            $enseignant='enseignant1';
            $heure='heure1';
            $idlescours=$idlescours.$_POST['idcours1'].';'; //Ajout de l'id du cours à la chaîne idlescours
        }
        if($i==2){
            //$CellFiliere = $CellFiliereCours2;
            $CellAnnee = $CellAnneeCours2;
            $CellModule = $CellModuleCours2;
            $CellHeure = $CellHeureCours2;
            $CellType = $CellTypeCours2;
            $CellEnseignant = $CellEnseignantCours2;
            $type='type2';
            $module='module2';
            $enseignant='enseignant2';
            $heure='heure2';
            $idlescours=$idlescours.$_POST['idcours2'].';';
        }
        if($i==3){
            //$CellFiliere = $CellFiliereCours3;
            $CellAnnee = $CellAnneeCours3;
            $CellModule = $CellModuleCours3;
            $CellHeure = $CellHeureCours3;
            $CellType = $CellTypeCours3;
            $CellEnseignant = $CellEnseignantCours3;
            $type='type3';
            $module='module3';
            $enseignant='enseignant3';
            $heure='heure3';
            $idlescours=$idlescours.$_POST['idcours3'].';';
        }
        if($i==4){
            //$CellFiliere = $CellFiliereCours4;
            $CellAnnee = $CellAnneeCours4;
            $CellModule = $CellModuleCours4;
            $CellHeure = $CellHeureCours4;
            $CellType = $CellTypeCours4;
            $CellEnseignant = $CellEnseignantCours4;
            $type='type4';
            $module='module4';
            $enseignant='enseignant4';
            $heure='heure4';
            $idlescours=$idlescours.$_POST['idcours4'].';';
        }
        if($i==5){
            //$CellFiliere = $CellFiliereCours5;
            $CellAnnee = $CellAnneeCours5;
            $CellModule = $CellModuleCours5;
            $CellHeure = $CellHeureCours5;
            $CellType = $CellTypeCours5;
            $CellEnseignant = $CellEnseignantCours5;
            $type='type5';
            $module='module5';
            $enseignant='enseignant5';
            $heure='heure5';
            $idlescours=$idlescours.$_POST['idcours5'].';';
        }
        
        
        //Ces lignes permettent de positionner l'information de chaque cours à sa cellule dédiée
        $spreadsheet->getActiveSheet()->setCellValue($CellAnnee, $_POST['filiere'].'-'.$_POST['annee']);
        $spreadsheet->getActiveSheet()->setCellValue($CellModule, $_POST[$module]);
        $spreadsheet->getActiveSheet()->setCellValue($CellHeure, $_POST[$heure]);
        $spreadsheet->getActiveSheet()->setCellValue($CellType, $_POST[$type]);
        $spreadsheet->getActiveSheet()->setCellValue($CellEnseignant, $_POST[$enseignant]);

        //La boucle for permet de positionner le nom et le prénom de chaque étudiant à chaque ligne, et d'optimiser au mieux l'affichage et le rendu sur la fiche d'absence.
        for($k=0;$k<count($etudiants);$k++){
            $ligne = $FirstLigneEtu1+$k;
            if($ligne>$LastLigneEtu1){
                $ligne = $FirstLigneEtu2 + ($k-$Etudiantparpage);
            }
            $spreadsheet->getActiveSheet()->setCellValue($ColNom.(string)$ligne, $etudiants[$k]['nom']);
            $spreadsheet->getActiveSheet()->setCellValue($ColPrenom.(string)$ligne, $etudiants[$k]['prenom']);
            $spreadsheet->getActiveSheet()->getStyle($ColNom.(string)$ligne.':X'.(string)$ligne)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        }
    }
    
    //Ces lignes permettent de mieux optimiser l'affichage de la fiche d'absence
    $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(0);

    for ($i=$FirstLigneEtu1;$i<=$LastLigneEtu1;$i++){
        $spreadsheet->getActiveSheet()->getRowDimension((string)$i)->setRowHeight($HauteurLigneEtudiant);
        $spreadsheet->getActiveSheet()->getStyle((string)$ColNom.$i)->getFont()->setSize($HauteurLigneEtudiant/2);
        $spreadsheet->getActiveSheet()->getStyle((string)$ColPrenom.$i)->getFont()->setSize($HauteurLigneEtudiant/2);
    }
    for ($i=$FirstLigneEtu2;$i<=50;$i++){
        $spreadsheet->getActiveSheet()->getRowDimension((string)$i)->setRowHeight($HauteurLigneEtudiant);
        $spreadsheet->getActiveSheet()->getStyle((string)$ColNom.$i)->getFont()->setSize($HauteurLigneEtudiant/2);
        $spreadsheet->getActiveSheet()->getStyle((string)$ColPrenom.$i)->getFont()->setSize($HauteurLigneEtudiant/2);
    }
    
    
    $ladate = $_POST['Date']." 00:00:00";

    //On créé une fiche d'absence dans la table "fichesabsences" afin de pouvoir extraire son identifiant pour l'afficher sur la fiche
    $bdd->query('INSERT INTO '.$bdName.'.fichesabsences (dateday,filiere,promo,iddescours) VALUES ('.'"'.$ladate.'"'.','.'"'.$_POST['filiere'].'"'.','."'".$_POST['promo']."'".','.'"'.$idlescours.'"'.')');

    $bddidfiche = $bdd->query('SELECT MAX(idfiche) FROM '.$bdName.'.fichesabsences');
    $idfiche = $bddidfiche->fetchall();
    $iddelafiche = $idfiche[0]['MAX(idfiche)'];

    $spreadsheet->getActiveSheet()->setCellValue($CellIDFICHE, 'ID : '.$iddelafiche);


    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf($spreadsheet);
    $pdf_path = 'Fiche a imprimer/Ficheaimprimer.pdf';
    
    $writer->save($pdf_path);

    //On associe les cours concernés à la fiche d'absence dans la base de données
    for($i=1; $i<=$_POST['ncours'];$i++){
        $stringcours = 'idcours'.(string)$i;
        $bdd->query('UPDATE cours SET idfiche = '.$iddelafiche.' WHERE idcours='.$_POST[$stringcours].'');
    }


    echo('Fiche prête pour impression');
    echo('<br/>');
    echo('<a href="Fiche a imprimer/Ficheaimprimer.pdf">Afficher le PDF pour impression</a>')
    
    ?>
    </h4>
    </p>
    </body>
</html>