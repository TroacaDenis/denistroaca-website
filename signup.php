<?php
    session_start();
    include("connect.php");
    include("functions.php");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
</head>
<body>
    <div>
        <form method = "post">
            <div style = "margin:10px">Sign-up</div>
            <p>email: <input type = "email" name = "email"> </p>
            <p>username: <input type = "text" name = "username"> </p>
            <p>password: <input type = "password" name = "password"></p>
            <input type = "submit" name = "signup">

            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST"){
                    $email = $_POST['email'];
                    $username = $_POST['username'];
                    $password = $_POST['password'];
            
                    if(!empty($email) && !empty($username) && !empty($password)){
                        $query = "insert into users (email, username, password) values ('$email', '$username', '$password')";
                        mysqli_query($con, $query);
                        echo "Account created!";
                    }
                }
            ?>

            <br><br>
            <a href = "login.php">Log-in</a>
        </form>
    </div>
</body>
</html>