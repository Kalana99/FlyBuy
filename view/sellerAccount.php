<?php 

include '../models/buyer.php';
include '../models/seller.php';
include '../database/db_connection.php';

require('../validators/product_validator.php');

function checknone($arr){

    foreach ($arr as $ele) {
        if ($ele != 'none') {
            return false;
        }
    }
    return true;
}

if(!isset($_COOKIE['user_login']) or !isset($_GET['id'])){      //if the cookie is not set redirect -> loginSignup
  header('Location: loginSignup.php');
}
else{

    $curr_email = $_COOKIE['user_login'];  //logged in user email
    
    $user  = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM sellers WHERE email = '$curr_email' LIMIT 1"), MYSQLI_ASSOC)[0];
    $products = mysqli_fetch_all( mysqli_query($conn, "SELECT * FROM  products"), MYSQLI_ASSOC);
    
    $add_itemName   = '';
    $add_amount   = '';
    $add_price   = '';
    $add_description   = '';

    if (isset($_POST['submitAddItem'])){

        $add_itemName = mysqli_real_escape_string($conn, $_POST['itemName']);
        $add_amount = mysqli_real_escape_string($conn, $_POST['amount']);
        $add_price = mysqli_real_escape_string($conn, $_POST['price']);
        $add_description = mysqli_real_escape_string($conn, $_POST['description']);
    
        $validation = new ProductValidator($_POST);
        $return_data    = $validation->validateForm('addItem');
        
        $addItemErrors = $return_data['errors'];
        $addItemClassNames = $return_data['classNames'];
        
        if(checknone($addItemErrors)){

            $seller_id = $_GET['id'];
            
            $sql = "INSERT INTO  products  (itemName,amount,price,description,seller_id) VALUES ('$add_itemName','$add_amount','$add_price','$add_description', '$seller_id')";
            
            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
                header('Location: sellerAccount.php?id='.$seller_id);
            }
            else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link 
        rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" 
        integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" 
        crossorigin="anonymous" 
    />
    
    <link rel="stylesheet" href="../css/styles_sellerAccount.css">
    <link rel="stylesheet" href="../css/styles_popup.css">
    <title>FlyBuy | Profile</title>
</head>
<body>

    <main>
        <nav>
            <a href="#" class="home">Home</a>
            <a href="#" class="notification">Notification</a>
            <a href="#" class="logout">Logout</a>
        </nav>

        <aside>
            <div class="header">
                <h3>Account Details</h3>
                <a href="#" class="user-edit-icon"><i class="fas fa-user-edit"></i></a>
            </div>
            <div class="img-div">
                <img src="../resources/user.png" alt="profile picture">
                <!-- <a href="#" class="edit-icon"><i class="fas fa-pen"></i></a> -->
            </div>
            <div class="store">COSMOS</div>
            <div class="name">akash tharuka</div>
            <div class="email">akash_tharuka@yahoo.</div>
            <div class="contact">071-569-4899</div>
            <div class="location">
                <div class="address-content">
                    <section class="street-no">No. 221/B</section>
                    <section class="street">Baker Street</section>
                    <section class="city">London</section>
                </div>
            </div>
        </aside>

        <section class="control-section">
            <a class="create-list" onclick="toggleDisplay()">New Item+</a>
            <div class="search-main">
                <div class="search-box-main">
                    <input class="search-txt-main" type="text" placeholder="search here...">
                    <a class="search-btn-main" href="#">
                        <i class="fas fa-search"></i>
                    </a>
                </div>
            </div>
        </section>

        <section class="item-container">
            <div class="item-details">
                <div class="item-name">
                    <div>Sugar 500g</div>
                    <small>Lorem ipsum dolor sit amet consectetur adipisicing elit.</small>
                </div>
                <div class="item-price">Rs. 500</div>
                <div class="item-amount">5</div>
                <div class="item-date-added">2021/11/10</div>
            </div>
        </section>

        <footer>footer</footer>
    </main>

    <!-- ------------------------------Popup window to add items-------------------------------------------- -->

    <div class="popup-window">

        <div class="overlay"></div>

        <div class="content">

            <div class="closeBtn" onclick="toggleDisplay()">&times;</div>

            <h1 class="title">Add Item</h1>

            <div class="items">

            </div>

            <form class="item-form" method="POST">
                <div class="input-field">
                    <i class="fas fa-archive"></i>
                    <input name="itemName" type="text" placeholder="Item Name" class="itemName">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error Message</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field">
                    <i class="fas fa-sort-numeric-up-alt"></i>
                    <input name="amount" type="number" placeholder="Amount" class="amount">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error Message</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field">
                    <i class="fas fa-dollar-sign"></i>
                    <input name="price" type="number" placeholder="Price" min="0.01" class="price">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error Message</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field">
                    <i class="fas fa-file-alt"></i>
                    <input name="description" type="text" placeholder="Description" class="description">
                    <!-- <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error Message</small>
                    </i> -->
                    <i class="fas fa-check-circle"></i>
                </div>

                <button type="submit" class="add-item btn" name="submitAddItem">Add</button>

            </form>

        </div>

    </div>
    
</body>

<script>
    function toggleDisplay(){
        let popupWindow = document.querySelector('.popup-window');
        popupWindow.classList.toggle('active');
    }
</script>

</html>