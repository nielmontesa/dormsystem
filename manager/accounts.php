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
function addTenant($con, $username, $password, $email, $school, $student_number)
{
    $stmt = $con->prepare('INSERT INTO tenants (username, password, email, studentid, school ) VALUES (?, ?, ?, ?, ?)');
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt->bind_param('sssss', $username, $hashedPassword, $email, $student_number, $school);
    $stmt->execute();
    $stmt->close();
}
// Function to archive a tenant (soft delete)
function archiveTenant($con, $tenantId)
{
    // Fetch the tenant data
    $stmtSelect = $con->prepare('SELECT * FROM tenants WHERE id = ?');
    $stmtSelect->bind_param('i', $tenantId);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();
    $tenantData = $result->fetch_assoc();
    $stmtSelect->close();

    // Insert the tenant data into the 'archived' table
    $stmtInsert = $con->prepare('INSERT INTO archived (id, username, password, email, studentid, school, roomnum) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmtInsert->bind_param('issssss', $tenantData['id'], $tenantData['username'], $tenantData['password'], $tenantData['email'], $tenantData['studentid'], $tenantData['school'], $tenantData['roomnum']);
    $stmtInsert->execute();
    $stmtInsert->close();

    // Delete the record from the 'tenants' table
    $stmtDelete = $con->prepare('DELETE FROM tenants WHERE id = ?');
    $stmtDelete->bind_param('i', $tenantId);
    $stmtDelete->execute();
    $stmtDelete->close();
}

// Function to search for tenants by username
function searchTenants($con, $searchTerm)
{
    $stmt = $con->prepare('SELECT * FROM tenants WHERE username LIKE ?');
    $searchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

// Function to edit a tenant's information
function editTenant($con, $tenantId, $newUsername, $newPassword, $newEmail, $newRoomNum)
{
    // Start building the query
    $query = 'UPDATE tenants SET';

    // Initialize an array to store the parameters and types for binding
    $params = [];
    $types = '';

    // Check if each field is provided and add it to the query
    if (!empty($newUsername)) {
        $query .= ' username = ?,';
        $params[] = $newUsername;
        $types .= 's';
    }

    if (!empty($newPassword)) {
        $query .= ' password = ?,';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $params[] = $hashedPassword;
        $types .= 's';
    }

    if (!empty($newEmail)) {
        $query .= ' email = ?,';
        $params[] = $newEmail;
        $types .= 's';
    }

    if (!empty($newRoomNum)) {
        $query .= ' roomnum = ?,';
        $params[] = $newRoomNum;
        $types .= 's';
    }

    // Remove the trailing comma
    $query = rtrim($query, ',');

    // Add the WHERE clause
    $query .= ' WHERE id = ?';
    $params[] = $tenantId;
    $types .= 'i';

    // Prepare and bind the parameters
    $stmt = $con->prepare($query);
    $stmt->bind_param($types, ...$params);

    // Execute the query
    $stmt->execute();

    // Close the statement
    $stmt->close();
}

// Check if the form for adding a new tenant is submitted
if (isset($_POST['add'])) {
    $username = $_POST['new_username'];
    $password = $_POST['new_password'];
    $email = $_POST['new_email'];
    $school = $_POST['new_school']; // Assuming your form has a field named 'new_school'
    $student_number = $_POST['new_student_number']; // Assuming your form has a field named 'new_student_number'
    addTenant($con, $username, $password, $email, $school, $student_number);
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
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

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

    <div class="content w-full h-full flex flex-col gap-2 text-xs">
        <div class="flex gap-2">
            <form method="post" class="rounded-lg bg-gray-200 p-4 text-xs flex flex-col gap-1 w-full">
                <h1 class="font-medium text-base">Add Tenant</h1>
                <label for="new_username" class="mt-1">Username: </label>
                <input type="text" name="new_username" class="rounded-lg" required>
                <label for="new_password" class="mt-1">Password: </label>
                <input type="password" name="new_password" class="rounded-lg" required>
                <label for="new_email" class="mt-1">Email: </label>
                <input type="email" name="new_email" class="rounded-lg" required>
                <label for="new_school" class="mt-1">School: </label>
                <input type="text" name="new_school" class="rounded-lg" required>
                <label for="new_student_number" class="mt-1">Student Number: </label>
                <input type="text" name="new_student_number" class="rounded-lg" required>
                <button type="submit" name="add"
                    class="bg-blue-700 rounded-full text-gray-100 hover:bg-blue-900 mt-4 py-2">Add Tenant</button>
            </form>
            <div class="flex flex-col">
                <form method="post" class="rounded-lg bg-gray-200 p-6 flex flex-col gap-2 h-full">
                    <h1 class="font-medium text-base">Archive Tenant</h1>
                    <label for="archive_id">Tenant ID to Archive:</label>
                    <input type="number" name="archive_id" required class="rounded-lg">
                    <button type="submit" name="archive"
                        class="bg-blue-700 rounded-full text-gray-100 hover:bg-blue-900 px-2 py-1">Archive
                        Tenant</button>
                </form>
                <form method="post" class="rounded-lg bg-gray-200 p-6 flex flex-col gap-2 h-full">
                    <h1 class="font-medium text-base">Search Tenant</h1>
                    <label for="search_term">Search Tenants:</label>
                    <input type="text" name="search_term" required class="rounded-lg">
                    <button type="submit" name="search"
                        class="bg-blue-700 rounded-full text-gray-100 hover:bg-blue-900 px-2 py-1">Search</button>
                </form>
            </div>
        </div>
        <div class="h-full overflow-y-auto over-flow-x-auto max-h-[100%] w-full rounded-lg ">
            <table class="bg-gray-200 w-full h-full text-center border-collapse">
                <tr>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">ID</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Username</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Password</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Email</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Room Number</th>
                    <th class="bg-gray-700 text-white p-2 px-4 border border-gray-600">Action</th>
                </tr>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td class='border border-gray-300 rounded-bl-lg text-xs'>{$row['id']}</td>";
                    echo "<td class='border border-gray-300 text-xs'>{$row['username']}</td>";
                    echo "<td class='border border-gray-300 text-xs'>{$row['password']}</td>";
                    echo "<td class='border border-gray-300 text-xs'>{$row['email']}</td>";
                    echo "<td class='border border-gray-300 text-xs'>{$row['roomnum']}</td>"; // Adjust the key accordingly
                    echo "<td class='border border-gray-300 rounded-br-lg text-xs'>";
                    echo "<form method='post' class='flex flex-col gap-2 p-2 text-xs'>";
                    echo "<input type='hidden' name='edit_id' value='{$row['id']}'>";
                    echo "<label for='new_username'>New Username:</label>";
                    echo "<input type='text' name='new_username' >";
                    echo "<label for='new_password'>New Password:</label>";
                    echo "<input type='password' name='new_password' >";
                    echo "<label for='new_email'>New Email:</label>";
                    echo "<input type='email' name='new_email' >";
                    echo "<label for='roomnum'>Room Number:</label>";
                    echo "<select name='roomnum'>";
                    // Dropdown options
                    for ($i = 1; $i <= 5; $i++) {
                        for ($j = 1; $j <= 5; $j++) {
                            echo "<option value='{$i}-{$j}'>{$i}-{$j}</option>";
                        }
                    }
                    echo "</select>";
                    echo "<button class='bg-blue-700 rounded-full text-gray-100 hover:bg-blue-900 mt-4 py-2' type='submit' name='edit'>Edit</button>";
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