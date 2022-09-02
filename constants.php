<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "denistroacashop";
    $con = mysqli_connect($servername, $username, $password, $database);
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $recaptcha_data_sitekey ="6LdJiaEhAAAAAJhR2nC_ifsA_ERSin-4iQzOwFIH";
    $recaptcha_secret_key = "6LdJiaEhAAAAACVmM3VSc8M-lCwN06JXVsBLwMyD";
    $email_verification_link = "http://localhost/denistroaca/verify.php";

?>