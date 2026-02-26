<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion des Classes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="text-center mb-4">Gestion des Classes</h1>

    <!-- Ajouter une Classe -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">Ajouter une Classe à un Niveau</div>
        <div class="card-body">
            <form action="traitement/action.php" method="POST">
                <div class="mb-3">
                    <label for="niveauClasse" class="form-label">Sélectionner le Niveau</label>
                    <select class="form-select" name="niveau" id="niveauClasse" required>
                        <option value="">-- Choisir un niveau --</option>
                        <option value="Licence 1">Licence 1</option>
                        <option value="Licence 2">Licence 2</option>
                        <option value="Master 1">Master 1</option>
                        <option value="Master 2">Master 2</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nomClasse" class="form-label">Nom de la Classe</label>
                    <input type="text" class="form-control" name="nom" id="nomClasse" placeholder="Ex: GL" required>
                </div>
                <button type="submit"name="ajouterclasse" class="btn btn-success">Ajouter Classe</button>
            </form>
        </div>
    </div>

    <!-- Afficher les Classes par Niveau -->
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">Liste des Classes par Niveau</div>
        <div class="card-body">
            <p class="text-muted">Les classes seront affichées dynamiquement après récupération côté serveur.</p>
            <!-- Exemple de liste statique (à remplacer par PHP/MySQL) -->
            <ul class="list-group">
                <li class="list-group-item"><strong>Licence 1 :</strong> GL, IAGE, CYBER</li>
                <li class="list-group-item"><strong>Licence 2 :</strong> GL2, IAGE2</li>
                <li class="list-group-item"><strong>Master 1 :</strong> M1-GL, M1-CYBER</li>
                <li class="list-group-item"><strong>Master 2 :</strong> M2-GL</li>
            </ul>
        </div>
    </div>

</div>
</body>
</html>
