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
    <title>online shop</title>
</head>
<body>
    <div>
        
        <?php

            if($user_data == null){
                header("Location: login.php");
            }
            else{
                if($user_data['user_type'] != "admin"){
                    header("Location: index.php");
                }
                else{
                    echo "Welcome to the admin page!";
                    ?>
                    <br>
                    <a href = "logout.php">Log-out</a>
                    <br><br>
                    <?php
                }
            }
        ?>
    </div>

    
</body>
</html>