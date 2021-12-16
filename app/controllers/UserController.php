<?php

class UserController extends Controller {

    private $isValid = true;

    public function __construct(){}

    public function register() {

        if(isset($_POST['submitSignup'])){

            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $userType = $_POST['userType'];

            if ($userType == 'buyer'){
                $this->userModel = $this->model('Buyer');
            }
            else{
                $this->userModel = $this->model('Seller');
            }

            $emailExists = $this->userModel->checkEmailExistence($_POST['email']);

            $signupValidator = new SignupValidator($_POST, $emailExists, $userType);

            $data = $signupValidator->validateForm();

            foreach ($data['signupErrors'] as $field => $errorValue) {
                if ($errorValue != 'none' and $errorValue != ''){
                    $this->isValid = false;
                    break;
                }
            }

            if ($this->isValid){
                //Register user from model function
                if ($this->userModel->register($data)) {

                    $additionalData  = ['vkey' => $data['vkey'], 'table' => $userType];
                    $email = $_POST['email'];

                    $path = URLROOT . '/PageController/emailVerified';
                    sendMail($email, 'signup', $additionalData, $path);
                    header('location: ' . URLROOT . '/PageController/verifyEmail/' . $userType . '/' . $data['vkey']);
                } 
                else {
                    die('Something went wrong.');
                }
            }
            else{
                $this->view('pages/loginSignup', $data);
            }
        }
    }

    public function login() {
        
        if(isset($_POST['submitLogin'])){

            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $userType = $_POST['submitLogin'];
            
            if ($userType == 'buyer'){
                $this->userModel = $this->model('Buyer');
            }
            else{
                $this->userModel = $this->model('Seller');
            }
            
            $users = $this->userModel->findAllUsers();
            
            $loginValidator = new LoginValidator($_POST, $users, $userType);

            $data = $loginValidator->validateForm($userType);

            foreach ($data['loginErrors'] as $field => $errorValue) {
                if ($errorValue != 'none' and $errorValue != ''){
                    $this->isValid = false;
                    break;
                }
            }

            if ($this->isValid){
                
                setcookie('user_login', $data['loginData']['email'], time() + 86400, "/");
                $id = $this->userModel->login($data);

                if($userType === 'buyer'){
                    header('location: ' . URLROOT . '/PageController/buyerAccount/' . $id);
                }
                else{
                    header('location: ' . URLROOT . '/PageController/sellerAccount/' . $id);
                }
            }
            else{
                $this->view('pages/loginSignup', $data);
            }
        }
    }

    /*
    create common edit method which takes usertype
    create model according to type
    call editProfile from model
    check for seller_id and buyer_id
    */
    public function editProfile(){
        
        if(isset($_POST['seller_id'])){
            $this->userModel = $this->model('Seller');
            $id = $_POST['seller_id'];
            $userType = 'seller';
        }
        else{
            $this->userModel = $this->model('Buyer');
            $id = $_POST['buyer_id'];
            $userType = 'buyer';
        }

        $user = $this->userModel->findUserById($id);

        $editProfileValidator = new EditProfileValidator($_POST, $userType);

        $data = $editProfileValidator->validateForm();
        $data['user'] = $user;
        
        foreach ($data['editProfileErrors'] as $field => $errorValue) {
            if ($errorValue != 'none' and $errorValue != ''){
                $this->isValid = false;
                break;
            }
        }

        if ($this->isValid){
            
            $data['editProfileData']['vkey'] = $user->vkey;
            $this->userModel->updateUserData($data['editProfileData']);

            if(!empty($data['editProfileData']['password'])){ // need to logout the user
                $this->view('pages/loginSignup');
            }
            else if($userType === 'buyer'){
                header('location: ' . URLROOT . '/PageController/buyerAccount/' . $id);
            }
            else{
                header('location: ' . URLROOT . '/PageController/sellerAccount/' . $id);
            }
        }
        else{

            if($userType === 'buyer'){
                // $data['buyer_id'] = $id;
                // $this->view('pages/editBuyerAccount', $data);
            }
            else{
                $data['seller_id'] = $id;
                $this->view('pages/editSellerAccount', $data);
            }
        }
    }














    public function logout(){

        if(isset($_POST['submitLogout'])){

            if (isset($_COOKIE['user_login'])) {

                unset($_COOKIE['user_login']); 
                setcookie('user_login', null, -1, '/');
            }
            
            header('location: ' . URLROOT . '/PageController/loginSignup');
        }
    }

    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['email'] = $user->email;
        header('location:' . URLROOT . '/pages/index');
    }

    public function logout_temp() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        header('location:' . URLROOT . '/users/login');
    }
}