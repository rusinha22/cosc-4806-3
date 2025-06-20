<?php

class Create extends Controller {

    // Shows the register form
    public function index() {
        $this->view('create/index');
    }

    // Handles form submission
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm  = $_POST['confirm'];

            if ($password !== $confirm) {
                echo "Passwords do not match.";
                return;
            }

            if (!empty($username) && !empty($password)) {
                $user = $this->model('User');
                $user->createUser($username, $password);
                header("Location: /login");
                exit;
            } else {
                echo "Please fill in all fields.";
            }
        }
    }
}
