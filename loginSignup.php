<?php
include 'templates/customer.php';
include 'templates/seller.php';
include 'database/db_connection.php';
require('user_validator.php');





$errors = [];

function checknone($arr)
{
    foreach ($arr as $ele) {
        if ($ele != 'none') {
            return false;
        }
    }
    return true;
}




if (isset($_POST['submitSignup'])){
      echo "dsf";
}
//print_r($_POST['submitSignup']);
if (isset($_POST['submitSignup'])) {


    if ($_POST['userType'] == "buyer") {

        //write query for all users
        $sql = 'SELECT * FROM customers';

        //make query and get result
        $result = mysqli_query($conn, $sql);

        //fetch the resulting rows as an array
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // validate entries
        $validation = new UserValidator($_POST, $users, "buyer");
        $errors = $validation->validateForm('signup');

        //array_filter($errors)

        if (checknone($errors)) {

            $sql = "INSERT INTO customers (username,email,password,telNo,address) VALUES ('$_POST[username]','$_POST[email]','$_POST[password]','$_POST[telNo]','$_POST[address]')";
            $errors = [];
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            print_r(array_values($errors));
        }
    } else {

        //write query for all users
        $sql = 'SELECT * FROM sellers';

        //make query and get result
        $result = mysqli_query($conn, $sql);

        //fetch the resulting rows as an array
        $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

        print_r($users);
        // validate entries
        $validation = new UserValidator($_POST, $users, "seller");
        $errors = $validation->validateForm('signup');



        if (checknone($errors)) {

            $sql = "INSERT INTO sellers (username,email,password,telNo,address,storeName) VALUES ('$_POST[username]','$_POST[email]','$_POST[password]','$_POST[telNo]','$_POST[address]','$_POST[storeName]')";
            $errors = [];
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
           print_r(array_values($errors));
       }
    }
}

if (isset($_POST['userTypeBuyer'])) {




    //write query for all users
    $sql = 'SELECT * FROM customers';

    //make query and get result
    $result = mysqli_query($conn, $sql);

    //fetch the resulting rows as an array
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // validate entries
    $validation = new UserValidator($_POST, $users, "buyer");
    $errors = $validation->validateForm('login');
   // print_r($users);
    array_filter($errors);

    if (checknone($errors)) {
        echo "Home";
     //   $_POST['userTypeBuyer']==null;
        //  header('Location: home.php');
    } else {
      print_r(array_values($errors));
  }
}


if (isset($_POST['userTypeSeller'])) {

    //write query for all users
    $sql = 'SELECT * FROM sellers';

    //make query and get result
    $result = mysqli_query($conn, $sql);

    //fetch the resulting rows as an array
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
   // print_r($users);

    // validate entries
    $validation = new UserValidator($_POST, $users, "seller");
    $errors = $validation->validateForm('login');



    if (checknone($errors)) {
        echo "Home";
        $_POST['userTypeSeller']=null;
        //  header('Location: home.php ');
    } else {
       print_r(array_values($errors));
    }
}
if(isset($_POST['userTypeBuyer']))

$conn->close();




















?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />

     <link rel="stylesheet" href="./css/styles_signinLogin.css">  

    <title>SignIn and SignUp</title>
</head>

<body>

    <main>

        <div class="column">

            <div class="container">

                <div class="forms-container">

                    <div class="signin-signup sign-in">

                        <form class="sign-in-form" method="post" action="loginSignup.php">

                            <h2 class="title">Sign in</h2>

                            <div class="input-field">
                                <i class="fas fa-envelope"></i>
                                <input name="email" type="text" placeholder="Email" class="email">
                                <i class="fas fa-exclamation-circle tooltip">
                                    <span class="tooltip-text">Error Message</span>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field">
                                <i class="fas fa-lock"></i>
                                <input name="password" type="password" placeholder="Password" class="psw">
                                <a href="forgotPsw.php"><small class="forgotPsw">forgotten password?</small></a>
                                <i class="fas fa-eye togglePassword"></i>
                                <i class="fas fa-exclamation-circle tooltip">
                                    <span class="tooltip-text">Error Message</span>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <button class="btn solid login buyer" name="userTypeBuyer" value="buyer">
                                <span class="buttonText">Login as a cutomer</span>
                            </button>

                            <button class="btn solid login seller" name="userTypeSeller" value="seller">
                                <span class="buttonText">Login as a seller</span>
                            </button>

                        </form>

                    </div>

                    <div class="signin-signup sign-up">

                        <form class="sign-up-form" method="post" action="loginSignup.php">

                            <h2 class="title">Sign up</h2>

                            <div class="input-field">
                                <i class="fas fa-user"></i>
                                <input name="username" type="text" placeholder="Username" class="username">
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field">
                                <i class="fas fa-envelope"></i>
                                <input name="email" type="email" placeholder="Email" class="email">
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field">
                                <i class="fas fa-mobile-alt"></i>
                                <input name="telNo" type="tel" placeholder="Phone Number" class="phone">
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field">
                                <i class="fas fa-map-marked-alt"></i>
                                <input name="address" type="text" placeholder="Address Ex:- No.20,city,county" class="address">
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field">
                                <i class="fas fa-lock"></i>
                                <input name="password" type="password" placeholder="Password" class="psw">
                                <i class="fas fa-eye togglePassword"></i>
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field">
                                <i class="fas fa-lock"></i>
                                <input name="confirmPsw" type="password" placeholder="Confirm Password" class="confirm-psw">
                                <i class="fas fa-eye togglePassword"></i>
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field store remove">
                                <i class="fas fa-store"></i>
                                <input name="storeName" type="text" placeholder="Store Name" class="store">
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <div class="input-field radio">
                                <input type="radio" class="radioBtn buyer" name="userType" value="buyer" onchange="removeField()" checked>
                                <label for="radio">Buyer</label>
                                <input type="radio" class="radioBtn seller" name="userType" value="seller" onchange="addField()">
                                <label for="radio">Seller</label>
                                <i class="fas fa-exclamation-circle tooltip">
                                    <small class="tooltip-text">Error Message</small>
                                </i>
                                <i class="fas fa-check-circle"></i>
                            </div>

                            <button class="btn solid signup" name="submitSignup">
                                <span class="buttonText">Sign Up</span>
                            </button>

                        </form>

                    </div>

                </div>

                <div class="panels-container">

                    <div class="panel left-panel">
                        <div class="content">
                            <h3>One of Us?</h3>
                            <p>
                                Sign in to your account from here
                            </p>
                            <button class="btn transparent" id="sign-in-button">
                                <span class="buttonText">Sign in</span>
                            </button>
                            <img src="user.png" alt="user">
                        </div>
                    </div>

                    <div class="panel right-panel">
                        <div class="content">
                            <img src="user.png" alt="user">
                            <h3>New Here?</h3>
                            <p>
                                Join us and enjoy the services
                            </p>
                            <button class="btn transparent" id="sign-up-button">
                                <span class="buttonText">Sign up</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

    <script src="./form.js"></script>
    
</body>

</html>