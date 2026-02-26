<?php

require_once __DIR__ . "/../traitement/action.php";


// Récupérer tous les modules avec leur classe associée
$modules = $pdo->query("
    SELECT m.idmodule, m.nommodule, m.coefficient, c.nom AS classe
    FROM module m
    LEFT JOIN classe_module cm ON m.idmodule = cm.idmodule
    LEFT JOIN classe c ON cm.idclasse = c.idclasse
    ORDER BY m.nommodule
")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les classes pour le formulaire d’ajout
$classes = $pdo->query("SELECT * FROM classe ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion des Modules</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .table th, .table td { vertical-align: middle; }
</style>
</head>
<body>
<div class="container my-5">

    <h1 class="text-center mb-4">📚 Gestion des Modules</h1>

    <!-- Formulaire d'ajout d'un module -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white fw-bold">➕ Ajouter un Module</div>
        <div class="card-body">
            <form action="traitement/action.php" method="POST" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="module" class="form-control" placeholder="Nom du module" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="coef" class="form-control" placeholder="Coefficient" min="1" required>
                </div>
                <div class="col-md-3">
                    <select name="classe" class="form-select">
                        <option value="">Aucune classe</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c['idclasse'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="ajoutermodule" class="btn btn-success w-100">
                        <i class="bi bi-plus-circle"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des modules -->
    <div class="card">
        <div class="card-header bg-dark text-white fw-bold">Liste des Modules</div>
        <div class="card-body">

            <!-- Messages -->
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✔ Opération réussie
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['erreur'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ❌ Une erreur est survenue
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Coefficient</th>
                            <th>Classe</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($modules)): ?>
                            <?php foreach($modules as $mod): ?>
                                <tr>
                                    <td><?= $mod['idmodule'] ?></td>
                                    <td><?= htmlspecialchars($mod['nommodule']) ?></td>
                                    <td><?= $mod['coefficient'] ?></td>
                                    <td><?= $mod['classe'] ?? 'Non affecté' ?></td>
                                    <td>
                                        <!-- Modifier -->
                                        <form action="modifier_module.php" method="POST" class="d-inline">
                                            <input type="hidden" name="idmodule" value="<?= $mod['idmodule'] ?>">
                                            <button class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil-square"></i> Modifier
                                            </button>
                                        </form>
                                        <!-- Supprimer -->
                                        <form action="traitement/action.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce module ?');">
                                            <input type="hidden" name="idmodule" value="<?= $mod['idmodule'] ?>">
                                            <button type="submit" name="supprimermodule" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-muted fw-bold">Aucun module enregistré</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>