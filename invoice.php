
<!DOCTYPE html>
<html>
<head>
    <title>Order complete</title>
</head>
<body>
    <?php
        session_start();
        include("constants.php");
        include("functions.php");

        if(isset($_SESSION['current_invoice']) && isset($_SESSION['user_id'])){
            require "fpdf184/fpdf.php";
            $user_id = $_SESSION['user_id'];
            $current_invoice = $_SESSION['current_invoice'];

            //A4 width : 219mm
            //default margin : 10mm each side
            //writable horizontal : 219-(10*2)=189mm

            //Cell(width , height , text , border , end line , align)
            //MultiCell(width, height, text, border, align, fill(true if cell must be painted))

            $pdf = new FPDF("P", "mm", "A4");

            $pdf->AddPage();

            //header
            $pdf->SetFont("Arial", "B", 14);
            $pdf->Cell(130, 5, "DENIS' PET-SHOP", 0, 0);
            $pdf->Cell(59, 5, "INVOICE", 0, 1);


            $pdf->SetFont("Arial", "", 12);

            //empty line
            $pdf->Cell(189, 10, "", 0, 1); 

            //customer info
            $query = "select * from order_history where user_id = '$user_id' order by order_id desc limit 1";
            $result = mysqli_query($con, $query);
            $customer_info = mysqli_fetch_assoc($result);

            $pdf->Cell(130, 5, $customer_info['address'], 0, 0);
            $pdf->Cell(30, 5, "Invoice #", 0, 0);
            $pdf->Cell(34, 5, $current_invoice, 0, 1); 

            $pdf->Cell(130, 5, $customer_info['country'], 0, 0);
            $pdf->Cell(30, 5, "Date", 0, 0);
            $pdf->Cell(34, 5, $customer_info['date'], 0, 1);

            $pdf->Cell(130, 5, $customer_info['city'], 0, 0);
            $pdf->Cell(30, 5, "Customer ID", 0, 0);
            $pdf->Cell(34, 5, "$user_id", 0, 1);

            $pdf->Cell(130, 5, "ZIP: " . $customer_info['postal_code'] , 0, 0);
            $pdf->Cell(64, 5, "", 0, 1); 

            //empty line
            $pdf->Cell(189, 10, "", 0, 1); 

            //billing details
            $pdf->Cell(100, 5, "Bill to", 0, 1);

            $pdf->Cell(10, 5, "", 0, 0);
            $pdf->Cell(90, 5, $customer_info['name'], 0, 1);

            $pdf->Cell(10, 5, "", 0, 0);
            $pdf->Cell(90, 5, $customer_info['email'], 0, 1);

            $pdf->Cell(10, 5, "", 0, 0);
            $pdf->Cell(90, 5, $customer_info['phone_number'], 0, 1);


            //empty line
            $pdf->Cell(189, 10, "", 0, 1);

            //products column titles
            $pdf->SetFont("Arial", "B", 12);

            $pdf->Cell(130, 5, "Description", 1, 0);
            $pdf->Cell(25, 5, "Qty", 1, 0);
            $pdf->Cell(34, 5, "Price", 1, 1); //end of line

            $pdf->SetFont("Arial", "", 12);

            //products
            $subtotal = 0;
            $tax_rate = 19;
            $query = "select * from order_history where invoice_number = '$current_invoice'";
            $result = mysqli_query($con, $query);
            while($row = mysqli_fetch_assoc($result)) {
                $prod_id = $row['prod_id'];
                $prod_query = "select * from products where prod_id = '$prod_id' limit 1";
                $prod_result = mysqli_query($con, $prod_query);
                $product = mysqli_fetch_assoc($prod_result);
                if (mysqli_num_rows($result) > 0) {
                    $x1 = $pdf->GetX();
                    $y1 = $pdf->GetY();
                    $pdf->Multicell(130, 5, $product['name'], 1);
                    $y2 = $pdf->GetY();
                    $pdf->SetXY($x1 + 130, $y1);
                    $pdf->Cell(25, $y2-$y1, $row['qty'], 1, 0, "R");
                    $pdf->Cell(34, $y2-$y1, $row['qty'] * $product['price'], 1, 1, "R");

                    $subtotal += $row['qty'] * $product['price'];
                }
                
            }

            //summary
            $pdf->Cell(130, 5, "", 0, 0);
            $pdf->Cell(25, 5, "Subtotal", 0, 0);
            $pdf->Cell(34, 5, $subtotal, 1, 1, "R");

            $pdf->Cell(130, 5, "", 0, 0);
            $pdf->Cell(25, 5, "Tax Rate", 0, 0);
            $pdf->Cell(34, 5, $tax_rate . "%", 1, 1, "R");

            $pdf->Cell(130, 5, "", 0, 0);
            $pdf->Cell(25, 5, "Total Due", 0, 0);
            $pdf->Cell(34, 5, ceil($subtotal + (($subtotal * 19) / 100)), 1, 1, "R");

            //mail
            $attachment = $pdf->Output('S');
            invoice_mail($customer_info['email'], "Order confirmation", $attachment, "You order from Denis' Pet-Shop has been confirmed.");
            unset($_SESSION['current_invoice']);

        }
        else{
            header("Location:index.php");
        }
    ?>
</body>
