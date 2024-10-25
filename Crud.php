<?php
// Include the database connection
include 'Database.php';

if (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "User deleted successfully.";
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $role = $_POST['role']; // Capture the role

    // Insert user into the database
    $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $password, $role); // Bind the role

    if ($stmt->execute()) {
        // Redirect to the appropriate login form based on the user role
        if ($role === 'Admin') {
            header('Location: AdminLogin.php'); // Redirect to admin login
        } else {
            header('Location: UserLogin.php'); // Redirect to user login
        }
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch users from the database for the table display
$sql = "SELECT * FROM users";
$users = $conn->query($sql); // Make sure this query returns results before using $users
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration CRUD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h2 {
            color: #333;
        }

        form, table {
            width: 100%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .role {
            margin-bottom: 20px;
        }

        button {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #218838;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: center;
        }

        th {
            background-color: #28a745;
            color: white;
        }

        td a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        td a:hover {
            color: #0056b3;
        }

        .no-users {
            color: #ff6b6b;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Register New User</h2>
<form action="crud.php" method="POST">
    <label>Name: </label><input type="text" name="name" required><br>
    <label>Email: </label><input type="email" name="email" required><br>
    <label>Password: </label><input type="password" name="password" required><br>
    
    <div class="role">
        <label>Role:</label>
        <label><input type="radio" name="role" value="Admin" required> Admin</label>
        <label><input type="radio" name="role" value="User" required> User</label>
    </div>
    
    <button type="submit" name="register">Register</button>
</form>

<p>Already have an account? <a href="UserLogin.php">Log In</a>.</p>

<h2>Users List</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th> <!-- Added Role Column -->
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($users && $users->num_rows > 0): ?>
            <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['role']; ?></td> <!-- Display Role -->
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="#" onclick="deleteUser(<?php echo $row['id']; ?>)" style="color: #dc3545;">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="no-users">No users found.</td> <!-- Adjusted colspan -->
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
function deleteUser(id) {
    if (confirm("Are you sure you want to delete this user?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "crud.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    alert("User deleted successfully.");
                    location.reload(); // Reload the page to update the users list
                } else {
                    alert("Error deleting user: " + xhr.responseText);
                }
            }
        };
        xhr.send("delete=" + id); // Send the user ID to delete
    }
}
</script>

</body>
</html>
