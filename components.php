<?php

    include("constants.php");

    function home_product($product){
        $home_prod = "
            <form method = 'post' style = 'width:fit-content; text-align:center; border-style:double; padding:5px; margin-right:20px; float:left' >
                <p> " . $product['name'] . "</p> 
                <p> pret: " . $product['price'] . "  lei </p> 
                <p> rating: " . $product['avg_ratings'] . "/5 </p> 
                <input type = 'hidden' name = 'prod_id' value = " . $product['prod_id'] . ">
                <input type = 'submit' value = 'Add to cart'>
            </form>";
        return $home_prod;
    }

    function cart_product($order){
        global $con;
        global $total_price;
        $query = "select * from products where prod_id = " . $order['prod_id'] . " limit 1";
        $result = mysqli_query($con, $query);
        $product = mysqli_fetch_assoc($result);
        $total_price = $total_price + $order['qty'] * $product['price'];
        $cart_prod = "
            <form method = 'post' style = 'width:fit-content; text-align:center; border-style:double; padding:5px; margin-right:20px; float:left' >
                <p> " . $product['name'] . "</p> 
                <p> pret: " . $order['qty'] * $product['price'] . "  lei </p> 
                <p> 
                    <input type = 'submit' name = 'decrease_qty' value = '-'>
                    ". $order['qty'] . "
                    <input type = 'submit' name = 'increase_qty' value = '+'>
                </p> 
                <input type = 'hidden' name = 'prod_id' value = " . $product['prod_id'] . ">
                <input type = 'submit' name = 'remove_prod' value = 'Remove'>
                ";
        
        if($order['qty']>$product['qty']){
            $cart_prod = $cart_prod . "<p> Warning! Only " . $product['qty'] . " products left in stock.</p></form>";
            $_SESSION['checkout_warning'] = "true";
        }
        else{
            $cart_prod = $cart_prod . "</form>";
        }

        return $cart_prod;
    }

?>