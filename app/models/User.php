<?php

class User {

    public $username;
    public $password;
    public $auth = false;

    public function __construct() {}

    // Just fetches one user row (for debugging purposes)
    public function test() {
        $db = db_connect();
        $statement = $db->prepare("SELECT * FROM users;");
        $statement->execute();
        $rows = $statement->fetch(PDO::FETCH_ASSOC);
        return $rows;
    }

    // Authenticate user on logining in
    // Authenticates user credentials and starts session if valid
    // Verifies user credentials and starts session if valid

    public function authenticate($username, $password) {
        $username = strtolower($username);
        $db = db_connect();

        $statement = $db->prepare("SELECT * FROM users WHERE username = :name;");
        $statement->bindValue(':name', $username);
        $statement->execute();
        $rows = $statement->fetch(PDO::FETCH_ASSOC);

        if ($rows && password_verify($password, $rows['password'])) {
            $_SESSION['auth'] = 1;
            $_SESSION['username'] = ucwords($username);
            unset($_SESSION['failedAuth']);
            header('Location: /home');
            exit;
        } else {
            $_SESSION['failedAuth'] = ($_SESSION['failedAuth'] ?? 0) + 1;
            header('Location: /login');
            exit;
        }
    }

    // Create a new user in the database
    // Creates a new user account with hashed password

    public function createUser($username, $password) {
        $username = strtolower($username);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = db_connect();

        // Check if username already exists
        $checkStmt = $db->prepare("SELECT rupsin FROM users WHERE username = :username");
        $checkStmt->bindValue(':username', $username);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            echo "<p style='color:red;'>❌ Username already exists!</p>";
            echo "<p><a href='/create'>Go back to register</a></p>";
            return;
        }

        // Try inserting new user
        try {
            $statement = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $hash);
            $statement->execute();

            echo "<p style='color:green;'>✅ Account created successfully!</p>";
            echo "<p>Redirecting to login...</p>";
            header("Refresh: 3; URL=/login"); // Auto-redirect after 3 seconds
            exit;

        } catch (PDOException $e) {
            echo "<p style='color:red;'>❌ Registration failed: " . $e->getMessage() . "</p>";
        }
    }
}
