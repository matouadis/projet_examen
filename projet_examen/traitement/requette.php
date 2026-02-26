<?php
include_once "datadb.php";

// ==========================
// FONCTIONS NIVEAUX
// ==========================
function ajouterNiveau($nom){
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO niveau (nom) VALUES (:nom)");
        return $stmt->execute(['nom' => $nom]);
    } catch (PDOException $e){
        return false;
    }
}

function modifierNiveau($idniveau, $nom){
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE niveau SET nom = :nom WHERE idniveau = :id");
        return $stmt->execute(['nom' => $nom, 'id' => $idniveau]);
    } catch (PDOException $e){
        return false;
    }
}

function supprimerNiveau($idniveau){
    global $pdo;
    try {
        $stmt = $pdo->prepare("DELETE FROM niveau WHERE idniveau = :id");
        return $stmt->execute(['id' => $idniveau]);
    } catch (PDOException $e){
        return false;
    }
}

function getNiveau(){
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM niveau ORDER BY idniveau ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e){
        return [];
    }
}

// ==========================
// FONCTIONS CLASSES
// ==========================
function ajouterClasse($nom){
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO classe (nom) VALUES (:nom)");
    return $stmt->execute(['nom' => $nom]);
}

function getClasse(){
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM classe ORDER BY nom");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==========================
// FONCTIONS ÉTUDIANTS
// ==========================
function ajouterEtudiant($nom, $prenom, $idclasse, $matricule){
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO etudiant (nom, prenom, idclasse, matricule) 
                           VALUES (:nom, :prenom, :idclasse, :matricule)");
    return $stmt->execute([
        'nom' => $nom,
        'prenom' => $prenom,
        'idclasse' => $idclasse,
        'matricule' => $matricule
    ]);
}

function getIdEtudiantByMatricule($matricule){
    global $pdo;
    $stmt = $pdo->prepare("SELECT idetudiant FROM etudiant WHERE matricule = :matricule LIMIT 1");
    $stmt->execute(['matricule' => $matricule]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res ? $res['idetudiant'] : null;
}
function getEtudiantByMatricule($matricule){
    global $pdo; // si tu utilises PDO dans ce fichier

    $stmt = $pdo->prepare("
        SELECT e.*, c.nom AS classe
        FROM etudiant e
        LEFT JOIN classe c ON e.idclasse = c.idclasse
        WHERE e.matricule = :matricule
        LIMIT 1
    ");
    $stmt->execute(['matricule' => $matricule]);
    return $stmt->fetch(PDO::FETCH_ASSOC);

}

// ==========================
// FONCTIONS MODULES
// ==========================
function ajouterModule($nom, $coef){
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO module (nommodule, coefficient) VALUES (:nom, :coef)");
    $stmt->execute(['nom' => $nom, 'coef'=> $coef]);
    return $pdo->lastInsertId(); 
}

function ajouterModuleDansClasse($idmodule, $idclasse){
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO classe_module (idmodule, idclasse) VALUES (:idmodule, :idclasse)");
    return $stmt->execute(['idmodule' => $idmodule, 'idclasse' => $idclasse]);
}

function getIdModuleByName($nomModule){
    global $pdo;
    $stmt = $pdo->prepare("SELECT idmodule FROM module WHERE nommodule = :nom LIMIT 1");
    $stmt->execute(['nom' => $nomModule]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res ? $res['idmodule'] : null;
}

// ==========================
// FONCTIONS ÉVALUATIONS
// ==========================
function ajouterEvaluation($idetudiant, $idmodule, $note, $typeevaluation = 'examen') {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO evaluation (idetudiant, idmodule, note, typeevaluation, dateevaluation)
        VALUES (:idetudiant, :idmodule, :note, :typeevaluation, :dateevaluation)
    ");
    return $stmt->execute([
        'idetudiant'     => $idetudiant,
        'idmodule'       => $idmodule,
        'note'           => $note,
        'typeevaluation' => $typeevaluation,
        'dateevaluation' => date('Y-m-d')
    ]);
}

function getNotesByEtudiant($idetudiant){
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT ev.idevaluation, m.nommodule, m.coefficient, ev.note
        FROM evaluation ev
        JOIN module m ON ev.idmodule = m.idmodule
        WHERE ev.idetudiant = ?
    ");
    $stmt->execute([$idetudiant]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function modifierNote($id, $note){
    global $pdo;
    $stmt = $pdo->prepare("UPDATE evaluation SET note = :note WHERE idevaluation = :id");
    return $stmt->execute(['note' => $note, 'id' => $id]);
}

function supprimerNote($id){
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM evaluation WHERE idevaluation = :id");
    return $stmt->execute(['id' => $id]);
}

// ==========================
// STATISTIQUES / COMPTES
// ==========================
function getNombreClasses() {
    global $pdo;
    return (int) $pdo->query("SELECT COUNT(*) FROM classe")->fetchColumn();
}

function getNombreModules() {
    global $pdo;
    return (int) $pdo->query("SELECT COUNT(*) FROM module")->fetchColumn();
}

function getNombreEtudiantsParNiveau() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT n.nom AS niveau, COUNT(e.idetudiant) AS nb
        FROM etudiant e
        JOIN classe c ON e.idclasse = c.idclasse
        JOIN niveau n ON c.idniveau = n.idniveau
        GROUP BY n.nom
    ");
    $result = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $result[$row['niveau']] = $row['nb'];
    }
    return $result;
}

function getEtudiantsParStatus() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT 
            SUM(CASE WHEN moyenne >= 10 THEN 1 ELSE 0 END) AS admis,
            SUM(CASE WHEN moyenne >=5 AND moyenne < 10 THEN 1 ELSE 0 END) AS ajournes,
            SUM(CASE WHEN moyenne < 5 THEN 1 ELSE 0 END) AS exclus
        FROM (
            SELECT e.idetudiant, AVG(ev.note) AS moyenne
            FROM etudiant e
            LEFT JOIN evaluation ev ON e.idetudiant = ev.idetudiant
            GROUP BY e.idetudiant
        ) AS sub
    ");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getModulesParNiveau() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT n.nom AS niveau, COUNT(cm.idmodule) AS nb_modules
        FROM niveau n
        LEFT JOIN classe c ON c.idniveau = n.idniveau
        LEFT JOIN classe_module cm ON cm.idclasse = c.idclasse
        GROUP BY n.nom
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMoyenneModules() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT m.nommodule, ROUND(AVG(e.note),2) AS moyenne
        FROM module m
        LEFT JOIN evaluation e ON e.idmodule = m.idmodule
        GROUP BY m.nommodule
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);

}


?>