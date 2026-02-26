
<div class="container my-5">
    <h1 class="text-center mb-4">Gestion des Étudiants</h1>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Ajouter un Étudiant</div>
        <div class="card-body">
            <form action="traitement/action.php" method="POST">
                <div class="mb-3">
                    <label for="nomEtudiant" class="form-label">Nom</label>
                    <input type="text" class="form-control" name="nom" id="nomEtudiant" required>
                </div>
                
                <input type="hidden" class="form-control" name="action" value="ajouter_etudiant">
                <div class="mb-3">
                    <label for="prenomEtudiant" class="form-label">Prénom</label>
                    <input type="text" class="form-control" name="prenom" id="prenomEtudiant" required>
                </div>
                <div class="mb-3">
                    <label for="nomEtudiant" class="form-label">matricule</label>
                    <input type="text" class="form-control" name="matricule" required>
                </div>
                <div class="mb-3">
                    <label for="classeEtudiant" class="form-label">Classe</label>
                    <select class="form-select" name="idclasse" id="classeEtudiant" required>
                        <option value="">-- Choisir une classe --</option>
                        <?php foreach($classes as $classe):?>
                                <option value="<?= $classe['idclasse'] ?>"><?= $classe['nom'] ?></option>
                            <?php endforeach?>
                    </select>
                </div>
                <button type="submit"name="ajouteretudiant" class="btn btn-success">Ajouter Étudiant</button>
            </form>
        </div>
    </div>
</div>

