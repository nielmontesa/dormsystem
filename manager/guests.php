<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
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
// Fetch all rows from the 'tenants' table
$result = mysqli_query($con, 'SELECT * FROM guests');

// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM tenants WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();
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
                            class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
                                class="fa-solid fa-table-columns"></i>Dashboard</a></li>
                    <li><a href="rooms.php"
                            class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
                                class="fa-solid fa-door-closed"></i>Rooms</a></li>
                    <li><a href="students.php"
                            class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
                                class="fa-solid fa-people-roof"></i>Students</a></li>
                    <li><a href="guests.php"
                            class="text-gray-300 hover:text-blue-200 flex items-center justify-start gap-2"><i
                                class="fa-solid fa-people-pulling"></i></i>Guests</a></li>
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
    <div class="content w-full">
        <div class="h-full overflow-y-auto max-h-[100vh]">
            <table class="bg-gray-200 w-full h-full text-center border-collapse ">
                <tr>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">ID</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Guest Name</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Email</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Contact Number</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Checkin Time</th>
                    <!-- Add more columns as needed -->
                </tr>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td class='border border-gray-300 rounded-bl-lg'>{$row['id']}</td>";
                    echo "<td class='border border-gray-300'>{$row['guest_name']}</td>";
                    echo "<td class='border border-gray-300'>{$row['email']}</td>";
                    echo "<td class='border border-gray-300'>{$row['contact_number']}</td>";
                    echo "<td class='border border-gray-300 rounded-br-lg'>{$row['checkin_time']}</td>";
                    // Add more columns as needed
                    echo "</tr>";
                }
                ?>
            </table>


        </div>
    </div>
</body>

</html>