<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

// Connect to the database
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Fetch the total number of records from the 'tenants' table
$totalStudentsQuery = mysqli_query($con, 'SELECT COUNT(*) as totalStudents FROM tenants');
$totalStudentsResult = mysqli_fetch_assoc($totalStudentsQuery);
$totalStudents = $totalStudentsResult['totalStudents'];

// Fetch the total number of records from the 'rooms' table
$totalRoomsQuery = mysqli_query($con, 'SELECT COUNT(*) as totalRooms FROM rooms');
$totalRoomsResult = mysqli_fetch_assoc($totalRoomsQuery);
$totalRooms = $totalRoomsResult['totalRooms'];

// Fetch the total number of unpaid students from the 'tenants' table
$totalUnpaidStudentsQuery = mysqli_query($con, 'SELECT COUNT(*) as totalUnpaidStudents FROM tenants WHERE ifpaid = 0');
$totalUnpaidStudentsResult = mysqli_fetch_assoc($totalUnpaidStudentsQuery);
$totalUnpaidStudents = $totalUnpaidStudentsResult['totalUnpaidStudents'];

// Fetch the total number of settled accounts from the 'tenants' table
$totalSettledAccountsQuery = mysqli_query($con, 'SELECT COUNT(*) as totalSettledAccounts FROM tenants WHERE ifpaid = 1');
$totalSettledAccountsResult = mysqli_fetch_assoc($totalSettledAccountsQuery);
$totalSettledAccounts = $totalSettledAccountsResult['totalSettledAccounts'];

// Fetch the total number of guests from the 'guests' table
$totalGuestsQuery = mysqli_query($con, 'SELECT COUNT(*) as totalGuests FROM guests');
$totalGuestsResult = mysqli_fetch_assoc($totalGuestsQuery);
$totalGuests = $totalGuestsResult['totalGuests'];

?>



<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Website Title</h1>
				<a href="dashboard.php"><i class="fas fa-user-circle"></i>Dashboard</a>
				<a href="rooms.php"><i class="fas fa-user-circle"></i>Rooms</a>
				<a href="students.php"><i class="fas fa-user-circle"></i>Students</a>
				<a href="accounts.php"><i class="fas fa-user-circle"></i>Accounts</a>
				<a href="#"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
		</div>

		<!-- Display the total number of rooms -->
		<div class="totalrooms">Total Rooms <label for=""><?=$totalRooms?></label></div>
		<div class="regstudents">Registered Students<label for=""> <?=$totalStudents?></label></div>
		<div class="studunpaid">Student Unpaid <label for=""><?=$totalUnpaidStudents?></label></div>
		<div class="settacc">Settled Accounts <label for=""><?=$totalSettledAccounts?></label></div>
		<div class="guests">Guests <label for=""><?=$totalGuests?></label></div>

	</body>
</html>
