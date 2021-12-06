<?php

    function check_login($con){
        if(isset($_SESSION['user_id'])){
            $user_id = $_SESSION['user_id'];
            $query = "select * from users where user_id = '$user_id' limit 1";
            $result = mysqli_query($con ,$query);

            if($result && mysqli_num_rows($result) > 0){
                $user_data = mysqli_fetch_assoc($result);
                return $user_data;
            }
        }
        return null;

    }

    function add_to_cart($con, $user_id, $prod_id){
        $query = "select qty from orders where user_id = '$user_id' and prod_id = '$prod_id' limit 1";
        $result = mysqli_query($con ,$query);
        if($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            $qty = $user_data['qty'] + 1;
            $query = "update orders set qty = '$qty' where user_id = '$user_id' and prod_id = '$prod_id'";
            mysqli_query($con ,$query);
            
        }
        else{
            $query = "insert into orders (prod_id, user_id, qty) values ('$prod_id', '$user_id', '1')";
            mysqli_query($con ,$query);
        }
        header("Location: index.php");
    }
?>
