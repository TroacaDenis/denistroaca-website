<?php
    session_start();

    include("constants.php");
    include("functions.php");

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf_token'];

    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div>
        <form method = "post">
            <div style = "margin:10px">Sign-up</div>

            <p>Email: <input type = "email" name = "email"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['email'])){echo "Email is required!";}?></span>
            <p>Username: <input type = "text" name = "username"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['username'])){echo "Username is required!";}?></span>
            <p>Password: <input type = "password" name = "password"></p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['password'])){echo "Password is required!";}?></span>
            
            <input type = "hidden" name = "csrf_token" value = <?php echo $token ?>>
            <div class="g-recaptcha" data-sitekey=<?php echo $recaptcha_data_sitekey; ?>></div>
            <input type = "submit" name = "signup">

            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST"){
                    if(isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] == $_POST['csrf_token']){
                        $email = htmlspecialchars(mysqli_real_escape_string($con,$_POST['email']));
                        $username = htmlspecialchars(mysqli_real_escape_string($con,$_POST['username']));
                        $password = htmlspecialchars(mysqli_real_escape_string($con,$_POST['password']));
                        
                        $recaptcha = check_recaptcha();
                        if(!$recaptcha){
                            echo "Please check the box to proceed!";
                        }
                        else{
                            if($recaptcha && !empty($email) && !empty($username) && !empty($password)){
                                $query="select * from users where email = '$email'";
                                $result = mysqli_query($con,$query);
                                if($result && mysqli_num_rows($result) > 0){
                                    echo "Email already in use!";
                                }
                                else{
                                    $query="select * from users where username = '$username'";
                                    $result = mysqli_query($con,$query);
                                    if($result && mysqli_num_rows($result) > 0){
                                        echo "Username is taken!";
                                    }
                
                                    else{
                                        $vkey = bin2hex(random_bytes(32));
                                        $mail_recipient = $email;
                                        $mail_subject = "Email Verification";
                                        $mail_message = "<a href='" . $email_verification_link ."?vkey=$vkey&&mail=$mail_recipient'>Register Account</a>";
                                        $email_sent = validation_mail($mail_recipient, $mail_subject, $mail_message);
                                        if($email_sent){
                                            $query = "insert into users (email, username, password, vkey) values ('$email', '$username', '$password', '$vkey')";
                                            mysqli_query($con, $query);
                                            header("Location: thank_you.php");
                                        }
                                    }
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
            <a href = "login.php">Log-in</a>
        </form>
    </div>
</body>
</html>