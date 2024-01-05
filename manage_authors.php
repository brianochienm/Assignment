<?php
session_start();

require_once 'Dbconn.php';

// Check if the user is logged in and is an Administrator
if (!isset($_SESSION['userId']) || $_SESSION['userType'] !== 'Administrator') {
    header('Location: index.php');
    exit();
}

$errors = [];
$successMessage = '';

// Handle Add Author
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addAuthor'])) {
    $authorName = filter_input(INPUT_POST, 'Full_Name', FILTER_SANITIZE_STRING);
    $authorEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $authorPhoneNumber = filter_input(INPUT_POST, 'phone_Number', FILTER_SANITIZE_STRING);
    $authorUserName = filter_input(INPUT_POST, 'User_Name', FILTER_SANITIZE_STRING);
    $authorPassword = password_hash(filter_input(INPUT_POST, 'Password', FILTER_SANITIZE_STRING), PASSWORD_DEFAULT);
    $authorAddress = filter_input(INPUT_POST, 'Address', FILTER_SANITIZE_STRING);

    // Validate input
    if (empty($authorName) || empty($authorEmail) || empty($authorPhoneNumber) || empty($authorUserName) || empty($authorPassword) || empty($authorAddress) || !filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid author details are required';
    }

    // If no errors, insert the new author
    if (empty($errors)) {
        try {
            $insertQuery = "INSERT INTO users (Full_Name, email, phone_Number, User_Name, Password, Address, UserType, AccessTime) VALUES (?, ?, ?, ?, ?, ?, 'Author', CURRENT_TIMESTAMP)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute([$authorName, $authorEmail, $authorPhoneNumber, $authorUserName, $authorPassword, $authorAddress]);
            $insertStmt->closeCursor();

            $successMessage = 'Author added successfully';
        } catch (PDOException $e) {
            $errors[] = 'Error adding author: ' . $e->getMessage();
        }
    }
}

// Fetch all authors for display
$query = "SELECT * FROM users WHERE UserType = 'Author'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Update Author
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateAuthor'])) {
    $selectedAuthorIdUpdate = $_POST['selectedAuthorIdUpdate'];

    // Validate author ID
    if (!is_numeric($selectedAuthorIdUpdate)) {
        $errors[] = 'Invalid author ID';
    }

    // If no errors, update the author details
    if (empty($errors)) {
        try {
            $newAuthorName = filter_input(INPUT_POST, 'newAuthorName', FILTER_SANITIZE_STRING);
            $newAuthorEmail = filter_input(INPUT_POST, 'newAuthorEmail', FILTER_SANITIZE_EMAIL);
            $newAuthorPhoneNumber = filter_input(INPUT_POST, 'newphone_Number', FILTER_SANITIZE_STRING);
            $newAuthorUserName = filter_input(INPUT_POST, 'newUser_Name', FILTER_SANITIZE_STRING);
            $newAuthorPassword = !empty($_POST['newPassword']) ? password_hash($_POST['newPassword'], PASSWORD_DEFAULT) : null;
            $newAuthorAddress = filter_input(INPUT_POST, 'newAddress', FILTER_SANITIZE_STRING);

            $updateQuery = "UPDATE users SET Full_Name = ?, email = ?, phone_Number = ?, User_Name = ?, Password = ?, Address = ? WHERE userId = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$newAuthorName, $newAuthorEmail, $newAuthorPhoneNumber, $newAuthorUserName, $newAuthorPassword, $newAuthorAddress, $selectedAuthorIdUpdate]);

            // Check if the update was successful
            if ($updateStmt->rowCount() > 0) {
                $successMessage = 'Author updated successfully!';
            } else {
                $errors[] = 'Author update failed. Please try again.';
            }
        } catch (PDOException $e) {
            $errors[] = 'Error updating author: ' . $e->getMessage();
        }
    }
}

// Handle Delete Author
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAuthor'])) {
    $authorIdToDelete = $_POST['selectedAuthorIdDelete'];

    // Validate author ID
    if (!is_numeric($authorIdToDelete)) {
        $errors[] = 'Invalid author ID';
    }

    // If no errors, delete the author
    if (empty($errors)) {
        try {
            $deleteQuery = "DELETE FROM users WHERE userId = ? AND UserType = 'Author'";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->execute([$authorIdToDelete]);
            $deleteStmt->closeCursor();

            $successMessage = 'Author deleted successfully';
        } catch (PDOException $e) {
            $errors[] = 'Error deleting author: ' . $e->getMessage();
        }
    }
}

// Include the HTML form for managing authors
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Authors</title>
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
            var successMessage = '<?php echo $successMessage; ?>';
            if (successMessage !== '') {
                var floatingMessage = document.getElementById('floatingMessage');
                floatingMessage.textContent = successMessage;
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
            <a href="admin1.php">Back</a>
            <a href="logout.php">Log out</a>
        </div>
    </div>

    <h2>Manage Authors</h2>

    <?php
    // Display success message or errors
    if (!empty($successMessage)) {
        echo '<p class="success-message">' . $successMessage . '</p>';
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<p class="error-message">' . $error . '</p>';
        }
    }
    ?>

    <!-- Add Author Form -->
    <form action="manage_authors.php" method="post">
        <label for="Full_Name">Author Name:</label>
        <input type="text" id="Full_Name" name="Full_Name" required>

        <label for="email">Author Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="phone_Number">Author Phone_Number:</label>
        <input type="text" id="phone_Number" name="phone_Number" required>

        <label for="User_Name">Author User_Name:</label>
        <input type="text" id="User_Name" name="User_Name" required>

        <label for="Password">Author password:</label>
        <input type="password" id="Password" name="Password" required>

        <label for="Address">Author Address:</label>
        <input type="text" id="Address" name="Address" required>

        <button type="submit" name="addAuthor">Add New Author</button>
    </form>


    <!-- Display authors list -->
    <h3>Authors List</h3>
    <?php if (!empty($authors)) : ?>
        <ul>
            <?php foreach ($authors as $author) : ?>
                <li>
                    <strong><?= $author['Full_Name']; ?></strong>
                    <p>Email: <?= $author['email']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No authors found.</p>
    <?php endif; ?>
  
    <!-- Update Author Form -->
    <h3>Update Author Details</h3>
    <form action="manage_authors.php" method="post">
        <label for="selectedAuthorIdUpdate">Select Author to Update:</label>
        <select id="selectedAuthorIdUpdate" name="selectedAuthorIdUpdate" required>
            <?php foreach ($authors as $author) : ?>
                <option value="<?= $author['userId']; ?>"><?= $author['Full_Name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Fetch and display existing values based on the selected author -->
        <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedAuthorIdUpdate'])) {
        $selectedAuthorIdUpdate = $_POST['selectedAuthorIdUpdate'];
        $selectedAuthor = array_filter($authors, function ($author) use ($selectedAuthorIdUpdate) {
            return $author['userId'] == $selectedAuthorIdUpdate;
        });

        if (!empty($selectedAuthor)) {
            $selectedAuthor = reset($selectedAuthor);
            ?>
            <label for="newAuthorName">New Author Name:</label>
            <input type="text" id="newAuthorName" name="newAuthorName" value="<?= $selectedAuthor['Full_Name']; ?>" required>

            <label for="newAuthorEmail">New Author Email:</label>
            <input type="email" id="newAuthorEmail" name="newAuthorEmail" value="<?= $selectedAuthor['email']; ?>" required>

            <label for="newphone_Number">New Author Phone_Number:</label>
            <input type="text" id="newphone_Number" name="newphone_Number" value="<?= $selectedAuthor['phone_Number']; ?>" required>

            <label for="newUser_Name">New Author User_Name:</label>
            <input type="text" id="newUser_Name" name="newUser_Name" value="<?= $selectedAuthor['User_Name']; ?>" required>

            <label for="newPassword">New Author password:</label>
            <input type="password" id="newPassword" name="newPassword" required>

            <label for="newAddress">New Author Address:</label>
            <input type="text" id="newAddress" name="newAddress" value="<?= $selectedAuthor['Address']; ?>" required>
        <?php } ?>
    <?php } ?>

        <button type="submit" name="updateAuthor">Update Author Details</button>
    </form>

    <!-- Delete Author Form -->
    <h3>Delete Author</h3>
<form action="manage_authors.php" method="post">
    <label for="selectedAuthorIdDelete">Select Author to Delete:</label>
    <select id="selectedAuthorIdDelete" name="selectedAuthorIdDelete" required>
        <?php foreach ($authors as $author) : ?>
            <option value="<?= $author['userId']; ?>"><?= $author['Full_Name']; ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit" name="deleteAuthor">Delete Author</button>
</form>
 

    <!-- Export Authors List -->
    <h3>Export Authors List</h3>
    <form action="export_authors.php" method="post">
        <button type="submit" name="exportPdf">Export to PDF</button><br><br>
        <button type="submit" name="exportTxt">Export to Text File</button><br><br>
        <button type="submit" name="exportExcel">Export to Excel</button><br><br>
    </form>
   

    <!-- Floating message -->
    <div id="floatingMessage" class="floating-message"></div>
</body>
</html>
