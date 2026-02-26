<?php
// ──────────────────────────────
// bulletin.php
// ──────────────────────────────

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . "/../traitement/datadb.php";

$etudiant = null;
$notes = [];
$moyenne = 0;
$statut = "";
$color = "";
$erreur = "";

// Si un matricule est passé
if(isset($_GET['matricule'])){
    $matricule = trim($_GET['matricule']);
    $etudiant = getEtudiantByMatricule($matricule);

    if($etudiant){
        $notes = getNotesByEtudiant($etudiant['idetudiant']);

        if($notes){
            $totalCoef = array_sum(array_column($notes,'coefficient'));
            $sommeNotes = 0;
            foreach($notes as $n) $sommeNotes += $n['note']*$n['coefficient'];
            $moyenne = $totalCoef>0 ? $sommeNotes/$totalCoef : 0;

            if($moyenne>=10){ $statut="Admis"; $color="success"; }
            elseif($moyenne>=5){ $statut="Ajourné"; $color="warning"; }
            else{ $statut="Exclus"; $color="danger"; }
        }
    } else {
        $erreur = "Aucun étudiant trouvé avec ce matricule : '$matricule'";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bulletin Étudiant</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">📄 Bulletin Étudiant</h1>

    <!-- Formulaire -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-bold">🔍 Rechercher un Étudiant</div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="matricule" class="form-control" placeholder="Matricule Étudiant" required>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-success w-100" type="submit">Générer Bulletin</button>
                </div>
            </form>
        </div>
    </div>

    <?php if($erreur): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if($etudiant): ?>
        <div class="card p-4 mb-4">
            <h4><?= htmlspecialchars($etudiant['nom']." ".$etudiant['prenom']) ?> - <?= htmlspecialchars($etudiant['classe']) ?></h4>

            <?php if($notes): ?>
                <table class="table table-striped mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>Module</th>
                            <th>Coefficient</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($notes as $n): ?>
                            <tr>
                                <td><?= htmlspecialchars($n['nommodule']) ?></td>
                                <td><?= $n['coefficient'] ?></td>
                                <td><?= number_format($n['note'],2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p class="fw-bold mt-2">Moyenne: <span class="text-primary"><?= number_format($moyenne,2) ?></span> | Statut: <span class="badge bg-<?= $color ?>"><?= $statut ?></span></p>
            <?php else: ?>
                <div class="alert alert-info">Aucune note disponible.</div>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="bulletin_pdf.php?matricule=<?= urlencode($etudiant['matricule']) ?>" class="btn btn-primary">🖨️ Générer PDF</a>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>