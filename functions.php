<?php

    include("constants.php");

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

    function check_recaptcha(){
        global $recaptcha_secret_key;
        $secret_key = $recaptcha_secret_key;
        $response_key = $_POST['g-recaptcha-response'];
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$response_key&remoteip=$user_ip";
        $response = file_get_contents($url);
        $response = json_decode($response);
        if($response->success){
            return "success";
        }
        return null;
    }

    function delete_from_cart($con, $user_id, $prod_id){
        $query = "delete from orders where user_id = '$user_id' and prod_id = '$prod_id'";
        mysqli_query($con ,$query);
        header("Location: cart.php");
    }

    function decrease_qty($con, $user_id, $prod_id){
        $query = "select qty from orders where user_id = '$user_id' and prod_id = '$prod_id' limit 1";
        $result = mysqli_query($con ,$query);
        $user_data = mysqli_fetch_assoc($result);
        $qty = $user_data['qty'] - 1;
        if($qty < 1){
            delete_from_cart($con, $user_id, $prod_id);
        }
        else{
            $query = "update orders set qty = '$qty' where user_id = '$user_id' and prod_id = '$prod_id'";
            mysqli_query($con ,$query);
        }
        header("Location: cart.php");
    }

    function increase_qty($con, $user_id, $prod_id){
        $query = "select qty from orders where user_id = '$user_id' and prod_id = '$prod_id' limit 1";
        $result = mysqli_query($con ,$query);
        $user_data = mysqli_fetch_assoc($result);
        $qty = $user_data['qty'] + 1;
        
        $query = "update orders set qty = '$qty' where user_id = '$user_id' and prod_id = '$prod_id'";
        mysqli_query($con ,$query);

        header("Location: cart.php");
    }


    use PHPMailer\PHPMailer\PHPMailer;
    function validation_mail($mail_recipient, $mail_subject, $mail_message){
        require_once "PHPMailer/PHPMailer.php";
        require_once "PHPMailer/SMTP.php";
        require_once "PHPMailer/Exception.php";

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                          
            $mail->Host       = 'smtp.gmail.com';                     
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'troacad2@gmail.com';                    
            $mail->Password   = 'rymlduygeddfrfci';                               
            $mail->SMTPSecure = 'ssl';           
            $mail->Port       = 465;                                    

            //Recipients
            $mail->setFrom('troacad2@gmail.com');
            $mail->addAddress($mail_recipient);   

            //Content
            $mail->isHTML(true);                                
            $mail->Subject = $mail_subject;
            $mail->Body    = $mail_message;

            $mail->send();
            echo 'Message has been sent';
            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }

    function invoice_mail($mail_recipient, $mail_subject, $attachment, $mail_message){
        require_once "PHPMailer/PHPMailer.php";
        require_once "PHPMailer/SMTP.php";
        require_once "PHPMailer/Exception.php";

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                          
            $mail->Host       = 'smtp.gmail.com';                     
            $mail->SMTPAuth   = true;                                  
            $mail->Username   = 'troacad2@gmail.com';                    
            $mail->Password   = 'rymlduygeddfrfci';                               
            $mail->SMTPSecure = 'ssl';           
            $mail->Port       = 465;                                    

            //Recipients
            $mail->setFrom('troacad2@gmail.com');
            $mail->addAddress($mail_recipient);   

            //Attachments
            $mail->addStringAttachment($attachment, "invoice.pdf"); 

            //Content
            $mail->isHTML(true);                                
            $mail->Subject = $mail_subject;
            $mail->Body    = $mail_message;

            $mail->send();
            echo 'Order complete. Return to ';
            ?>
            <a href="index.php">shopping.</a>
            <?php

            return true;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            return false;
        }
    }
    
?>
