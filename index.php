<?php
    session_start();
    include("constants.php");
    include("functions.php");
    include("components.php");
    $user_data = check_login($con);
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online shop</title>
</head>
<body>
    <div>
        
        <?php

            if($user_data == null){
                ?>
                <a href = "signup.php">Sign-up</a>
                <a href = "login.php">Log-in</a>
                <br><br>
                <?php
            }
            else{
                echo "Welcome, " . $user_data['username'] . "!";
                ?>
                <br>
                <a href = "cart.php">View cart</a>
                <a href = "logout.php">Log-out</a>
                <br><br>
                <?php
            }
        ?>
    </div>
    <div>
        <?php
            $query = "select * from products";
            $result = mysqli_query($con, $query);

            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo home_product($row);
                }
            } 
            else {
                echo "0 results";
            }
        ?>

        <?php
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                if($user_data == null){
                    header("Location: login.php");
                }
                else{
                    add_to_cart($con, $user_data['user_id'], $_POST['prod_id']);
                }  
            }
        ?> 
    </div>

    
</body>
</html>