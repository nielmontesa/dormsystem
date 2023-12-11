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
$result = mysqli_query($con, 'SELECT * FROM tenants');

// We don't have the password or email info stored in sessions, so instead, we can get the results from the database.
$stmt = $con->prepare('SELECT password, email FROM tenants WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email);
$stmt->fetch();
$stmt->close();

// Function to add a new tenant
function addTenant($con, $username, $password, $email) {
    $stmt = $con->prepare('INSERT INTO tenants (username, password, email) VALUES (?, ?, ?)');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sss', $username, $hashedPassword, $email);
    $stmt->execute();
    $stmt->close();
}

// Function to archive a tenant (soft delete)
function archiveTenant($con, $tenantId) {
    $stmt = $con->prepare('UPDATE tenants SET archived = 1 WHERE id = ?');
    $stmt->bind_param('i', $tenantId);
    $stmt->execute();
    $stmt->close();
}

// Function to search for tenants by username
function searchTenants($con, $searchTerm) {
    $stmt = $con->prepare('SELECT * FROM tenants WHERE username LIKE ?');
    $searchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// Function to edit a tenant's information
function editTenant($con, $tenantId, $newUsername, $newPassword, $newEmail, $newRoomNum) {
    $stmt = $con->prepare('UPDATE tenants SET username = ?, password = ?, email = ?, roomnum = ? WHERE id = ?');
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt->bind_param('ssssi', $newUsername, $hashedPassword, $newEmail, $newRoomNum, $tenantId);
    $stmt->execute();
    $stmt->close();
}

// Check if the form for adding a new tenant is submitted
if (isset($_POST['add'])) {
    $username = $_POST['new_username'];
    $password = $_POST['new_password'];
    $email = $_POST['new_email'];
    $studid = $_POST['new_studid'];
    addTenant($con, $username, $password, $email, $studid);
}

// Check if the form for archiving a tenant is submitted
if (isset($_POST['archive'])) {
    $tenantIdToArchive = $_POST['archive_id'];
    archiveTenant($con, $tenantIdToArchive);
}

// Check if the form for searching tenants is submitted
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search_term'];
    $result = searchTenants($con, $searchTerm);
} else {
    // Default to displaying all tenants if no search term provided
    $result = $con->query('SELECT * FROM tenants');
}

// Check if the form for editing a tenant is submitted
if (isset($_POST['edit'])) {
    $tenantIdToEdit = $_POST['edit_id'];
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $newEmail = $_POST['new_email'];
    $newRoomNum = $_POST['roomnum'];
    editTenant($con, $tenantIdToEdit, $newUsername, $newPassword, $newEmail, $newRoomNum);
    
    // Redirect to the same page to see the updated data
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

?>

<!DOCTYPE html>
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
        <h2>Current Students</h2>

        <div>
        <div>
            <!-- Add Tenant Form -->
            <form method="post">
                <label for="new_username">Username:</label>
                <input type="text" name="new_username" required>
                <label for="new_password">Password:</label>
                <input type="password" name="new_password" required>
                <label for="new_email">Email:</label>
                <input type="email" name="new_email" required>
                <label for="new_school">School:</label>
                <input type="text" name="new_school" required>
                <label for="new_student_number">Student Number:</label>
                <input type="text" name="new_student_number" required>
                <button type="submit" name="add">Add Tenant</button>
            </form>

            <!-- Archive Tenant Form -->
            <form method="post">
                <label for="archive_id">Tenant ID to Archive:</label>
                <input type="number" name="archive_id" required>
                <button type="submit" name="archive">Archive Tenant</button>
            </form>

            <!-- Search Tenants Form -->
            <form method="post">
                <label for="search_term">Search Tenants:</label>
                <input type="text" name="search_term" required>
                <button type="submit" name="search">Search</button>
            </form>


            <!-- Display the 'tenants' table -->
            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Email</th>
                    <th>Room Number</th>
                    <th>Action</th>
                </tr>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['username']}</td>";
                    echo "<td>{$row['password']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['roomnum']}</td>"; // New column to display 'roomnum'
                    echo "<td>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='edit_id' value='{$row['id']}'>";
                    echo "<label for='new_username'>New Username:</label>";
                    echo "<input type='text' name='new_username' required>";
                    echo "<label for='new_password'>New Password:</label>";
                    echo "<input type='password' name='new_password' required>";
                    echo "<label for='new_email'>New Email:</label>";
                    echo "<input type='email' name='new_email' required>";
                    echo "<label for='roomnum'>Room Number:</label>";
                    echo "<select name='roomnum'>";
                    // Dropdown options
                    for ($i = 1; $i <= 5; $i++) {
                        for ($j = 1; $j <= 5; $j++) {
                            echo "<option value='{$i}-{$j}'>{$i}-{$j}</option>";
                        }
                    }
                    echo "</select>";
                    echo "<button type='submit' name='edit'>Edit</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>