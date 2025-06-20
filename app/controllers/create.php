<?php

class Create extends Controller {

    public function index() {
        $this->view('create/index');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $confirm  = $_POST['confirm'];

            // Validation: passwords must match
            if ($password !== $confirm) {
                echo "❌ Passwords do not match.";
                return;
            }

            // Validation: fields can't be empty
            if (empty($username) || empty($password)) {
                echo "❌ All fields are required.";
                return;
            }

            // Try to create user
            $user = $this->model('User');
            try {
                $user->createUser($username, $password);
               header("Location: /login");
               exit;

            } catch (Exception $e) {
                echo "❌ Registration failed: " . $e->getMessage();
                return;
            }
        }
    }
}
