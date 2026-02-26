<?php

require_once __DIR__ . "/../traitement/action.php";

// =========================
// 1️⃣ Nombre de classes
// =========================
$stmt = $pdo->query("SELECT COUNT(*) as total FROM classe");
$total_classes = $stmt->fetch()['total'];

// =========================
// 2️⃣ Nombre de modules
// =========================
$stmt = $pdo->query("SELECT COUNT(*) as total FROM module");
$total_modules = $stmt->fetch()['total'];

// =========================a
// 3️⃣ Étudiants par classe
// =========================
$stmt = $pdo->query("
    SELECT c.nom AS classe, COUNT(e.idetudiant) AS total
    FROM classe c
    LEFT JOIN etudiant e ON e.idclasse = c.idclasse
    GROUP BY c.idclasse
");
$par_classe = $stmt->fetchAll();

// =========================
// 4️⃣ Liste étudiants + moyenne + statut (CORRIGÉ)
// =========================
$stmt = $pdo->query("
    SELECT e.idetudiant, e.nom, e.prenom, c.nom AS classe,
           COALESCE(m.moyenne, 0) AS moyenne
    FROM etudiant e
    LEFT JOIN classe c ON e.idclasse = c.idclasse
    LEFT JOIN (
        SELECT idetudiant, AVG(note) AS moyenne
        FROM evaluation
        GROUP BY idetudiant
    ) m ON e.idetudiant = m.idetudiant
");

$etudiants = $stmt->fetchAll();

$statuts = ['Admis'=>0,'Ajourné'=>0,'Exclus'=>0];

foreach($etudiants as &$etu){
    if($etu['moyenne'] >= 10){
        $etu['statut'] = "Admis";
        $statuts['Admis']++;
    }elseif($etu['moyenne'] >= 5){
        $etu['statut'] = "Ajourné";
        $statuts['Ajourné']++;
    }else{
        $etu['statut'] = "Exclus";
        $statuts['Exclus']++;
    }
}
    
?>
<!DOCTYPE html>
<html>
<head>
<title>Tableau de Bord</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

<h2 class="text-center mb-4">📊 Tableau de Bord Académique</h2>

<!-- Résumé -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-success text-white p-3">
            Nombre de classes : <strong><?= $total_classes ?></strong>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-primary text-white p-3">
            Nombre de modules : <strong><?= $total_modules ?></strong>
        </div>
    </div>
</div>

<!-- Étudiants par classe -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">Étudiants par Classe</div>
    <div class="card-body">
        <?php foreach($par_classe as $c): ?>
            <p><?= $c['classe'] ?> : <strong><?= $c['total'] ?></strong></p>
        <?php endforeach; ?>
    </div>
</div>

<!-- Étudiants par statut -->
<div class="card mb-4">
    <div class="card-header bg-warning">Étudiants par Statut</div>
    <div class="card-body">
        <p>Admis : <strong><?= $statuts['Admis'] ?></strong></p>
        <p>Ajournés : <strong><?= $statuts['Ajourné'] ?></strong></p>
        <p>Exclus : <strong><?= $statuts['Exclus'] ?></strong></p>
    </div>
</div>

<!-- Liste complète -->
<div class="card">
    <div class="card-header bg-dark text-white">Liste des Étudiants</div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Moyenne</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($etudiants as $etu): ?>
                <tr>
                    <td><?= $etu['nom'] ?></td>
                    <td><?= $etu['prenom'] ?></td>
                    <td><?= $etu['classe'] ?></td>
                    <td><?= number_format($etu['moyenne'],2) ?></td>
                    <td><?= $etu['statut'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</body>
</html>