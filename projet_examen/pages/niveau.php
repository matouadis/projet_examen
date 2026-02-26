<?php
// pages/niveau.php

require_once __DIR__ . "/../traitement/requette.php";

// Récupérer tous les niveaux
$niveaux = getNiveau();

// Messages de succès / erreur
$success = $_GET['success'] ?? null;
$erreur = $_GET['erreur'] ?? null;
?>

<div class="container mt-5">

    <h1 class="mb-4">📚 Gestion des Niveaux</h1>

    <!-- Messages -->
    <?php if($success === 'ajout'): ?>
        <div class="alert alert-success">✔ Niveau ajouté avec succès</div>
    <?php elseif($success === 'delete'): ?>
        <div class="alert alert-success">✔ Niveau supprimé avec succès</div>
    <?php elseif($success === 'modif'): ?>
        <div class="alert alert-success">✔ Niveau modifié avec succès</div>
    <?php endif; ?>

    <?php if($erreur === 'vide'): ?>
        <div class="alert alert-danger">❌ Veuillez saisir un nom de niveau</div>
    <?php elseif($erreur === 'existe'): ?>
        <div class="alert alert-danger">❌ Ce niveau existe déjà</div>
    <?php elseif($erreur === 'autre'): ?>
        <div class="alert alert-danger">❌ Une erreur est survenue</div>
    <?php endif; ?>

    <!-- Formulaire d'ajout -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">➕ Ajouter un Niveau</div>
        <div class="card-body">
            <form action="/projet_examen/traitement/action.php" method="POST" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="nom" class="form-control" placeholder="Nom du niveau" required>
                </div>
                <div class="col-md-6">
                    <button type="submit" name="ajouterniveau" class="btn btn-success w-100">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des niveaux -->
    <div class="card">
        <div class="card-header bg-dark text-white">Liste des Niveaux</div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($niveaux)): ?>
                        <?php foreach($niveaux as $n): ?>
                        <tr>
                            <td><?= $n['idniveau'] ?></td>
                            <td><?= htmlspecialchars($n['nom']) ?></td>
                            <td>
                                <!-- Supprimer -->
                                <form action="/projet_examen/traitement/action.php" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce niveau ?');">
                                    <input type="hidden" name="idniveau" value="<?= $n['idniveau'] ?>">
                                    <button type="submit" name="supprimerniveau" class="btn btn-danger btn-sm">Supprimer</button>
                                </form>
                                <!-- Modifier -->
                                <form action="/projet_examen/pages/modifier_niveau.php" method="POST" class="d-inline">
                                    <input type="hidden" name="idniveau" value="<?= $n['idniveau'] ?>">
                                    <input type="hidden" name="nom" value="<?= htmlspecialchars($n['nom']) ?>">
                                    <button type="submit" name="modifierniveau" class="btn btn-primary btn-sm">Modifier</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Aucun niveau enregistré</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>