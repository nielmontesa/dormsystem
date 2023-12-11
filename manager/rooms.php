<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$roomQuery = mysqli_query($con, 'SELECT * FROM rooms GROUP BY floornum');
$floors = [];

while ($row = mysqli_fetch_assoc($roomQuery)) {
    $floors[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link href="style.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        .main-container {
            margin-top: 2em;
            font-size: 16px;
        }

        .floor {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            gap: 1em;
        }

        .floor-name {
            background-color: #202020;
            color: white;
            padding: 2em;
            border-radius: 15px;
            font-family: sans-serif;
        }

        .room-details {
            padding: 2em;
            gap: 1em;
            display: flex;
            align-items: center;
            justify-content: center;

        }

        .room {
            padding: 2em;
            border-radius: 12px;
            font-family: sans-serif;
            border: 1px solid #333;
            background-color: #333;
            color: white;
        }

        .legend-vacant {
                        text-align: center;
            width: 10%;
            padding: 2em;
            border-radius: 12px;
            font-family: sans-serif;
            border: 1px solid #333;
            background-color: white; /* Darker background for vacant rooms */
            color: black;
        }

        .legend-nonvacant {
            text-align: center;
            width: 10%;
            padding: 2em;
            border-radius: 12px;
            font-family: sans-serif;
            border: 1px solid #333;
            background-color: #333;
            color: white;
        }

        /* Apply different background colors based on vacancy status */
        .vacant {
            background-color: white; /* Darker background for vacant rooms */
            color: black;
        }

        .legend-container {
            display: flex;
            gap: 2em;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="loggedin">
<nav class="navtop">
    <div>
        <h1>Room Available</h1>
        <a href="dashboard.php"><i class="fas fa-user-circle"></i>Dashboard</a>
        <a href="rooms.php"><i class="fas fa-user-circle"></i>Rooms</a>
        <a href="students.php"><i class="fas fa-user-circle"></i>Students</a>
        <a href="accounts.php"><i class="fas fa-user-circle"></i>Accounts</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
    </div>
</nav>

<div class="legend-container">
        <div class="legend-vacant">
            Vacant
        </div>
        <div class="legend-nonvacant">
            Non-Vacant
        </div>
    </div>

<div class="main-container">
    
    <?php foreach ($floors as $floor): ?>
        <div class="floor">
            <div class="floor-name">Floor <?=$floor['floornum']?></div>
            <div class="room-details">
                <?php
                $roomQuery = mysqli_query($con, 'SELECT * FROM rooms WHERE floornum = '.$floor['floornum']);
                while ($room = mysqli_fetch_assoc($roomQuery)) {
                    // Apply "vacant" class for vacant rooms
                    $roomClass = $room['vacancy'] === 'vacant' ? 'vacant' : '';
                    
                    echo '<div class="room '.$roomClass.'">';
                    echo '<p>Room Number: '.$room['roomnum'].'</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
