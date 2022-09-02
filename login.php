<?php
    session_start();
    include("constants.php");
    include("functions.php");

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf_token'];

    if(isset($_SESSION['user_id']))
        unset($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div>
        <form method = "post">
            <div style = "margin:10px">Log-in</div>

            <p>Username: <input type = "text" name = "username"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['username'])){echo "Username is required!";}?></span>
            <p>Password: <input type = "password" name = "password"></p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['password'])){echo "Password is required!";}?></span>

            <input type = "hidden" name = "csrf_token" value = <?php echo $token ?>>
            <div class="g-recaptcha" data-sitekey=<?php echo $recaptcha_data_sitekey; ?>></div>
            <input type = "submit" name = "login">

            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST"){
                    if(isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] == $_POST['csrf_token']){
                        $username = htmlspecialchars(mysqli_real_escape_string($con,$_POST['username']));
                        $password = htmlspecialchars(mysqli_real_escape_string($con,$_POST['password']));

                        $recaptcha = check_recaptcha();
                        if(!$recaptcha){
                            echo "Please check the box to proceed!";
                        }
                        else{
                            if(!empty($username) && !empty($password)){
                                $query = "select * from users where username = '$username' limit 1";
                                $result = mysqli_query($con, $query);
                                if($result && mysqli_num_rows($result) > 0){
                                    $user_data = mysqli_fetch_assoc($result);
                                    if($user_data['password'] == $password){
                                        if($user_data['verified'] == 1){
                                            if($user_data['user_type'] == "user"){
                                                $_SESSION['user_id'] = $user_data['user_id'];
                                                header("Location: index.php");
                                            }
                                            if($user_data['user_type'] == "admin"){
                                                $_SESSION['user_id'] = $user_data['user_id'];
                                                header("Location: admin_page.php");
                                            }
                                        }
                                        else{
                                            echo "Account not validated!";
                                        }
                                    }
                                    else{
                                        echo "Incorrect password!";
                                    }
                                }
                                else{
                                    echo "Incorrect username!";
                                }
                                    
                            }
                            
                        }
                    }
                    else{
                        echo "Csrf token is not valid!";
                    }
                }
            ?>
            <br><br>
            <a href = "signup.php">Sign-up</a>
        </form>
    </div>
</body>
</html>