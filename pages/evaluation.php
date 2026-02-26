<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestion des Évaluations</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
<h1 class="text-center mb-4">Gestion des Évaluations</h1>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">Ajouter une Évaluation</div>
    <div class="card-body">
        <form action="traitement/action.php" method="POST">
            <div class="mb-3">
                <label for="etudiantEval" class="form-label">Matricule Étudiant</label>
                <input type="text" class="form-control" name="matricule" id="etudiantEval" required>
            </div>
            <div class="mb-3">
                <label for="moduleEval" class="form-label">Module</label>
                <input type="text" class="form-control" name="module" id="moduleEval" required>
            </div>
            <div class="mb-3">
                <label for="noteEval" class="form-label">Note</label>
                <input type="number" class="form-control" name="note" id="noteEval" min="0" max="20" step="0.01" required>
            </div>
            <button type="submit" name="ajouteevaluation" class="btn btn-success">Enregistrer Évaluation</button>
        </form>
    </div>
</div>
</div>
</body>
</html>
