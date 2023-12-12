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
	<script src="https://cdn.tailwindcss.com"></script>
	<meta charset="utf-8">
	<title>Home Page</title>
	<link href="style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
		integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
		crossorigin="anonymous" referrerpolicy="no-referrer">
</head>

<body class="loggedin p-12 flex gap-12">
	<nav class="navtop bg-gray-900 text-white p-12 rounded-lg ">
		<div class="flex flex-col gap-5 justify-between	h-full">
			<div>
				<p class="capitalize">
					<?= $_SESSION['name'] ?>
				</p>
				<p class="text-sm text-gray-300">Dorm Manager</p>
			</div>
			<div class="h-full mt-12">
				<ul class="flex flex-col gap-5">
					<li><a href="dashboard.php"
							class="text-gray-100 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-table-columns"></i>Dashboard</a></li>
					<li><a href="rooms.php"
							class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-door-closed"></i>Rooms</a></li>
					<li><a href="students.php"
							class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-people-roof"></i>Students</a></li>
				</ul>
			</div>
			<div class="flex flex-col gap-2">
				<ul>
					<li><a href="accounts.php"
							class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-money-bill"></i></i>Accounts</a></li>
					<li><a href="logout.php"
							class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
								class="fa-solid fa-right-from-bracket"></i></i>Logout</a></li>
				</ul>



			</div>
		</div>
	</nav>
	<div class="content  w-full h-full flex flex-col">
		<h2 class="text-5xl">Welcome to your dashboard!</h2>
		<p class="font-medium mt-3">This gives you an overview for the entire dormitory!</p>


		<!-- Display the total number of rooms -->
		<div class="dash grid grid-cols-3 gap-5 mt-4 h-full">
			<div
				class="totalrooms rounded-lg p-8 bg-gray-200 flex flex-col justify-between font-medium text-gray-400 hover:text-gray-700">
				<i class="fa-solid fa-door-open fa-2xl mt-2"></i>
				<div class="flex justify-between w-full text-xl">
					<h1 class="text-xl">Total Rooms</h1>
					<p class="text-xl" for="">
						<?= $totalRooms ?>
					</p>
				</div>
			</div>

			<div
				class="regstudents rounded-lg p-8 bg-gray-200 flex flex-col justify-between font-medium text-gray-400 hover:text-gray-700">
				<i class="fa-solid fa-person fa-2xl mt-2"></i>
				<div class="flex justify-between w-full text-xl">
					<h1 class="text-xl">Registered Students</h1>
					<p class="text-xl" for="">
						<?= $totalStudents ?>
					</p>
				</div>
			</div>

			<div
				class="studunpaid rounded-lg p-8 bg-gray-200 flex flex-col justify-between font-medium text-gray-400 hover:text-gray-700">
				<i class="fa-solid fa-person-circle-exclamation fa-2xl mt-2"></i>
				<div class="flex justify-between w-full text-xl">
					<h1 class="text-xl">Student Unpaid</h1>
					<p class="text-xl" for="">
						<?= $totalUnpaidStudents ?>
					</p>
				</div>
			</div>

			<div
				class="settacc rounded-lg p-8 bg-gray-200 flex flex-col justify-between font-medium text-gray-400 hover:text-gray-700">
				<i class="fa-solid fa-person-circle-check fa-2xl mt-2"></i>
				<div class="flex justify-between w-full text-xl">
					<h1 class="text-xl">Settled Accounts</h1>
					<p class="text-xl" for="">
						<?= $totalSettledAccounts ?>
					</p>
				</div>
			</div>

			<div
				class="guests rounded-lg p-8 bg-gray-200 flex flex-col justify-between font-medium text-gray-400 hover:text-gray-700">
				<i class="fa-solid fa-people-pulling fa-2xl mt-2"></i>
				<div class="flex justify-between w-full text-xl">
					<h1 class="text-xl">Guests</h1>
					<p class="text-xl" for="">
						<?= $totalGuests ?>
					</p>
				</div>
			</div>


		</div>
	</div </body>

</html>