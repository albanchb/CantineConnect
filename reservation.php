<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT classe, internat FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$internat = $user['internat'];

$sql = "SELECT date FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reserved_dates = [];
while ($row = $result->fetch_assoc()) {
    $reserved_dates[] = $row['date'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['reservation_date'];
    $repas_midi = $_POST['repas_midi'];
    $repas_soir = ($internat && isset($_POST['repas_soir'])) ? $_POST['repas_soir'] : NULL;

    $current_date = date("Y-m-d");
    if ($date < $current_date) {
        echo "<script>alert('Erreur : Vous ne pouvez pas réserver une date passée.');</script>";
    } elseif (in_array($date, $reserved_dates)) {
        echo "<script>alert('Erreur : Vous avez déjà réservé ce jour.');</script>";
    } else {
        $sql = "INSERT INTO reservations (user_id, date, repas_midi, repas_soir) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $date, $repas_midi, $repas_soir);

        if ($stmt->execute()) {
            $reserved_dates[] = $date;

            header("Location: " . $_SERVER['PHP_SELF'] . "?message=Merci pour votre réservation !");
            exit();
        } else {
            echo "<script>alert('Erreur lors de la réservation : " . $conn->error . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <title>Réservation de repas - CantineConnect</title>
     <style>
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #FF914D;
            padding: 15px 20px;
            text-align: center;
            color: white;
            font-size: 25px;
        }

        .container {
            flex: 1;
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #FF914D;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .warning-block {
            background-color: #FF5722;
            padding: 15px;
            border-left: 5px solid #D84315;
            margin-bottom: 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            text-align: center;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus, select:focus {
            border-color: #FF914D;
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(90deg, #FF914D 0%, #FF5722 100%);
            color: white;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background: linear-gradient(90deg, #FF5722 0%, #FF914D 100%);
        }

        .calendar {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .calendar th, .calendar td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            cursor: pointer;
        }

        .calendar th {
            background-color: #f4f4f4;
        }

        .reserved {
            background-color: #4CAF50;
            color: white;
        }

        .not-available {
            background-color: #FF6347;
            cursor: not-allowed;
        }

        .available {
            background-color: #FFFFFF; 
            color: #333;
        }

        footer {
            background-color: #FF914D;
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 14px;
            margin-top: auto;
        }

        .contact-info {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .credits {
            font-size: 12px;
            color: #FFDAB9;
        }

        @media (max-width: 768px) {
            header {
                font-size: 20px;
            }

            .container {
                width: 95%;
            }

            .calendar th, .calendar td {
                padding: 8px;
            }
        }

        @media (max-width: 414px) {
            header {
                font-size: 18px;
            }

            .container {
                padding: 15px;
            }

            button {
                font-size: 0.9em;
            }
        }
    </style>
</head>
</head>

<script>
   document.addEventListener("DOMContentLoaded", function() {
    let reservedDates = <?php echo json_encode($reserved_dates); ?>;
    let currentDate = new Date();
    currentDate.setHours(0, 0, 0, 0); 

    function createCalendar() {
        const calendar = document.getElementById("calendar");
        calendar.innerHTML = ""; 
        const date = new Date();
        const month = date.getMonth();
        const year = date.getFullYear();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const dayCount = lastDay.getDate();
        const daysInWeek = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
        let dayHTML = "<tr>";

        daysInWeek.forEach(day => {
            dayHTML += `<th>${day}</th>`;
        });
        dayHTML += "</tr><tr>";

        for (let i = 0; i < firstDay.getDay(); i++) {
            dayHTML += "<td></td>";
        }

        for (let day = 1; day <= dayCount; day++) {
            let dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
            let className = "";
            const currentDay = new Date(year, month, day);

            if (currentDay < currentDate) {
                className = "not-available"; 
            } else if (reservedDates.includes(dateStr)) {
                className = "reserved"; 
            } else if (currentDay.getDay() === 0 || currentDay.getDay() === 6) {
                className = "not-available";
            } else {
                className = "available"; 
            }

            dayHTML += `<td class="${className}" onclick="${className === 'available' ? `selectDate('${dateStr}')` : ''}">${day}</td>`;

            if ((firstDay.getDay() + day) % 7 === 0) {
                dayHTML += "</tr><tr>";
            }
        }

        for (let i = lastDay.getDay(); i < 6; i++) {
            dayHTML += "<td></td>";
        }

        dayHTML += "</tr>";
        calendar.innerHTML = dayHTML;
    }

    window.selectDate = function(dateStr) {
        document.getElementById("reservation_date").value = dateStr;
        document.getElementById("selected_date").innerText = `Date sélectionnée : ${dateStr}`;
        document.getElementById("modal").style.display = "none";
    };

    createCalendar();
});

</script>

<body>
    <header>
        CantineConnect - Réservez vos repas
    </header>

    <div class="container">
        <h2>Réservation de repas</h2>

        <div class="warning-block">
        Attention : Toute réservation effectuée moins de 24 heures avant le jour sélectionné ne sera pas prise en compte.
        </div>

        <table class="calendar" id="calendar"></table>

        <div id="selected_date" style="text-align: center; margin-top: 15px; font-weight: bold;">Sélectionnez une date dans le calendrier</div>
        <p>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="hidden" id="reservation_date" name="reservation_date" value="">

            <select name="repas_midi" id="repas_midi" required>
                <option value="viande">Viande</option>
                <option value="vegetarien">Végétarien</option>
                <option value="halal">Halal</option>

            </select>

            <?php if ($internat): ?>
            <select name="repas_soir" id="repas_soir">
                <option value="viande">Viande</option>
                <option value="vegetarien">Végétarien</option>
                <option value="halal">Halal</option>

            </select>
            <?php endif; ?>

            <button type="submit">Réserver</button>
        </form>
    </div>

    <footer>
        <div class="contact-info">Contactez-nous : support@cantineconnect.fr | +33 1 23 45 67 89</div>
        <div class="credits">© 2024 CantineConnect. Tous droits réservés.</div>
    </footer>
</body>
</html>
