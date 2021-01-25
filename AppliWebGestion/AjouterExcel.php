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
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //Dans le $_POST est transmi le fichier Excel (ou ODS) sélectionné dans le formulaire.
    //Ce fichier peut être exploitée grâce à une librairie "PhpSpreadsheet"
    //Cette librairie s'installe par l'intermédiaire de "Composer", qui va générer plusieurs fichiers .php dont "autoload.php"
    //Ce premier fichier va initier toute l'importation de la librairie, pour que les noms de domainent coïncident et qu'il soit possible d'utiliser les différentes fonctions de PhpSpreadsheet.
    require_once('Config.php');
    require_once('lib/PhpSpreadsheet-master/vendor/autoload.php');
    use PhpOffice\PhpSpreadsheet\IOFactory; //Permet de créer les entités de Reader, et objets de type spreadsheet
    use PhpOffice\PhpSpreadsheet\Reader\Xls; //Permet de lire les fichiers xls

    // Il est important, de préférence, d'utiliser la fonctioner "identify" de IOFactory : Il permet d'adapter de "reader" à tout type de fichier (tant qu'il peut le lire).
    // Notament, bien que créer un reader dédicacé pour un type de fichier spécifique est tout à fait possible, il ne permet pas toujours de lire correctement le fichier, car le format ou la version peut différer selon les logiciels lors de l'enregistrement.
    $inputFileName = $_FILES["userfile"]["tmp_name"]; //Chargement du fichier excel sélectionner sur le PC
    $inputFileType = IOFactory::identify($inputFileName); //Identification du type de fichier
    $reader = IOFactory::createReader($inputFileType); //Création du reader selon le type de fichier
    $reader->setLoadAllSheets(); //On autorise la lecture de toutes les pages d'un fichier
    $spreadsheet = $reader->load($inputFileName); //Chargement du fichier dans le reader
    
    //Sur les fichiers Excels sur lesquels des listes d'étudiants sont présents, ces derniers peuvent être présents sur plusieurs pages du fichier excel.
    //Ci-dessous, on extrait le nom de chaque page du fichier, afin de les parcourir et ajouter tous les étudiants présents sur toutes les pages.
    $loadedSheetNames = $spreadsheet->getSheetNames();
    //Ci-dessous, on parcours chaque page du fichier excel
    foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
        $spreadsheet->setActiveSheetIndexByName($loadedSheetName);//On précise la page du fichier excel sur laquelle on souhaite se trouver.
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        //$sheetData est un tableau qui détient toutes les informations présentes dans les cellules de la page active.
        //C'est ce tableau qui va être exploitée et lu pour extraire les informations du fichier Excel.
        $GroupeTDtemp ='';//Variable qui va concerver l'information sur le groupe de TD de l'étudiant à ajouter
        $annee = substr($sheetData[$LiAnnee][$ColAnnee], 5, 4); //Variable de l'année d'étude de l'étudiant (ou la liste d'étudiants)
        $anneescolaire = $sheetData[$LiAnneeEtu][$ColAnneeEtu];
        $promo = $annee + 5 - $anneescolaire;// On calcule l'année de promotion de l'étudiant selon l'année scolaire en cours et son année d'étude
        $spe = $sheetData[$LiSpe][$ColSpe];//Variable qui va concerver l'information sur la filière l'étudiant à ajouter
        if ($sheetIndex==0 or $sheetIndex==1){//Sur le fichier exemple à traiter, seul les 2 premère pages contenaient des liste d'étudiants à ajouter à la base de données. La 3ème page correspondait à des notes, et ne correspondaient pas au projet.
            for ($i = $LiEleve1er; $i <= $LiEleveDer; $i++){//On parcours les lignes du fichier Excel sur lequelles les informations des étudiants sont présents
                //Le nom du groupe de TP ou du groupe de TD de l'étudiant ne sont précisés que sur une seule ligne du fichier Excel
                //Ainsi, tant qu'un nouveau nom de TP ou de TD n'est pas précisé sur une autre ligne, on concervera le même nom de groupe de TP ou de TD pour l'étudiant traîté.
                if ($sheetData[$i][$ColTP] != NULL and $sheetData[$i][$ColTP] != '')
                    $GroupeTPtemp = $sheetData[$i][$ColTP];
                    //Si un nouveau nom de groupe est situé sur la ligne, on met a jour la variable du nom de groupe.
                if ($sheetData[$i][$ColTD] != NULL and $sheetData[$i][$ColTD] != '')
                    $GroupeTDtemp = $sheetData[$i][$ColTD];

                //La condition if ci-dessous permet de s'assurer que l'on n'ajoute pas des étudiants "fantomes" à la base de données : On ne traite pas les lignes du fichiers excels dans lesquels les champs nom ou prénom de l'étudiant sont vides.
                if($sheetData[$i][$Colnom] != NULL and $sheetData[$i][$Colprenom] != NULL and $sheetData[$i][$Colnom] !='' and $sheetData[$i][$Colprenom] !='')
                    //Si les champs ne sont effectivement pas vide, on a toutes les informations relative à l'étudiant, on peut donc l'ajouter à la base de données.
                    $bdd->query('INSERT INTO '.$bdName.'.etudiant (nom,prenom,promo,filiere,groupetd,groupetp) VALUES ('.'"'.$sheetData[$i][$Colnom].'"'.','.'"'.$sheetData[$i][$Colprenom].'"'.','.$promo.','.'"'.$spe.'"'.','.'"'.$GroupeTDtemp.'"'.','.'"'.$GroupeTPtemp.'"'.')');
            }
        }
    }
    echo("Tous les élèves ont bien été ajoutés à la base de données")

//Remarque : Etant donnée qu'une liste d'étudiant est ajouté à la base de donnée avant l'ajout de n'importe quel cours. Il est innutile de créer des éléments dans la table Présence.

//Remarque 2 : Organisation de $sheetData pour les fichiers xls : sélection de la ligne (1,2,..) puis de la colone pour extraire les données.
// Exemple : $sheetData['7']['A']  correspond à la valeur dans la cellule A7
    ?>
    </body>
</html>