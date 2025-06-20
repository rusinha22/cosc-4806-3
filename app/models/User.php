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

    // Authenticate user credentials and start session if valid
    public function authenticate($username, $password) {
        $username = strtolower($username);
        $db = db_connect();

        // Check if user is currently locked out
        if (isset($_SESSION['lockout']) && time() < $_SESSION['lockout']) {
            $wait = $_SESSION['lockout'] - time();
            echo "<p style='color:red;'>⏳ Account locked. Try again in {$wait} seconds.</p>";
            echo "<p><a href='/login'>Back to Login</a></p>";
            return;
        }

        $statement = $db->prepare("SELECT * FROM users WHERE username = :name;");
        $statement->bindValue(':name', $username);
        $statement->execute();
        $rows = $statement->fetch(PDO::FETCH_ASSOC);

        $loginStatus = 'bad';

        if ($rows && password_verify($password, $rows['password'])) {
            $_SESSION['auth'] = 1;
            $_SESSION['username'] = ucwords($username);
            unset($_SESSION['failedAuth']);
            unset($_SESSION['lockout']);
            $loginStatus = 'good';

            // Log successful attempt
            $logStmt = $db->prepare("INSERT INTO logins (username, attempt) VALUES (:username, :attempt)");
            $logStmt->execute([
                ':username' => $username,
                ':attempt' => $loginStatus
            ]);

            header('Location: /home');
            exit;

        } else {
            // Log failed attempt
            $logStmt = $db->prepare("INSERT INTO logins (username, attempt) VALUES (:username, :attempt)");
            $logStmt->execute([
                ':username' => $username,
                ':attempt' => $loginStatus
            ]);

            $_SESSION['failedAuth'] = ($_SESSION['failedAuth'] ?? 0) + 1;

            if ($_SESSION['failedAuth'] >= 3) {
                $_SESSION['lockout'] = time() + 60; // Lock for 60 seconds
                echo "<p style='color:red;'>❌ Too many failed attempts. You are locked out for 60 seconds.</p>";
                echo "<p><a href='/login'>Back to Login</a></p>";
            } else {
                echo "<p style='color:red;'>❌ Invalid credentials. Attempt {$_SESSION['failedAuth']} of 3.</p>";
                echo "<p><a href='/login'>Try again</a></p>";
            }

            return;
        }
    }

    // Create a new user account with hashed password
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

        try {
            $statement = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $hash);
            $statement->execute();

            echo "<p style='color:green;'>✅ Account created successfully!</p>";
            echo "<p><a href='/login'>Click here to log in</a></p>";
            return;

        } catch (PDOException $e) {
            echo "<p style='color:red;'>❌ Registration failed: " . $e->getMessage() . "</p>";
        }
    }
}
