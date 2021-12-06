<?php
    session_start();
    include("connect.php");
    include("functions.php");

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
            ?>
            <form method = "post" style = "width:fit-content; text-align:center; border-style:double; float:left" >
                <p> <?php echo $row["name"] ?> </p> 
                <p> pret: <?php echo $row["price"] ?> lei </p> 
                <p> rating: <?php echo $row["avg_ratings"] ?>/10 </p> 
                <input type = "hidden" name = "prod_id" value = <?php echo $row["prod_id"] ?> >
                <input type = "submit" value = "Add to cart">
            </form>
            <?php
           
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
                die;
            }
            else{
                add_to_cart($con, $user_data['user_id'], $_POST['prod_id']);
            }  
        }
    ?>  
    </div>

    
</body>
</html>