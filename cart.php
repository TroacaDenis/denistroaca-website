<?php
    session_start();
    include("constants.php");
    include("functions.php");
    include("components.php");
    $user_data = check_login($con);

    if($user_data==null){
        header("Location:login.php");
    }
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping cart</title>
</head>
<body>
    <div>
        <a href = "index.php">Home</a>       
    </div>

    <div>
        <div> 
            <?php
                $total_price = 0;
                $query = "select * from orders where user_id = " . $user_data['user_id'] . " order by prod_id";
                $result = mysqli_query($con, $query);

                if (mysqli_num_rows($result) > 0) {
                    unset($_SESSION['checkout_warning']);
                    while($row = mysqli_fetch_assoc($result)) {
                        echo cart_product($row);
                    }
                }
            ?>

            <?php
            if($_SERVER['REQUEST_METHOD'] == "POST"){
                if(isset($_POST['remove_prod'])){
                    delete_from_cart($con, $user_data['user_id'], $_POST['prod_id']);
                } 
                if(isset($_POST['decrease_qty'])){
                    decrease_qty($con, $user_data['user_id'], $_POST['prod_id']);
                }
                if(isset($_POST['increase_qty'])){
                    increase_qty($con, $user_data['user_id'], $_POST['prod_id']);
                }

            }
        ?> 
        </div>
            <p>Total price: <?php echo $total_price ?></p>
            <?php
                if(isset($_SESSION['checkout_warning'])){
                    ?>
                    <script>
                        function alert_function() {
                            alert("Not enough products in stock!");
                        }
                    </script>
                    <button onclick="alert_function()">Proceed to checkout</button>
                    <?php
                }
                else{
                    ?>
                    <button onclick="location.href='checkout.php'">Proceed to checkout</button>
                    <?php
                }
            ?>
        <div> 
            
        </div>
    </div>

    
</body>
</html>