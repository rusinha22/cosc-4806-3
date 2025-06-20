<?php
// Handles user login requests
class Login extends Controller {
	// Shows the login form
    public function index() {		
	    $this->view('login/index');
    }
    
    public function verify(){
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
			
			$user = $this->model('User');
			$user->authenticate($username, $password); 
    }

}
