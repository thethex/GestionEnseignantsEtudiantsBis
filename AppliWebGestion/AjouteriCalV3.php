<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="style.css" />
        <title>SaisieCours</title>
    </head>

    <body>
    <?php
    echo("Veuillez patienter, traitement en cours");
    echo('<br/>');
    require_once('Config.php');
    //Activation des rapports d'erreurs et connexion à la base de données.
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    $bdd = new PDO('mysql:host='.$bdServer.';dbname='.$bdName.';charset=utf8', $bdUser, $bdUserPasswd);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $datafiliere = $bdd->query('SELECT DISTINCT filiere FROM '.$bdName.'.etudiant');//Extraction des filières déjà existantes dans la base de données
    $dataprof = $bdd->query('SELECT * FROM '.$bdName.'.enseignant');//Extraction des enseignants déjà existants dans la base de données
    $datamodules = $bdd->query('SELECT DISTINCT nom FROM '.$bdName.'.module');//Extraction des modules déjà existants dans la base de données
    $Listefiliere = $datafiliere->fetchAll();
    $ListeEnseignants = $dataprof->fetchAll();
    $Listemodules = $datamodules->fetchAll();

    //iCalEasyReader.php permet la lecture simple des fichiers iCal par l'utilisation d'une classe iCalEasyReader.
    require_once('iCalEasyReader.php');

    $ical = new iCalEasyReader();
    $lines = $ical->load(file_get_contents($_FILES["userfile"]["tmp_name"]));
    
    
    //$lines correspond à un array() contenant toutes les informations sur le fichier iCal. Chaque élément de $lines correspond à un cours unique de l'empoi du temps. Ce cours est représenté par un autre array() qui contient plusieurs chaînes de caractères.
    
    //PAROUCRIR CHAQUE COURS DU FICHIER ICAL
    for ($i = 0; $i < count($lines['VEVENT']); $i++){
        //Pour chaque cours, on va extraire les différentes informations concernant le cours
        $StringDateStart = $lines['VEVENT'][$i]['DTSTART']; //On conserve la chaîne de caractères qui contient la date de début du cours
        $StringLeCours = $lines['VEVENT'][$i]['SUMMARY']; //On conserve la chaîne de caractères qui contient le module.
        $StringClasseProf = $lines['VEVENT'][$i]['DESCRIPTION']; //On conserve la chaîne de caractères qui contient les filières concernées (avec l'année d'étude) ET le nom de l'enseignant
        $StringAnnee = substr($lines['VEVENT'][$i]['DTSTAMP'], 0, 8); //On prélève l'année du cours
        //Ci-dessous, on détermine l'année d'étude en cours, selon la date actuelle
        if ((int)substr($StringAnnee, 4, 2)<$nouvelanneescolaire){//paramètre $nouvelanneescolaire dans Config.php
            $anneecourante = (int)substr($StringAnnee, 0, 4);
        }
        else{
            $anneecourante = (int)substr($StringAnnee, 0, 4)+1;
        }

        //Ci-dessous, on remodèle une chaîne de caractère pour qu'il corresponde à une date exploitable par la requête SQL
        $date= substr($lines['VEVENT'][$i]['DTSTART'], 0, 4).'-'.substr($lines['VEVENT'][$i]['DTSTART'], 4, 2).'-'.substr($lines['VEVENT'][$i]['DTSTART'], 6, 2).' '.substr($lines['VEVENT'][$i]['DTSTART'], 9, 2).':'.substr($lines['VEVENT'][$i]['DTSTART'], 11, 2).':'.substr($lines['VEVENT'][$i]['DTSTART'], 13, 2);

        //Les oppérations ci-dessous permettent de remodeler les dates et calculer la durée du coup avec la date de début et la date de fin du cours
        $heuredebut = (int)substr($lines['VEVENT'][$i]['DTSTART'], 9, 2);
        $mindebut = (int)substr($lines['VEVENT'][$i]['DTSTART'], 11, 2);
        $heurefin = (int)substr($lines['VEVENT'][$i]['DTEND'], 9, 2);
        $minfin = (int)substr($lines['VEVENT'][$i]['DTEND'], 11, 2);

        $dureeh=$heurefin-$heuredebut;
        $dureem=$minfin-$mindebut;
        $dureecours =$dureeh + $dureem/60;

        //Ci-dessous, on va préparer l'exploitation de la chaîne de caractère concernant les informations sur toutes les classes et l'enseignant impliqués dans le cours. C'est la partie la plus délicate car certains cas particuliers pourraient ne pas être traités.
        $parcoureur=''; //On créé un parcoureur, une chaîne de caractère vide, qui va construire les chaînes de caractères importantes qui constituent la chaîne de caractère à analyser.
        $annee=array(); //On créé 2 arrays vides "$annee" et "$filiere" qui vont contenir la liste de toutes les classes (ou filière+année d'études) qui sont concernés par le cours. Elles seront toujours de taille identique avec l'élément n du 1er tableau en relation avec l'élément n du 2ème tableau.
        $filiere = array();
        $nomprof = '';
        $prenomprof = '';
        //PARCOURIR LE STRING CLASSE PROF
        //La technique utilisée ici, est de parcourir chaque élément de la chaîne de caractère :
        //- Tant qu'il s'agit d'une lettre, on construit l'entité parcoureur
        //- Dès lors que l'on rencontre un caractère autre qu'une lettre, cela signifie que le parcoureur construit correspond très probablement au nom d'une filière ou au nom ou prénom d'un enseignant. On observe le parcoureur et on réagit en conséquence.
        for($l=0; $l < strlen($StringClasseProf); $l++){
            //La condition ci-dessous permet d'éliminer des chaînes de caractères qui apparaîtraient juste avant le nom d'un enseignant, qui empêcherai alors son identification. (cela peut toutefois correspondre au nom des groupes de TP, qui seront utiles à exploiter plus tard)
            if($parcoureur == '-TPA' or $parcoureur=='-TPB'){//ATTENTION, si d'autres chaînes de caractères particuliers apparaissent juste avant le nom d'un enseignant, ajouter l'exception ici.
                $parcoureur='';
            }
            //La condition ci-dessous ignore le traitement de chiffres, et passse au caractère suivant.
            //Les chiffres rencontrés correspondent à l'année d'étude de la classe concernée. Toutefois, étant systématiquement localisée, l'information est traitée ailleurs dans le traitement.
            if($StringClasseProf[$l]=='1' or $StringClasseProf[$l]=='2' or $StringClasseProf[$l]=='3' or $StringClasseProf[$l]=='4' or $StringClasseProf[$l]=='5' or $StringClasseProf[$l]=='6' or $StringClasseProf[$l]=='7' or $StringClasseProf[$l]=='8' or $StringClasseProf[$l]=='9'){
                $parcoureur='';
                continue;
            }
            //La condition ci-dessous construit la chaîne du parcoureur dès lors que le caractère rencontré n'est pas autre chose qu'une lettre. Le cas ou le caractère est un chiffre est déjà traîté par la condition précédente.
            if($StringClasseProf[$l]!=' ' and $StringClasseProf[$l]!='-' and $StringClasseProf[$l]!='(' and $StringClasseProf[$l]!="\n"){//ATTENTION, éventuellement, d'autres caractères peu visibles pourraient poser problème.
                //Le cas particulier du retour à la ligne "\n" peut déclancher des problèmes.
                $parcoureur.=$StringClasseProf[$l];
            }
            //La condition ci-dessous est une autre type d'exception. Etant une lettre, on ne pouvait pas la placer dans une des conditions précédentes (auquel cas, les enseignants dont le nom commence par "S" n'auraient pas été reconnues)
            //Il s'agit de l'exception de la présence d'un -S5 ou -S6 (ou autre) devant l'année d'étude, qui n'apporte aucune information utile.
            //Dans la condition ci-dessous, on traîte le cas ou le caractère est un tiret. Ce caractère est TOUJOURS observé après le nom d'une filière. Le parcoureur constuit correspond donc très certainement au nom d'une filière.
            //En outre, certains prénoms voire noms d'enseignants peut comporter un tiret.
            if($StringClasseProf[$l]=='-'){ 
                for ($j = 0; $j < count($Listefiliere); $j++){//On parcours les filières déjà existantes dans la base de données
                    if ($parcoureur==$Listefiliere[$j]['filiere']){//Si le parcoureurs correspond bien au nom d'une filière, on l'ajoute à la liste des filières concernées, associée à leur année.
                    //En effet, l'année d'étude de la filière concernée se situe systématiquement après le tiret.
                        array_push($filiere, $parcoureur);
                        array_push($annee, $StringClasseProf[$l+1]);
                    }
                if ($parcoureur=='MMT'){//Cas spécifique, la filière MM est décrite par "MMT" dans le fichier iCal.
                    array_push($filiere, 'MM');
                    array_push($annee, $StringClasseProf[$l+1]);
                }
                if ($parcoureur=='EPU'){//Cas spécifique ou EPU représente toutes les filières
                    for ($j = 0; $j < count($EPU); $j++){//On parcours la table $EPU qui contient toutes les filières existantes
                        array_push($filiere, $EPU[$j]);
                        array_push($annee, $StringClasseProf[$l+1]);
                    }
                }     
                $parcoureur.=$StringClasseProf[$l];//Une fois les données correctements prises en considération, on peut réinitialiser le parcoureur.
                }
            }
            //Dans la condition ci-dessous, on traîte le cas ou le caractère est un espace. Ce caractère est TOUJOURS observé après le nom d'un enseignant. Le parcoureur constuit correspond donc très certainement au nom d'un enseignant.
            if($StringClasseProf[$l]==' '){
                for ($j = 0; $j < count($ListeEnseignants); $j++){// On parcourt la liste des enseignants présent dans la base de données pour voir si le parcoureur correspond à ce nom
                    if (strtoupper($parcoureur)==strtoupper($ListeEnseignants[$j]['nom'])){
                        $nomprof = $parcoureur; //
                    }
                    //On suit le même procédé avec le prénom de l'enseignant, car éventuellement, les noms et prénoms pourraient être interchangés ou l'un d'eux peuvent être absent.
                    if (strtoupper($parcoureur)==strtoupper($ListeEnseignants[$j]['prenom'])){
                        $prenomprof = $parcoureur;
                    }
                }
                $parcoureur='';//Une fois les données correctements prises en considération, on peut réinitialiser le parcoureur.
                
            }
            //La condition ci-dessous est similaire à la condition précédente. Toutefois, le caractère '(' peut suivre ou non le prenom ou nom de l'enseignant. Alors on réitère l'anayse du parcoureur avant de cesser la lecture de la chaîne de caractères, car les caractères qui suivent l'ouverture de la parenthèse ne sont pas utiles.
            if ($StringClasseProf[$l]=='('){
                for ($j = 0; $j < count($ListeEnseignants); $j++){
                    if (strtoupper($parcoureur)==strtoupper($ListeEnseignants[$j]['nom'])){
                        $nomprof = $parcoureur;
                    }
                    if (strtoupper($parcoureur)==strtoupper($ListeEnseignants[$j]['prenom'])){
                        $prenomprof = $parcoureur;
                    }
                }
                $parcoureur='';
                break;
            }
        }
        //FIN DU PARCOURS DU STRING CLASSE PROF

        //PARCOURIR LE STRING SUMMARY AVEC LE NOM DU COURS
        //Dans une même logique, on va parcourir le string summary pour lire le nom du module et le type du cours
        $parcoureur='';
        $nomcours='';
        $l=0;
        for($l=0; $l<strlen($StringLeCours); $l++){
            //On construit d'abords le parcoureur. Une fois le caractère ajouté, il s'agit peut être d'un type de cours ou d'un nom de module.
            if($StringLeCours[$l]!="_" and $StringLeCours[$l]!="-"){
                $parcoureur.=$StringLeCours[$l];
            }
            if($StringLeCours[$l]=='_' or $StringLeCours[$l]=='-'){
                $parcoureur='';
            }
            if($parcoureur=='CM'){
                $typecours='CM';
                break;//Le type du cours se situe toujours après le nom du module, la chaîne de caractères qui suivent ne sont donc plus intéressantes à exploiter.
            }
            if($parcoureur=='TD'){
                $typecours='TD';
                break;
            }
            if($parcoureur=='TP'){
                $typecours='TP';
                break;
            }
            if(strlen($parcoureur)==7){//Le nom d'un module est toujours de taille 7
                $p=0;
                for($p=0;$p<count($Listemodules);$p++){//On parcours la liste des modules existant dans la base de données pour voir si le parcoureur correspond effectivement au nom d'un module.
                    if($Listemodules[$p]['nom']==$parcoureur){
                        $nomcours=$parcoureur;
                    }
                }
            }
        }
        //FIN DU PARCOURS DU STRING SUMMARY

        //Cas particulier des cours de langues, considérés comme des TD
        if (substr($nomcours, 0, 4)=='LANG'){
            $typecours='TD';
        }

        //On a à présent extrait toutes les données nécessaires d'un cours traité dans le fichier iCal.
        //(En effet, dans cette partie du code, nous sommes toujours dans la boucle for, avec la variable $i définissant le cours concerné dans l'array $lines, voire ligne 34 ou équivalent)
        //A partir d'ici, on va commencer à réaliser les requêtes SQL qui vont permettrent d'ajouter ce cours à la base de données
        $idduprof =0;//Initialisation de l'ID du prof, il peut ne pas être renseigné.
        $iddumodule =0;//Initialisation de l'ID du module, il peut ne pas être renseigné.

        $idprof = $bdd->query('SELECT idenseignant FROM '.$bdName.'.enseignant WHERE (nom = "'.$nomprof.'") and (prenom ="'.$prenomprof.'")');//On relève l'ID de l'enseignant dans la base de données, avec les noms et prénoms relevés
        $idprof2 = $idprof->fetchAll();
        $idmodule = $bdd->query('SELECT idmodule FROM '.$bdName.'.module WHERE (nom = "'.$nomcours.'")');//On relève l'ID de du module dans la base de données
        $idmodule2 = $idmodule->fetchAll();
    
        $idduprof = $idprof2[0]['idenseignant'];//attribution de l'ID de l'enseignant
        $iddumodule = $idmodule2[0]['idmodule'];//attribution de l'ID du module
        
        //Les lignes ci-dessous premettent d'ajouter le cours à la base de données selon les 4 situations suivante :
        //Avec l'enseignant ET le module de renseignés
        if ($idduprof!=0 and $iddumodule!=0)
            $bdd->query('INSERT INTO '.$bdName.'.cours (type,datecours,duree,idmodule,idenseignant) VALUES ('.'"'.$typecours.'"'.','."'".$date."'".','.$dureecours.','.$iddumodule.','.$idduprof.')');
        //Si l'information sur l'enseignant est manquant
        if ($iddumodule!=0 and $idduprof==0)
            $bdd->query('INSERT INTO '.$bdName.'.cours (type,datecours,duree,idmodule) VALUES ('.'"'.$typecours.'"'.','."'".$date."'".','.$dureecours.','.$iddumodule.')');
        //Si l'information sur le module est manquant
        if ($iddumodule==0 and $idduprof!=0)
            $bdd->query('INSERT INTO '.$bdName.'.cours (type,datecours,duree,idenseignant) VALUES ('.'"'.$typecours.'"'.','."'".$date."'".','.$dureecours.','.$idduprof.')');
        //Si les deux informations sont manquantes
        if ($iddumodule==0 and $idduprof==0)
            $bdd->query('INSERT INTO '.$bdName.'.cours (type,datecours,duree) VALUES ('.'"'.$typecours.'"'.','."'".$date."'".','.$dureecours.')');
        
        //Le cours a enfin été ajouté. Il faut toutefois encore relier le cours aux étudiants concernés, en ajoutant des éléments dans la table "Présence" de la base de données.
        $datadernieridcours = $bdd->query('SELECT MAX(idcours) FROM '.$bdName.'.cours');//On reprend l'ID du cours que nous venons d'ajouter
        $dernieridcours = $datadernieridcours->fetchAll();
        
        $l=0;
        //Puis, on parcours chaque élément de l'array "filiere" que nous avons construit précédemment, afin que nous puissions ajouter un élément de la table présence pour chaque élève de chaque filière et année d'étude concernée.
        for ($l=0; $l<count($filiere); $l++){
            $promosql = $anneecourante+5-$annee[$l];//Calcul de l'année de promotion des étudiants selon l'année d'étude.
            $dataetudiantconcernes = $bdd->query('SELECT idetudiant FROM '.$bdName.'.etudiant WHERE (promo='.$promosql.' AND filiere="'.$filiere[$l].'")');//Sélection de l'ensemble des étudiants concernés, selon la filière et l'année de promotion.
            $etudiantconcernes = $dataetudiantconcernes->fetchAll();
            $et=0;
            //Enfin, pour chaque étudiant, on ajoute un élément à la table "Présence".
            for($et=0; $et<count($etudiantconcernes);$et++){
                $bdd->query('INSERT INTO '.$bdName.'.presence (idetudiant,idcours) VALUES ('.'"'.$etudiantconcernes[$et]['idetudiant'].'"'.','."'".$dernieridcours[0]['MAX(idcours)']."'".')');
            }
        }
    }
    //Ici, nous sommes sortis de la première boucle for et avons traité le premier cours contenu dans le fichier iCal.
    //Le procédé va donc se répéter pour chaque cours du fichier iCal.
    echo("Tous les cours ont bien été ajoutés à la base de données");
    ?>
    </body>
</html>