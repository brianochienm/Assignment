<?php
// Start the session to access session variables
session_start();

// Include the database connection
require_once 'Dbconn.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="CSS/style.css" />
</head>

<body>
    <!-- Navigation bar -->
    <div class="topnav">
        <a href="index.php">Home</a>
        <div class="topnav-right">
            <a href="superuser1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <h1>Manage Users</h1>
<!-- Add New User Form -->
<form action="manage_users.php" method="post">
    <h2>Add New User</h2>

    <!-- Input fields for new user -->
    <label for="newFullName">New Full Name:</label>
    <input type="text" id="newFullName" name="newFullName" required>

    <label for="newUserEmail">New User Email:</label>
    <input type="email" id="newUserEmail" name="newUserEmail" required>

    <label for="newUserPhoneNumber">New User phone Number:</label>
    <input type="text" id="newUserPhoneNumber" name="newUserPhoneNumber" required>

    <label for="newUserName">New User Name:</label>
    <input type="text" id="newUserName" name="newUserName" required>

    <label for="newUserPassword">New User Password:</label>
    <input type="password" id="newUserPassword" name="newUserPassword" required>

    <label for="newUserType">New User Type:</label>
    <select id="newUserType" name="newUserType" required>
        <option value="Administrator">Administrator</option>
        <option value="Author">Author</option>
        <!-- Add other user types as needed -->
    </select>

    <label for="newUserAddress">New User Address:</label>
    <input type="text" id="newUserAddress" name="newUserAddress" required>

    <!-- Submit button for adding a new user -->
    <button type="submit" name="addUser">Add New User</button>
</form>

<!-- Update User Details Form -->
<form action="manage_users.php" method="post">
    <h2>Update User Details</h2>

    <!-- Select dropdown for choosing a user to update -->
    <label for="selectedUserId">Select User:</label>
    <select id="selectedUserId" name="selectedUserId" required>
        <?php
        // Fetch all users from the database
        $query = "SELECT * FROM users";
        $result = $pdo->query($query);

        // Check if there are users
        $users = $result->rowCount() > 0 ? $result->fetchAll(PDO::FETCH_ASSOC) : [];

        // Display each user as an option in the dropdown
        foreach ($users as $user) :
        ?>
            <option value="<?php echo $user['userId']; ?>"><?php echo $user['User_Name']; ?></option>
        <?php endforeach; ?>
    </select>

    <?php
    // Display user details in the form if a user is selected
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedUserId'])) {
        $selectedUserId = $_POST['selectedUserId'];
        $selectedUserDetailsQuery = "SELECT * FROM users WHERE userId = ?";
        $selectedUserDetailsStmt = $pdo->prepare($selectedUserDetailsQuery);
        $selectedUserDetailsStmt->execute([$selectedUserId]);
        $selectedUserDetails = $selectedUserDetailsStmt->fetch(PDO::FETCH_ASSOC);
    ?>
        <!-- Input fields for updating user details -->
        <label for="updatedFullName">Updated Full Name:</label>
        <input type="text" id="updatedFullName" name="updatedFullName" value="<?php echo $selectedUserDetails['Full_Name']; ?>" required>

        <label for="updatedUserType">Updated User Type:</label>
        <select id="updatedUserType" name="updatedUserType" required>
            <option value="Administrator" <?php echo ($selectedUserDetails['UserType'] == 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
            <option value="Author" <?php echo ($selectedUserDetails['UserType'] == 'Author') ? 'selected' : ''; ?>>Author</option>
            <!-- Add other user types as needed -->
        </select>

        <!-- Additional fields -->
        <label for="updatedUserEmail">Updated User Email:</label>
        <input type="email" id="updatedUserEmail" name="updatedUserEmail" value="<?php echo $selectedUserDetails['email']; ?>" required>

        <label for="updatedUserPhoneNumber">Updated User Phone Number:</label>
        <input type="text" id="updatedUserPhoneNumber" name="updatedUserPhoneNumber" value="<?php echo $selectedUserDetails['phone_Number']; ?>" required>

        <label for="updatedUserName">Updated User Name:</label>
        <input type="text" id="updatedUserName" name="updatedUserName" value="<?php echo $selectedUserDetails['User_Name']; ?>" required>

        <label for="updatedUserPassword">Updated User Password:</label>
        <input type="password" id="updatedUserPassword" name="updatedUserPassword">

        <label for="updatedUserAddress">Updated User Address:</label>
        <input type="text" id="updatedUserAddress" name="updatedUserAddress" value="<?php echo $selectedUserDetails['Address']; ?>" required>
    <?php
    }
    ?>

    <!-- Submit button for updating user details -->
    <button type="submit" name="updateUser">Update User Details</button>
</form>



    <!-- Delete User Form -->
    <form action="manage_users.php" method="post">
        <h2>Delete User</h2>

        <!-- Select dropdown for choosing a user to delete -->
        <label for="selectedUserIdDelete">Select User to Delete:</label>
        <select id="selectedUserIdDelete" name="selectedUserIdDelete" required>
            <?php
            // Display each user as an option in the dropdown
            foreach ($users as $user) :
            ?>
                <option value="<?php echo $user['userId']; ?>"><?php echo $user['User_Name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Submit button for deleting a user -->
        <button type="submit" name="deleteUser">Delete User</button>
    </form>

    <!-- Export Users List Form -->
    <h3>Export Users List</h3>
    <form action="export_users.php" method="post">
        <!-- Buttons for exporting to PDF, Text, and Excel -->
        <button type="submit" name="exportPdf">Export to PDF</button><br><br>
        <button type="submit" name="exportTxt">Export to Text File</button><br><br>
        <button type="submit" name="exportExcel">Export to Excel</button><br><br>
    </form>
</body>

</html>

<?php
// Handle Add New User Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
    handleAddUser();
}

// Handle Update User Details Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser'])) {
    handleUpdateUser();
}

// Handle Delete User Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteUser'])) {
    handleDeleteUser();
}

// Include export logic after form processing
include_once 'export_users.php';
// Function to handle Add New User
function handleAddUser()
{
    global $pdo;

    $newFullName = $_POST['newFullName'];
    $newUserEmail = $_POST['newUserEmail'];
    $newUserPhoneNumber = $_POST['newUserPhoneNumber'];
    $newUserName = $_POST['newUserName'];
    $newUserPassword = $_POST['newUserPassword'];
    $newUserType = $_POST['newUserType'];
    $newUserAddress = $_POST['newUserAddress'];

    // Validate input if needed

    // Hash the password (use password_hash in a real-world scenario)
    $hashedPassword = md5($newUserPassword);

    $insertQuery = "INSERT INTO users (Full_Name, User_Name, email, phone_Number, Password, UserType, Address) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->execute([$newFullName, $newUserName, $newUserEmail, $newUserPhoneNumber, $hashedPassword, $newUserType, $newUserAddress]);
}


// Function to handle Update User Details
function handleUpdateUser()
{
    global $pdo;

    $selectedUserId = $_POST['selectedUserId'];
    $updatedFullName = $_POST['updatedFullName'];
    $updatedUserType = $_POST['updatedUserType'];

    // Validate input if needed

    $updateUserQuery = "UPDATE users SET Full_Name = ?, UserType = ? WHERE userId = ?";
    $updateUserStmt = $pdo->prepare($updateUserQuery);
    $updateUserStmt->execute([$updatedFullName, $updatedUserType, $selectedUserId]);
}

// Function to handle Delete User
function handleDeleteUser()
{
    global $pdo;

    $selectedUserId = $_POST['selectedUserIdDelete'];

    // Validate input if needed

    $deleteQuery = "DELETE FROM users WHERE userId = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $deleteStmt->execute([$selectedUserId]);
}
?>








