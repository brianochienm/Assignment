<?php
session_start();

// Include the database connection
require_once 'Dbconn.php';

// Initialize update message
$updateMessage = '';

// Check if the user is logged in and is a Super User
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Super_User') {
    header('Location: index.php');
    exit();
}

// Fetch existing user details from the database
$query = "SELECT * FROM users WHERE userId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['userId']]);
$userDetails = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Update Profile logic here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProfile'])) {
    $newFullName = $_POST['newFullName'];
    $newUserEmail = isset($_POST['newUserEmail']) ? $_POST['newUserEmail'] : $userDetails['email'];
    $newUserPhoneNumber = isset($_POST['newUserPhoneNumber']) ? $_POST['newUserPhoneNumber'] : $userDetails['phone_Number'];
    $newUserName = $_POST['newUserName']; // You might want to validate if you want to allow this field to be updated
    $newPassword = password_hash($_POST['newUserPassword'], PASSWORD_DEFAULT); // Hash the new password
    $newUserAddress = isset($_POST['newUserAddress']) ? $_POST['newUserAddress'] : $userDetails['Address'];

    // Assuming you have validated the inputs
    $updateQuery = "UPDATE users SET Full_Name = ?, email = ?, phone_Number = ?, Password = ?, Address = ? WHERE userId = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    if ($updateStmt->execute([$newFullName, $newUserEmail, $newUserPhoneNumber, $newPassword, $newUserAddress, $_SESSION['userId']])) {
        // Update successful, set the message
        $updateMessage = 'Profile updated successfully!';
    }

    // Refresh user details after the update
    $userDetails = [
        'Full_Name' => $newFullName,
        'email' => $newUserEmail,
        'phone_Number' => $newUserPhoneNumber,
        'User_Name' => $newUserName,
        'Password' => $newPassword,
        'Address' => $newUserAddress
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <style>
        /* Floating message style */
        .floating-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            display: none; /* Hide by default */
        }
    </style>
    <script>
        // JavaScript to show and hide the floating message
        document.addEventListener('DOMContentLoaded', function() {
            var updateMessage = '<?php echo $updateMessage; ?>';
            if (updateMessage !== '') {
                var floatingMessage = document.getElementById('floatingMessage');
                floatingMessage.textContent = updateMessage;
                floatingMessage.style.display = 'block';

                // Hide the message after 3 seconds
                setTimeout(function() {
                    floatingMessage.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</head>
<body>
    <div class="topnav"> 
        <a href="index.php">Home</a>  
        <div class="topnav-right">
            <a href="superuser1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>
    
    <!-- Your Update Profile HTML Form goes here -->
    <form action="update_profile.php" method="post">
        <h2>Update Profile</h2>
        <label for="newFullName">New Full Name:</label>
        <input type="text" id="newFullName" name="newFullName" value="<?php echo $userDetails['Full_Name']; ?>" required>

        <label for="newUserEmail">New User Email:</label>
        <input type="email" id="newUserEmail" name="newUserEmail" value="<?php echo $userDetails['email']; ?>" required>

        <label for="newUserPhoneNumber">New User phone Number:</label>
        <input type="text" id="newUserPhoneNumber" name="newUserPhoneNumber" value="<?php echo $userDetails['phone_Number']; ?>" required>

        <label for="newUserName">New User Name:</label>
        <input type="text" id="newUserName" name="newUserName" value="<?php echo $userDetails['User_Name']; ?>" required readonly>

        <label for="newUserPassword">New User Password:</label>
        <input type="password" id="newUserPassword" name="newUserPassword" required>

        <label for="newUserAddress">New User Address:</label>
        <input type="text" id="newUserAddress" name="newUserAddress" value="<?php echo $userDetails['Address']; ?>" required>

        <button type="submit" name="updateProfile">Update Profile</button>
    </form>

    <!-- Floating message container -->
    <div id="floatingMessage" class="floating-message"></div>
</body>
</html>





















