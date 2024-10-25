<?php
// Include the database connection
include 'Database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the user data from the database
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
}

if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role']; // Capture the role
    // Optional: Hash the password if the user wants to change it
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    // Update user in the database
    $sql = "UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $password, $role, $id); // Bind parameters

    if ($stmt->execute()) {
        header('Location: index.php'); // Redirect to index.php after updating
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .role {
            margin-bottom: 15px;
        }
        .role label {
            margin-right: 15px;
        }
    </style>
</head>
<body>

<h2>Edit User</h2>
<form action="edit.php?id=<?php echo $id; ?>" method="POST">
    <label>Name: </label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    
    <label>Email: </label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
    
    <label>Password: </label>
    <input type="password" name="password" placeholder="Leave blank to keep current password">
    
    <div class="role">
        <label>Role:</label>
        <label><input type="radio" name="role" value="Admin" <?php if ($user['role'] === 'Admin') echo 'checked'; ?> required> Admin</label>
        <label><input type="radio" name="role" value="User" <?php if ($user['role'] === 'User') echo 'checked'; ?> required> User</label>
    </div>
    
    <button type="submit" name="update">Update</button>
</form>

</body>
</html>
