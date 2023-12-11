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
function addTenant($con, $username, $password, $email, $school, $studentNumber) {
    // Check if the student ID already exists
    $stmtCheck = $con->prepare('SELECT id FROM tenants WHERE studentid = ?');
    $stmtCheck->bind_param('s', $studentNumber);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        // Student ID already exists, handle the error (you can redirect or show an error message)
        echo "Error: Student ID already exists!";
    } else {
        // Student ID does not exist, proceed to insert the new record
        $stmtCheck->close();

        $stmt = $con->prepare('INSERT INTO tenants (username, password, email, school, studentid) VALUES (?, ?, ?, ?, ?)');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('sssss', $username, $hashedPassword, $email, $school, $studentNumber);
        $stmt->execute();
        $stmt->close();
    }
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
function editTenant($con, $tenantId, $newUsername, $newPassword, $newEmail) {
    $stmt = $con->prepare('UPDATE tenants SET username = ?, password = ?, email = ? WHERE id = ?');
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt->bind_param('sssi', $newUsername, $hashedPassword, $newEmail, $tenantId);
    $stmt->execute();
    $stmt->close();
}

// Check if the form for adding a new tenant is submitted
if (isset($_POST['add'])) {
    $username = $_POST['new_username'];
    $password = $_POST['new_password'];
    $email = $_POST['new_email'];
    $school = $_POST['new_school'];
    $studentNumber = $_POST['new_student_number'];
    addTenant($con, $username, $password, $email, $school, $studentNumber);
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
    editTenant($con, $tenantIdToEdit, $newUsername, $newPassword, $newEmail);
}

?>

<!DOCTYPE html>
<html>
    <head>
        <!-- ... (head section remains unchanged) -->
    </head>
    <body class="loggedin">
        <!-- ... (navigation section remains unchanged) -->

        <div class="content">
            <h2>Current Students</h2>
            <!-- ... (profile details section remains unchanged) -->

            <h2>All Tenants</h2>
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
                        <th>Student ID</th>
                        <th>School</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['username']}</td>";
                        echo "<td>{$row['password']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['studentid']}</td>";
                        echo "<td>{$row['school']}</td>";
                        // Add more columns as needed
                        echo "<td>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='edit_id' value='{$row['id']}'>";
                        echo "<label for='new_username'>New Username:</label>";
                        echo "<input type='text' name='new_username' required>";
                        echo "<label for='new_password'>New Password:</label>";
                        echo "<input type='password' name='new_password' required>";
                        echo "<label for='new_email'>New Email:</label>";
                        echo "<input type='email' name='new_email' required>";
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