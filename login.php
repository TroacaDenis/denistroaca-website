<?php
    session_start();
    include("connect.php");
    include("functions.php");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <div>
        <form method = "post">
            <div style = "margin:10px">Log-in</div>
            <p>username: <input type = "text" name = "username"> </p>
            <p>password: <input type = "password" name = "password"></p>
            <input type = "submit" name = "login">

            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST"){
                    $username = $_POST['username'];
                    $password = $_POST['password'];
            
                    if(!empty($username) && !empty($password)){
                        $query = "select * from users where username = '$username' limit 1";
                        $result = mysqli_query($con, $query);
                        if($result && mysqli_num_rows($result) > 0){
                            $user_data = mysqli_fetch_assoc($result);
                            if($user_data['password'] == $password){
                                $_SESSION['user_id'] = $user_data['user_id'];
                                header("Location: index.php");
                                die;
                            }
                        }
                        
                    }
                    echo "Incorrect username or password!";
                }
            ?>
            <br><br>
            <a href = "signup.php">Sign-up</a>
        </form>
    </div>
</body>
</html>