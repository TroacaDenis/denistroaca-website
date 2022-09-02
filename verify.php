<!DOCTYPE html>
<html>
<head>
    <title>validation</title>
</head>
<body>
<?php
    include("constants.php");
    include("functions.php");

    if(isset($_GET['mail']) && isset($_GET['vkey'])){
        $vkey = $_GET['vkey'];
        $email = $_GET['mail'];
        $query = "select * from users where vkey = '$vkey' and email = '$email' and verified = 0 limit 1";
        $result = mysqli_query($con, $query);
        if($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            $query = "update users set verified = 1 where vkey = '$vkey' and email = '$email' limit 1";
            mysqli_query($con, $query);
            echo "Validation complete. Proceed to ";
            ?>
            <a href="login.php">login.</a>
            <?php
        }
        else{
            echo "This account is not valid or already verified";
        }

    }
    else{
        die("Something went wrong.");
    }
?>
</body>
</html>