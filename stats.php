<?php
require 'config.php';

$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$sql = "SELECT COUNT(*) as total_repas_midi FROM reservations WHERE repas_midi IS NOT NULL AND date = '$selected_date'";
$total_repas_midi = $conn->query($sql)->fetch_assoc()['total_repas_midi'];

$sql = "SELECT COUNT(*) as total_repas_soir FROM reservations WHERE repas_soir IS NOT NULL AND date = '$selected_date'";
$total_repas_soir = $conn->query($sql)->fetch_assoc()['total_repas_soir'];

$sql = "SELECT repas_midi, COUNT(*) as count FROM reservations WHERE repas_midi IS NOT NULL AND date = '$selected_date' GROUP BY repas_midi";
$result_midi = $conn->query($sql);
$repas_midi_counts = [];
while ($row = $result_midi->fetch_assoc()) {
    $repas_midi_counts[$row['repas_midi']] = $row['count'];
}

$sql = "SELECT repas_soir, COUNT(*) as count FROM reservations WHERE repas_soir IS NOT NULL AND date = '$selected_date' GROUP BY repas_soir";
$result_soir = $conn->query($sql);
$repas_soir_counts = [];
while ($row = $result_soir->fetch_assoc()) {
    $repas_soir_counts[$row['repas_soir']] = $row['count'];
}

$sql = "SELECT u.nom, u.prenom, u.classe, r.repas_midi, r.repas_soir 
        FROM users u 
        JOIN reservations r ON u.id = r.user_id 
        WHERE r.date = '$selected_date'
        ORDER BY u.classe, u.nom";
$result = $conn->query($sql);

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[$row['classe']][] = $row;
}

$dates = [];
for ($i = 0; $i < 7; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days"));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques des repas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        h2, h3, h4 {
            color: #4a4a4a;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card h4 {
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .navbar {
            background-color: #007BFF;
            padding: 10px;
            text-align: center;
        }

        .navbar a {
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            margin: 5px;
            border-radius: 5px;
        }

        .navbar a:hover {
            background-color: #0056b3;
        }

        .navbar a.active {
            background-color: #0056b3;
        }

        .date-select {
            display: none;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            margin: 10px 0;
        }

        @media (max-width: 768px) {
            .navbar {
                display: none;
            }

            .date-select {
                display: block;
            }
        }

        @media print {
            body {
                background-color: white;
            }

            .card {
                page-break-before: always;
            }

            button, .btn {
                display: none;
            }

            .navbar {
                display: none;
            }

            .date-select {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <?php foreach ($dates as $date) { ?>
        <a href="?date=<?php echo $date; ?>" class="<?php echo $selected_date == $date ? 'active' : ''; ?>">
            <?php echo date('d/m/Y', strtotime($date)); ?>
        </a>
    <?php } ?>
</div>

<form method="GET">
    <select name="date" class="date-select" onchange="this.form.submit()">
        <?php foreach ($dates as $date) { ?>
            <option value="<?php echo $date; ?>" <?php echo $selected_date == $date ? 'selected' : ''; ?>>
                <?php echo date('d/m/Y', strtotime($date)); ?>
            </option>
        <?php } ?>
    </select>
</form>

<div class="container">
    <h2>Statistiques des repas du <?php echo date('d/m/Y', strtotime($selected_date)); ?></h2>
    
    <div class="card">
        <h4>Total des repas réservés (midi) : <?php echo $total_repas_midi; ?></h4>
        <?php foreach ($repas_midi_counts as $type => $count) { ?>
            <p>Repas midi (<?php echo $type; ?>) : <?php echo $count; ?></p>
        <?php } ?>
    </div>

    <h3>Liste des élèves et leurs repas</h3>
    <div id="printableArea">
        <?php foreach ($classes as $classe => $eleves) { ?>
            <div class="card">
                <h4>Classe : <?php echo $classe; ?></h4>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Repas Midi</th>
                            <th>Repas Soir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eleves as $eleve) { ?>
                            <tr>
                                <td><?php echo $eleve['nom']; ?></td>
                                <td><?php echo $eleve['prenom']; ?></td>
                                <td><?php echo $eleve['repas_midi']; ?></td>
                                <td><?php echo $eleve['repas_soir']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>

    <form method="POST">
        <button type="submit" name="reset" class="btn btn-danger">Réinitialiser les données</button>
    </form>

    <button onclick="printDiv()" class="btn">Imprimer</button>
</div>

<script>
function printDiv() {
    var printContents = document.getElementById('printableArea').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>

</body>
</html>
