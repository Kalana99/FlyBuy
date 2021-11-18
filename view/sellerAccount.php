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

    if (count($_POST) > 0){
        
        $add_itemName = mysqli_real_escape_string($conn, $_POST['itemName']);
        $add_amount = mysqli_real_escape_string($conn, $_POST['amount']);
        $add_price = mysqli_real_escape_string($conn, $_POST['price']);
        $add_description = mysqli_real_escape_string($conn, $_POST['description']);
        
        $seller_id = $_GET['id'];
            
        $sql = "INSERT INTO  products  (itemName, amount, price, description, seller_id) VALUES ('$add_itemName', '$add_amount', '$add_price', '$add_description', '$seller_id')";
        
        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            header('Location: sellerAccount.php?id='.$seller_id);
        }
        else {
            echo "Error: " . $sql . "<br>" . $conn->error;
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
            <a href="#" class="logo">FlyBuy</a>
            <a href="#" class="home">Home</a>
            <a href="#" class="notification">Notification</a>
            <a href="#" onclick="toggleLogout()" class="logout">Logout</a>
        </nav>

        <aside>
            <div class="header">
                <h3 class="store"><?php echo $user['storeName']; ?></h3>
                <h3>
                    <i class="fa fa-star checked"></i>
                    <i class="fa fa-star checked"></i>
                    <i class="fa fa-star checked"></i>
                    <i class="fa fa-star"></i>
                    <i class="fa fa-star"></i>
                </h3>
                <a href="#" class="user-edit-icon"><i class="fas fa-user-edit"></i></a>
            </div>
            <div class="img-div">
                <img src="../resources/user.png" alt="profile picture">
                <!-- <a href="#" class="edit-icon"><i class="fas fa-pen"></i></a> -->
            </div>
            <div class="name"><?php echo $user['username']; ?>
                <label for="name" class="label label-name">Username</label>
            </div>
            <div class="email"><?php echo $user['email']; ?>
                <label for="name" class="label label-name">Email</label>
            </div>
            <div class="contact"><?php echo $user['telNo']; ?>
                <label for="name" class="label label-name">Telephone</label>
            </div>
            <div class="location"><?php echo $user['Address']; ?>
                <label for="name" class="label label-name">Address</label>
            </div>
        </aside>

        <section class="control-section">
            <div class="search">
                <div class="search-box">
                    <input class="search-txt" type="text" placeholder="search here...">
                    <a class="search-btn" href="#">
                        <i class="fas fa-search"></i>
                    </a>
                </div>
            </div>
            <a class="add-item" onclick="toggleDisplay()">New Item+</a>
        </section>

        <section class="item-container">
            <?php foreach ($products as $product): ?>
                <div class="item-details">
                    <div class="item-img"><img src="../resources/sugar500g.jpg" alt="item"></div>
                    <div class="item-name">
                        <div><?php echo $product['itemName']; ?></div>
                        <small><?php echo $product['description']; ?></small>
                    </div>
                    <div class="item-price"><?php echo "Rs. ".$product['price']; ?></div>
                    <div class="item-amount"><?php echo $product['amount']." Available"; ?></div>
                    <div class="item-date-added"><?php echo date('Y-m-d H:i:s', strtotime($product['created_at'])); ?>
                        <button class="item-edit-btn" onclick="toggleEdit()">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="item-delete-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
            
        </section>

        <footer>Copyright</footer>
    </main>

    <!-- ------------------------------Popup window to add items-------------------------------------------- -->

    <div class="popup-window addItem">

        <div class="overlay"></div>

        <div class="content">

            <div class="closeBtn" onclick="toggleDisplay()">&times;</div>

            <h1 class="title">Add Item</h1>

            <form class="item-form" id="item-form" method="POST">
                <div class="input-field addItem">
                    <i class="fas fa-archive"></i>
                    <input name="itemName" type="text" placeholder="Item Name" class="itemName">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field addItem">
                    <i class="fas fa-sort-numeric-up-alt"></i>
                    <input name="amount" type="number" placeholder="Amount" min="1" class="amount">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field addItem">
                    <i class="fas fa-dollar-sign"></i>
                    <input name="price" type="number" placeholder="Price" min="0.00" class="price">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field addItem">
                    <i class="fas fa-file-alt"></i>
                    <input name="description" type="text" placeholder="Description" class="description">
                    <i class="fas fa-check-circle"></i>
                </div>

                <input type="submit" class="add-item btn" value="Add">

            </form>

        </div>

    </div>

    <!-- ------------------------------Popup window to edit items------------------------------------------- -->

    <div class="popup-window editItem">

        <div class="overlay"></div>

        <div class="content">

            <div class="closeBtn" onclick="toggleEdit()">&times;</div>

            <h1 class="title">Edit Item</h1>

            <form class="item-form" id="item-form" method="POST">
                <div class="input-field editItem">
                    <i class="fas fa-archive"></i>
                    <input name="itemName" type="text" placeholder="Item Name" class="itemName">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field editItem">
                    <i class="fas fa-sort-numeric-up-alt"></i>
                    <input name="amount" type="number" placeholder="Amount" min="1" class="amount">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field editItem">
                    <i class="fas fa-dollar-sign"></i>
                    <input name="price" type="number" placeholder="Price" min="0.00" class="price">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field editItem">
                    <i class="fas fa-file-alt"></i>
                    <input name="description" type="text" placeholder="Description" class="description">
                    <i class="fas fa-check-circle"></i>
                </div>

                <input type="submit" class="edit-item btn" value="Edit">

            </form>

        </div>

    </div>

    <!-- -----------------------------Popup window to confirm logout--------------------------------------- -->

    <div class="popup-window logout">

        <div class="overlay"></div>

        <div class="content">

            <div class="closeBtn" onclick="toggleLogout()">&times;</div>

            <h1 class="title">Do you want to logout?</h1>

            <img src="../resources/warn.png" alt="warn.png" class="warn-img">

            <form action="#" method="post">
                <input type="submit" class="logout btn" value="Confirm">
            </form>

        </div>

    </div>

    <!-- ----------------------------Popup window for edit account details--------------------------------- -->

    <div class="popup-window editAccount">

        <div class="overlay"></div>

        <div class="content editAccount">

            <div class="closeBtn" onclick="toggleEdit()">&times;</div>

            <h1 class="title">Edit Account</h1>

            <form class="edit-form" id="edit-form" method="POST">
                <div class="input-field ">
                    <i class="fas fa-user"></i>
                    <input name="username" type="text" placeholder="Username" class="username">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field ">
                    <i class="fas fa-store"></i>
                    <input name="storeName" type="text" placeholder="Store name" class="storeName">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field ">
                    <i class="fas fa-mobile-alt"></i>
                    <input name="telNo" type="text" placeholder="Telephone" class="telNo">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field ">
                    <i class="fas fa-map-marked-alt"></i>
                    <input name="address" type="text" placeholder="Address" class="address">
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input name="password" type="password" placeholder="Password" class="psw">
                    <i class="fas fa-eye togglePassword"></i>
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="input-field <?php echo $signupClassNames['confirmPsw']; ?>">
                    <i class="fas fa-lock"></i>
                    <input name="confirmPsw" type="password" placeholder="Confirm Password" class="confirm-psw">
                    <i class="fas fa-eye togglePassword"></i>
                    <i class="fas fa-exclamation-circle tooltip">
                        <small class="tooltip-text">Error</small>
                    </i>
                    <i class="fas fa-check-circle"></i>
                </div>

                <input type="submit" class="edit btn" value="Edit">

            </form>

        </div>

    </div>
    
</body>

<script src="../javaScript/popupFormValidation.js"></script>

</html>