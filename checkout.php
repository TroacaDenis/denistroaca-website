<?php
    session_start();
    include("constants.php");
    include("functions.php");
    include("components.php");
    $user_data = check_login($con);

    if($user_data==null){
        header("Location:login.php");
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div>
        <a href = "index.php">Home</a>   
        <a href = "cart.php">View cart</a>      
    </div>

    <div>
        <form method = 'post' style = 'width:fit-content; text-align:center; border-style:double; float:left'>

            <p>Email: <input type = "email" name = "email"></p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['email'])){echo "Email is required!";}?></span>
            <p>Full name: <input type = "text" name = "name"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['name'])){echo "Name is required!";}?></span>
            <p>Phone number: <input type = "text" name = "phone_number"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['phone_number'])){echo "Phone number is required!";}?></span>
            <p>Country: <input type = "text" name = "country"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['country'])){echo "Country is required!";}?></span>
            <p>City: <input type = "text" name = "city"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['city'])){echo "City is required!";}?></span>
            <p>County(if applicable): <input type = "text" name = "county"> </p>
            <p>Postal code: <input type = "text" name = "postal_code"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['postal_code'])){echo "Postal code is required!";}?></span>
            <p>Address: <input type = "text" name = "address"> </p>
            <span class="error"><?php if ($_SERVER['REQUEST_METHOD'] == "POST" && empty($_POST['address'])){echo "Address is required!";}?></span>

            <input type = "hidden" name = "csrf_token" value = <?php echo $token ?>>
            <div class="g-recaptcha" data-sitekey=<?php echo $recaptcha_data_sitekey; ?>></div>
            <input type = "submit" name = "purchase">

            <?php
                if($_SERVER['REQUEST_METHOD'] == "POST"){
                    if(isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] == $_POST['csrf_token']){
                        $email = htmlspecialchars(mysqli_real_escape_string($con,$_POST['email']));
                        $name = htmlspecialchars(mysqli_real_escape_string($con,$_POST['name']));
                        $phone_number = htmlspecialchars(mysqli_real_escape_string($con,$_POST['phone_number']));
                        $country = htmlspecialchars(mysqli_real_escape_string($con,$_POST['country']));
                        $city = htmlspecialchars(mysqli_real_escape_string($con,$_POST['city']));
                        $county = htmlspecialchars(mysqli_real_escape_string($con,$_POST['county']));
                        $postal_code = htmlspecialchars(mysqli_real_escape_string($con,$_POST['postal_code']));
                        $address = htmlspecialchars(mysqli_real_escape_string($con,$_POST['address']));

                        $recaptcha = check_recaptcha();
                        if(!$recaptcha){
                            echo "Please check the box to proceed!";
                        }
                        else{
                            if(!empty($email) && !empty($name) && !empty($phone_number) && !empty($country) && !empty($city) && !empty($postal_code) && !empty($address)){

                                //get previous invoice nr
                                $query = "select coalesce(max(invoice_number),0) as max_invoice from order_history";
                                $result = mysqli_query($con, $query);
                                if(mysqli_num_rows($result) > 0){
                                    $row = mysqli_fetch_assoc($result);
                                    $current_invoice = $row['max_invoice'] + 1;
                                }

                                //get orders
                                $user_id = $user_data['user_id'];
                                $query = "select * from orders where user_id = '$user_id'";
                                $result = mysqli_query($con, $query);
                                if (mysqli_num_rows($result) > 0) {
                                    $_SESSION['current_invoice'] = $current_invoice;
                                    while($row = mysqli_fetch_assoc($result)) {
                                        $prod_id = $row['prod_id'];
                                        $qty = $row['qty'];

                                        $update_product = "update products set qty = qty - '$qty' where prod_id = '$prod_id'";
                                        mysqli_query($con, $update_product);

                                        $delete_order = "delete from orders where user_id = '$user_id' and prod_id = '$prod_id'";
                                        mysqli_query($con, $delete_order);

                                        $insert_order_history = "insert into order_history (user_id, prod_id, qty, email, name, phone_number, country, city, county, postal_code, address, invoice_number) values ('$user_id', '$prod_id', '$qty', '$email', '$name', '$phone_number', '$country', '$city', '$county', '$postal_code', '$address', '$current_invoice')";
                                        mysqli_query($con, $insert_order_history);

                                    }
                                    header("Location: invoice.php");
                                }
                                else{
                                    echo "Shopping cart is empty!";
                                } 
                            }
                        }
                    }
                    else{
                        echo "Csrf token is not valid!";
                    }
                }
            ?>
        </form>
    </div>

    
</body>
</html>