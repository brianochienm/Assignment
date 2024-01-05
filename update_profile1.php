<?php
session_start();

// Include the database connection
require_once 'Dbconn.php';

function updateProfile($userId, $newFullName, $newEmail, $newPhone_Number, $newUser_Name, $newPassword, $newAddress, $pdo) {
    // Check if the new password is provided and not empty
    if (!empty($newPassword)) {
        // Update full name, email, phone number, user name, password, and address
        $updateQuery = "UPDATE users SET Full_Name = ?, email = ?, phone_Number = ?, User_Name = ?, Password = ?, Address = ? WHERE userId = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([$newFullName, $newEmail, $newPhone_Number, $newUser_Name, $newPassword, $newAddress, $userId]);
    } else {
        // Update full name, email, phone number, user name, and address (excluding password)
        $updateQuery = "UPDATE users SET Full_Name = ?, email = ?, phone_Number = ?, User_Name = ?, Address = ? WHERE userId = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute([$newFullName, $newEmail, $newPhone_Number, $newUser_Name, $newAddress, $userId]);
    }

    // Check if the update was successful
    if ($updateStmt->rowCount() > 0) {
        $_SESSION['updateMessage'] = 'Profile updated successfully!';
    } else {
        $_SESSION['updateMessage'] = 'Profile update failed. Please try again.';
    }
}

// Function to fetch administrator details
function getAdministratorDetails($userId, $pdo) {
    $query = "SELECT Full_Name, email, phone_Number, User_Name, Password, UserType, Address FROM users WHERE userId = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId]);
    $administratorDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    return $administratorDetails ? $administratorDetails : false;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated details from the form
    $newFullName = $_POST['newFullName'];
    $newEmail = $_POST['newEmail'];
    $newPhone_Number = $_POST['newPhone_Number'];
    $newUser_Name = $_POST['newUser_Name'];
    $newPassword = $_POST['newPassword'];
    $newAddress = $_POST['newAddress'];

    // Update the administrator's profile
    updateProfile($_SESSION['userId'], $newFullName, $newEmail, $newPhone_Number, $newUser_Name, $newPassword, $newAddress, $pdo);

    // Redirect to avoid form resubmission
    header('Location: update_profile1.php');
    exit();
}

// Fetch administrator details for pre-populating the form
$administratorDetails = getAdministratorDetails($_SESSION['userId'], $pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update administrator</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <style>
        .floating-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var updateMessage = '<?php echo isset($_SESSION['updateMessage']) ? $_SESSION['updateMessage'] : ''; ?>';
            if (updateMessage !== '') {
                var floatingMessage = document.getElementById('floatingMessage');
                floatingMessage.textContent = updateMessage;
                floatingMessage.style.display = 'block';

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
    
    <!-- Display the update profile form -->
    <h2>Update Profile</h2>
    <p>This section allows you to update your administrator profile information.</p>

    <form action="update_profile1.php" method="post">
        <label for="newFullName">New Full Name:</label>
        <input type="text" id="newFullName" name="newFullName" required value="<?= htmlspecialchars($administratorDetails['Full_Name']); ?>">

        <label for="newEmail">Email:</label>
        <input type="text" id="newEmail" name="newEmail" required value="<?= htmlspecialchars($administratorDetails['email']); ?>">

        <label for="newPhone_Number">Phone Number:</label>
        <input type="text" id="newPhone_Number" name="newPhone_Number" required value="<?= htmlspecialchars($administratorDetails['phone_Number']); ?>">

        <label for="newUser_Name">User Name:</label>
        <input type="text" id="newUser_Name" name="newUser_Name" required value="<?= htmlspecialchars($administratorDetails['User_Name']); ?>">

        <label for="newPassword">New Password:</label>
        <input type="password" id="newPassword" name="newPassword">

        <label for="newAddress">Address:</label>
        <input type="text" id="newAddress" name="newAddress" required value="<?= htmlspecialchars($administratorDetails['Address'] ?? ''); ?>">

        <button type="submit" name="updateProfile">Update Profile</button>
    </form>

    <!-- Floating message container -->
    <div id="floatingMessage" class="floating-message"></div>
</body>
</html>
