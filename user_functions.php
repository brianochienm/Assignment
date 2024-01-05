<?php
// Include the database connection
require_once 'Dbconn.php';


// Function to add a new user
function handleAddUser()
{
    global $pdo;

    // Retrieve form data
    $newFullName = $_POST['newFullName'];
    $newUserEmail = $_POST['newUserEmail'];
    $newUserPhoneNumber = $_POST['newUserPhoneNumber'];
    $newUserName = $_POST['newUserName'];
    $newUserPassword = $_POST['newUserPassword'];
    $newUserType = $_POST['newUserType'];
    $newUserAddress = $_POST['newUserAddress'];

    // Hash the password (use password_hash in a real-world scenario)
    $hashedPassword = md5($newUserPassword);

    // Get the current timestamp
    $currentTime = date('Y-m-d H:i:s');

    // Insert new user into the database with access time
    $insertQuery = "INSERT INTO users (Full_Name, User_Name, email, phone_Number, Password, UserType, Address, accessTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $pdo->prepare($insertQuery);
    $success = $insertStmt->execute([$newFullName, $newUserName, $newUserEmail, $newUserPhoneNumber, $hashedPassword, $newUserType, $newUserAddress, $currentTime]);

    if ($success) {
        return 'User added successfully!';
    } else {
        return 'Error adding user. Please try again.';
    }
}


// Function to update user details
function handleUpdateUser()
{
    global $pdo;

    // Retrieve form data
    $selectedUserId = $_POST['selectedUserId'];

    // Check if the keys are set before accessing them
    $updatedFullName = isset($_POST['updatedFullName']) ? $_POST['updatedFullName'] : '';
    $updatedUserType = isset($_POST['updatedUserType']) ? $_POST['updatedUserType'] : '';

    // Update user details in the database
    $updateUserQuery = "UPDATE users SET Full_Name = ?, UserType = ? WHERE userId = ?";
    $updateUserStmt = $pdo->prepare($updateUserQuery);
    $success = $updateUserStmt->execute([$updatedFullName, $updatedUserType, $selectedUserId]);

    if ($success) {
        return 'User updated successfully!';
    } else {
        return 'Error updating user. Please try again.';
    }
}




// Function to delete user
function handleDeleteUser()
{
    global $pdo;

    // Retrieve form data
    $selectedUserId = $_POST['selectedUserIdDelete'];

    // Delete user from the database
    $deleteQuery = "DELETE FROM users WHERE userId = ?";
    $deleteStmt = $pdo->prepare($deleteQuery);
    $success = $deleteStmt->execute([$selectedUserId]);

    if ($success) {
        return 'User deleted successfully!';
    } else {
        return 'Error deleting user. Please try again.';
    }
}

// Additional functions for managing users can be added here
?>
