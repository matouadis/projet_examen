<?php
require_once("requette.php");


 // AJOUTER UN NIVEAU
// ==========================
if(isset($_POST['ajouterniveau'])){
    $nom = trim($_POST['nom']);
    $nomMin = strtolower($nom);

    if(empty($nom)){
        header("Location: ../index.php?page=niveau&erreur=vide");
        exit();
    }

    // Vérifier si le niveau existe déjà (insensible à la casse)
    $niveaux = getNiveau();
    foreach($niveaux as $n){
        if(strtolower($n['nom']) === $nomMin){
            header("Location: ../index.php?page=niveau&erreur=existe");
            exit();
        }
    }

    // Ajouter le niveau
    if(ajouterNiveau($nom)){
        header("Location: ../index.php?page=niveau&success=ajout");
    } else {
        header("Location: ../index.php?page=niveau&erreur=autre");
    }
    exit();
}

// ==========================
// SUPPRIMER UN NIVEAU
// ==========================
if(isset($_POST['supprimerniveau'])){
    $id = $_POST['idniveau'] ?? null;

    if(empty($id)){
        header("Location: ../index.php?page=niveau&erreur=autre");
        exit();
    }

    if(supprimerNiveau($id)){
        header("Location: ../index.php?page=niveau&success=delete");
    } else {
        header("Location: ../index.php?page=niveau&erreur=autre");
    }
    exit();
}

// ==========================
// MODIFIER UN NIVEAU
// ==========================
if(isset($_POST['modifierniveau'])){
    $id = $_POST['idniveau'] ?? null;
    $nom = trim($_POST['nom']);
    $nomMin = strtolower($nom);

    if(empty($nom) || empty($id)){
        header("Location: ../index.php?page=niveau&erreur=vide");
        exit();
    }

    // Vérifier doublon avant modification
    $niveaux = getNiveau();
    foreach($niveaux as $n){
        if(strtolower($n['nom']) === $nomMin && $n['idniveau'] != $id){
            header("Location: ../index.php?page=niveau&erreur=existe");
            exit();
        }
    }

    if(modifierNiveau($id, $nom)){
        header("Location: ../index.php?page=niveau&success=modif");
    } else {
        header("Location: ../index.php?page=niveau&erreur=autre");
    }
    exit();

}



if(isset($_POST["ajouteretudiant"])){

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $idclasse = $_POST['idclasse'];
    $matricule = $_POST['matricule'];

    if(!empty($nom) && !empty($prenom) && !empty($idclasse) && !empty($matricule)){

        if(ajouterEtudiant($nom, $prenom, $idclasse, $matricule)){
            header("Location: ../index.php?page=etudiant&success=1");
            exit();
        } else {
            header("Location: ../index.php?page=etudiant&erreur=1");
            exit();
        }

    } else {
        header("Location: ../index.php?page=etudiant&erreur=vide");
        exit();
    }
}

if(isset($_POST["ajouteevaluation"])) {

    $matricule = htmlspecialchars($_POST['matricule']);
    $nomModule = htmlspecialchars($_POST['module']);
    $note      = (float) $_POST['note'];

    // Récupérer l'id de l'étudiant
    $idetudiant = getIdEtudiantByMatricule($matricule);
    if($idetudiant === null){
        die("Erreur : étudiant introuvable avec le matricule '$matricule'.");
    }

    // Récupérer l'id du module
    $idmodule = getIdModuleByName($nomModule);
    if($idmodule === null){
        die("Erreur : module '$nomModule' introuvable.");
    }

    // Ajouter l'évaluation
    if(ajouterEvaluation($idetudiant, $idmodule, $note)){
        header("Location: ../index.php?page=evaluation&succes=yes");
        exit();
    } else {
        die("Erreur : impossible d'ajouter l'évaluation.");
    }
}

// Action pour afficher les statistiques
if(isset($_POST['afficher_statistiques'])){

    // Requête pour nombre d'étudiants
    $stmt = $pdo->query("SELECT COUNT(*) AS nbEtudiants FROM etudiant");
    $nbEtudiants = $stmt->fetch(PDO::FETCH_ASSOC)['nbEtudiants'];

    // Requête pour nombre de modules
    $stmt = $pdo->query("SELECT COUNT(*) AS nbModules FROM module");
    $nbModules = $stmt->fetch(PDO::FETCH_ASSOC)['nbModules'];

    // Moyenne par module
    $stmt = $pdo->query("
        SELECT m.nommodule, ROUND(AVG(e.note),2) AS moyenne
        FROM evaluation e
        JOIN module m ON e.idmodule = m.idmodule
        GROUP BY m.nommodule
    ");
    $moyenneModules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Moyenne par étudiant
    $stmt = $pdo->query("
        SELECT CONCAT(eu.nom,' ',eu.prenom) AS etudiant, ROUND(AVG(ev.note),2) AS moyenne
        FROM evaluation ev
        JOIN etudiant eu ON ev.idetudiant = eu.idetudiant
        GROUP BY ev.idetudiant
    ");
    $moyenneEtudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Stockage dans session pour afficher côté HTML
    $_SESSION['stats'] = [
        'nbEtudiants' => $nbEtudiants,
        'nbModules' => $nbModules,
        'moyenneModules' => $moyenneModules,
        'moyenneEtudiants' => $moyenneEtudiants
    ];

    // Redirection vers la page de statistiques
    header("Location: http://localhost/projet%20examen/?page=statistique");
    exit();
}
// ==========================
// AJOUTER MODULE
// ==========================
if(isset($_POST['ajoutermodule'])){

    $nom = trim($_POST['module']);
    $coef = trim($_POST['coef']);
    $idclasse = $_POST['classe'] ?? null;

    if(empty($nom) || empty($coef)){
        header("Location: ../index.php?page=module&erreur=vide");
        exit();
    }

    // 1️⃣ Ajouter le module
    $idmodule = ajouterModule($nom, $coef);

    if(!$idmodule){
        header("Location: ../index.php?page=module&erreur=insert");
        exit();
    }

    // 2️⃣ Si une classe est sélectionnée → lier
    if(!empty($idclasse)){
        ajouterModuleDansClasse($idmodule, $idclasse);
    }

    header("Location: ../index.php?page=module&success=1");
    exit();
}


// ==========================
// SUPPRIMER MODULE
// ==========================
if(isset($_POST['supprimermodule'])){

    $id = $_POST['idmodule'] ?? null;

    if(empty($id)){
        header("Location: ../index.php?page=module&erreur=id");
        exit();
    }

    if(supprimerModule($id)){
        header("Location: ../index.php?page=module&success=delete");
        exit();
    } else {
        header("Location: ../index.php?page=module&erreur=delete");
        exit();
    }
}


if(isset($_POST['ajouteevaluation'])){
    $matricule = trim($_POST['matricule']);
    $nomModule = trim($_POST['module']);
    $note = (float) $_POST['note'];

    $idetudiant = getIdEtudiantByMatricule($matricule);
    if($idetudiant === null){
        die("Erreur : étudiant introuvable.");
    }

    $idmodule = getIdModuleByName($nomModule);
    if($idmodule === null){
        die("Erreur : module introuvable.");
    }

    if(ajouterEvaluation($idetudiant, $idmodule, $note)){
        header("Location: ../index.php?page=bulletin&matricule=".$matricule);
        exit();
    } else {
        die("Erreur : impossible d'ajouter la note.");
    }
}

// ==========================
// Modifier une note
if(isset($_POST['modifiernote'])){
    $id = $_POST['idevaluation'];
    $note = (float) $_POST['note'];
    $matricule = $_POST['matricule'] ?? null;

    modifierNote($id, $note);

    if($matricule){
        header("Location: ../index.php?page=bulletin&matricule=".$matricule);
    } else {
        header("Location: ../index.php?page=bulletin");
    }
    exit();
}

// ==========================
// Supprimer une note
if(isset($_POST['supprimernote'])){
    $id = $_POST['idevaluation'];
    $matricule = $_POST['matricule'] ?? null;

    supprimerNote($id);

    if($matricule){
        header("Location: ../index.php?page=bulletin&matricule=".$matricule);
    } else {
        header("Location: ../index.php?page=bulletin");
    }
    exit();
}


if(isset($_POST['ajouterclasse'])){
    $nom = trim($_POST['nom']);
    if(empty($nom)){
        header("Location: ../index.php?page=classe&erreur=vide");
        exit();
    }

    // Vérifier si la classe existe déjà
    $classes = getClasse();
    foreach($classes as $c){
        if(strtolower($c['nom']) === strtolower($nom)){
            header("Location: ../index.php?page=classe&erreur=existe");
            exit();
        }
    }

    // Ajouter la classe
    if(ajouterClasse($nom)){
        header("Location: ../index.php?page=classe&success=1");
        exit();
    } else {
        header("Location: ../index.php?page=classe&erreur=autre");
        exit();
    }
}