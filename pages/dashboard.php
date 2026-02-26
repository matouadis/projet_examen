<?php
require_once __DIR__ . "/../traitement/requette.php";

// ==========================
// STATISTIQUES GLOBALES
// ==========================
$total_classes = $pdo->query("SELECT COUNT(*) FROM classe")->fetchColumn();
$total_modules = $pdo->query("SELECT COUNT(*) FROM module")->fetchColumn();
$total_etudiants = $pdo->query("SELECT COUNT(*) FROM etudiant")->fetchColumn();

// Étudiants par classe
$par_classe = $pdo->query("
    SELECT c.nom AS classe, COUNT(e.idetudiant) AS total
    FROM classe c
    LEFT JOIN etudiant e ON e.idclasse = c.idclasse
    GROUP BY c.idclasse
")->fetchAll(PDO::FETCH_ASSOC);

// Étudiants + moyenne + statut
$etudiants = $pdo->query("
    SELECT e.idetudiant, e.nom, e.prenom, e.matricule, c.nom AS classe,
           COALESCE(AVG(ev.note), 0) AS moyenne
    FROM etudiant e
    LEFT JOIN classe c ON e.idclasse = c.idclasse
    LEFT JOIN evaluation ev ON e.idetudiant = ev.idetudiant
    GROUP BY e.idetudiant
")->fetchAll(PDO::FETCH_ASSOC);

$statuts = ['Admis'=>0,'Ajourné'=>0,'Exclus'=>0];
foreach($etudiants as &$etu){
    if($etu['moyenne'] >= 10){ $etu['statut']="Admis"; $statuts['Admis']++; }
    elseif($etu['moyenne'] >=5){ $etu['statut']="Ajourné"; $statuts['Ajourné']++; }
    else{ $etu['statut']="Exclus"; $statuts['Exclus']++; }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Académique Avancé</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<style>
body { background-color: #f8f9fa; }
.card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
</style>
</head>
<body>
<div class="container my-5">
<h2 class="text-center mb-4">📊 Dashboard Académique Avancé</h2>

<!-- Statistiques globales -->
<div class="row mb-4">
    <div class="col-md-4"><div class="card p-3 bg-primary text-white">Classes : <strong><?= $total_classes ?></strong></div></div>
    <div class="col-md-4"><div class="card p-3 bg-success text-white">Modules : <strong><?= $total_modules ?></strong></div></div>
    <div class="col-md-4"><div class="card p-3 bg-info text-white">Étudiants : <strong><?= $total_etudiants ?></strong></div></div>
</div>

<!-- Filtres -->
<div class="row mb-3">
    <div class="col-md-6">
        <label for="filterClasse" class="form-label">Filtrer par classe :</label>
        <select id="filterClasse" class="form-select">
            <option value="all">Toutes les classes</option>
            <?php foreach($par_classe as $c): ?>
                <option value="<?= $c['classe'] ?>"><?= $c['classe'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Graphiques -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Étudiants par Classe</h5>
            <canvas id="chartClasse" height="150"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h5>Étudiants par Statut</h5>
            <canvas id="chartStatut" height="150"></canvas>
        </div>
    </div>
</div>

<!-- Table des étudiants -->
<div class="card p-3">
    <h5>Liste des Étudiants</h5>
    <table class="table table-bordered" id="tableEtudiants" style="width:100%">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Classe</th>
                <th>Moyenne</th>
                <th>Statut</th>
                <th>Action</th>
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
                <td>
                    <a href="bulletin_pdf.php?matricule=<?= $etu['matricule'] ?>" 
                       class="btn btn-primary btn-sm" target="_blank">
                        Générer bulletin
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<script>
let chartClasse, chartStatut;
function drawCharts(filter='all') {
    let labelsClasse = [], dataClasse = [];
    <?php
    foreach($par_classe as $c){
        echo "labelsClasse.push('".$c['classe']."');";
        echo "dataClasse.push(".$c['total'].");";
    }
    ?>

    if(chartClasse) chartClasse.destroy();
    chartClasse = new Chart(document.getElementById('chartClasse').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelsClasse,
            datasets:[{
                label:'Nombre d\'étudiants',
                data:dataClasse,
                backgroundColor:'rgba(54, 162, 235, 0.7)'
            }]
        },
        options:{ responsive:true, plugins:{legend:{display:false}} }
    });

    let statutCounts = {Admis:0,Ajourné:0,Exclus:0};
    $('#tableEtudiants tbody tr').each(function(){
        let classeRow = $(this).find('td:eq(2)').text();
        let statutRow = $(this).find('td:eq(4)').text();
        if(filter==='all' || classeRow===filter) statutCounts[statutRow]++;
    });
    if(chartStatut) chartStatut.destroy();
    chartStatut = new Chart(document.getElementById('chartStatut').getContext('2d'), {
        type:'pie',
        data:{
            labels:['Admis','Ajourné','Exclus'],
            datasets:[{
                data:[statutCounts['Admis'],statutCounts['Ajourné'],statutCounts['Exclus']],
                backgroundColor:['#28a745','#ffc107','#dc3545']
            }]
        },
        options:{ responsive:true }
    });
}

$(document).ready(function(){
    let table = $('#tableEtudiants').DataTable({
        dom: 'Bfrtip',
        buttons: ['excelHtml5','pdfHtml5'],
        paging: true,
        pageLength: 10
    });

    $('#filterClasse').on('change', function(){
        let val = this.value;
        if(val==='all'){ table.rows().show(); }
        else{
            table.rows().every(function(){
                let classe = this.data()[2];
                $(this.node()).toggle(classe===val);
            });
        }
        drawCharts(val);
    });

    drawCharts();
});
</script>
</body>
</html>