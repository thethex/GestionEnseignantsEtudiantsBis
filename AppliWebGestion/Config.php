<?php
//Paramètres global pour le serveur :
$bdName = 'enseignementpolytech1'; //Nom de la base de données MySQL
$bdUser = 'root';// Identifiant pour se connecter a la base de données (par PDO)
$bdUserPasswd = '';// Mot de passe pour se connecter à la base de données (par PDO)
$bdServer = 'localhost:3308';// Adresse de localisation de la base de données MySQL
$webServer = '';// ???

//Paramètre pour définir le mois après lequel une nouvelle année universitaire commence officiellement
//Ce paramètre impact AjouteriCalV3 et Aperçu.php.
$nouvelanneescolaire = 8;//Une fois le mois d'Aout passé, une nouvelle année scolaire commence


//Paramètres pour AjouterExcel.php
$LiAnneeEtu = 1; // Ligne ou est renseigné l'année d'étude de l'étudiant
$ColAnneeEtu = 'F'; // Colonne ou est renseigné l'année d'étude de l'étudiant
$LiSpe = 1; // Ligne ou est renseigné la filière de l'étudiant
$ColSpe = 'E'; // Colonne ou est renseigné la filière de l'étudiant
$LiAnnee = 2; // Ligne ou est renseigné l'année d'étude de l'étudiant
$ColAnnee = 'G'; // Colonne ou est renseigné l'année d'étude de l'étudiant
$LiEleve1er = 7; // Première ligne sur le fichier Excel sur lequel se trouve les données sur le 1er étudiant de la liste.
$ColTP = 'A'; // Numéro de colonne ou se trouve le nom du groupe de TP de l'étudiant
$Colnom = 'E'; // Numéro de colonne ou se trouve le nom de l'étudiant 
$Colprenom = 'F'; // Numéro de colonne ou se trouve le prénom de l'étudiant
$ColTD = 'I'; // Numéro de colonne ou se trouve le nom du groupe de TD de l'édutiant


//Paramètres pour AjouteriCal.php
$EPU = array('EBE','IAI','IDU','ITII','ITII-CM','ITII-MP','MM'); //EPU est une chaîne de caractères que l'on peut trouver dans le fichier iCal, il représente toutes les fillières.
$typecours='TP';//Par défaut, le cours est considéré comme un TP.


//Paramètres pour Impression.php
// Les paramètres ci-dessous permettent de localiser et placer chaque information du cours sur la fiche d'absence.
$NomFichierModele = 'Liste_IDU3_S6FicheAbsenceUPDATED.ods';//Nom entier du fichier Modèle utilisé pour réutiliser le formatage de la fiche d'absence.
$CellIDFICHE = 'B2';//Emplacement de l'ID de la fiche d'absence
$CellDate = 'E2'; //Emplacement de la date
$ColNom = 'D'; //Emplacement du nom de chaque étudiant
$ColPrenom = 'E'; //Emplacement du prénom de chaque étudiant
$FirstLigneEtu1 = 5; //Ligne correspondant au premier étudiant de la liste, sur la 1ère feuille
$LastLigneEtu1 = 24; //Ligne correspondant au dernier étudiant de la liste, sur la 1ère feuille
$FirstLigneEtu2 = 29; //Ligne correspondant au premier étudiant de la liste, sur la 2ère feuille
$Etudiantparpage = 20;//Nombre d'étudiants par page.

$HauteurLigneEtudiant = 38;//Défini la hauteur des lignes.

//Informations pour le 1er cours
//$CellFiliereCours1 ='F2'; //Emplacement de la filière concernée/fusionné avec l'année
$CellAnneeCours1 ='G2'; //Emplacement de l'année d'étude de la classe concernée
$CellModuleCours1 ='F3'; //Emplacement du module concerné
$CellHeureCours1 ='G3'; //Emplacement du début de l'heure du cours
$CellTypeCours1 ='H3'; //Emplacement du type de cours (CM, TD, TP)
$CellEnseignantCours1 ='F4'; //Emplacement du nom de l'enseignant concerné.

//Informations pour le 2ème cours
//$CellFiliereCours2 ='J2';
$CellAnneeCours2 ='K2';
$CellModuleCours2 ='J3';
$CellHeureCours2 ='K3';
$CellTypeCours2 ='L3';
$CellEnseignantCours2 ='J4';

//Informations pour le 3ème cours
//$CellFiliereCours3 ='N2';
$CellAnneeCours3 ='O2';
$CellModuleCours3 ='N3';
$CellHeureCours3 ='O3';
$CellTypeCours3 ='P3';
$CellEnseignantCours3 ='N4';

//Informations pour le 4ème cours
//$CellFiliereCours4 ='R2';
$CellAnneeCours4 ='S2';
$CellModuleCours4 ='R3';
$CellHeureCours4 ='S3';
$CellTypeCours4 ='T3';
$CellEnseignantCours4 ='R4';

//Informations pour le 5ème cours
//$CellFiliereCours5 ='V2';
$CellAnneeCours5 ='W2';
$CellModuleCours5 ='V3';
$CellHeureCours5 ='W3';
$CellTypeCours5 ='X3';
$CellEnseignantCours5 ='V4';


?>