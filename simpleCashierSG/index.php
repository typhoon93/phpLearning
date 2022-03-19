<?php

    function test_input($data) {
    //trims and secures input;
        
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    //makes it upper case;
    $data = strtoupper($data);

    return $data;
    }

    function valid_products($data){
        // checks if the products entered are all valid - i.e they exist in the database
        // Returns True if all product codes entered into the form are valid, else returns False
        require("database.php");

        $query = 'SELECT * FROM products';
            $statement = $db->prepare($query);
            $statement->execute();
            $allProducts = $statement->fetchAll();
            $statement->closeCursor();
        
        foreach($allProducts as $product){
            $allProductsArray[] = $product['Code'];
        }

        $codes = str_split($data);

        foreach($codes as $code){

            if(in_array($code, $allProductsArray)){

                continue;

            }else{
                return false;
            }
        }

        return true;
    }

    $ordercodes = filter_input(INPUT_POST, "ordercodes", FILTER_SANITIZE_STRING);

    if($ordercodes){
    $ordercodes = test_input($ordercodes);
    $validProductCodes = valid_products($ordercodes);
    }

?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content= "IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Order Form</title>

  <link rel="stylesheet" href="main.css">

</head>

<body>
<center>
 <main>
 
  <header>
    <h1>Order Form</h1>
 </header>


    <?php if(!$ordercodes || !$validProductCodes){ ?>
      <section>
        <h2>Input Order -  use A, B, C, D as values.</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method = "POST" >
          <label for="ordercodes">OrderCodes:</label>
          <input type="text" id="ordercodes" name="ordercodes" required>
          <button>Submit</button>
          <?php if($validProductCodes === false){
              echo "<br><br>  You have entered invalid data: <FONT COLOR='red'>", $ordercodes,"</FONT>. Please try again. "; 
          } ?>
        </form>
      </section>

    <?php } else {?>
        
        <?php require("database.php"); ?>

        <?php 
          if($ordercodes){

            //create an ORDER / Order ID
            $query = "INSERT INTO orders
                          (OrderCodes)
                        VALUES
                          (:ordercodes)";
            $statement = $db->prepare($query);
            $statement->bindValue(':ordercodes', $ordercodes);
            $statement->execute();
            $statement->closeCursor();

            //gets the last inserted id
            $OrderID = $db->lastInsertId();


            // get frequency of specific chars as an array, from the input string,
            // so we can calculate the individual suborders;

            $frequency = count_chars($ordercodes,1);

            $currentOrderTotalPrice = 0;

            // outputs html for the invoice we will display
            echo "<h2>Invoice<h2>";
            echo "<h3>OrderID: ", $OrderID, "</h3>";
            echo "<table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>";
            // end of output part

            foreach($frequency as $code=>$freq){
                
                //turnunicode to char
                $code = chr($code);    

                //In this next section we are querying the database and getting the relevant for the current product.

                $query = 'SELECT * FROM products
                      WHERE Code = :code';
                $statement = $db->prepare($query);
                $statement->bindValue(':code', $code);
                $statement->execute();
                $currentProduct = $statement->fetch();
                $statement->closeCursor();

                //creating easy to read variables from the data that we gatherd
                $normalPrice = $currentProduct["Price"];
                $promoPrice = $currentProduct["PromoPrice"];
                $promoQuantity = $currentProduct["PromoQuantity"];
                $productName = $currentProduct['Code'];
                $quantity = $freq;

                // In this next section we will calculate the total price for this specific product
                // A loop to get the total price of the order for this specific item, having in mind the promo price
                
                $currentItemTotalPrice = 0;
                
                if($promoPrice > 0 && $promoQuantity > 0 ) {
                while($freq >= $promoQuantity) {
                    
                    $currentItemTotalPrice += $promoPrice;
                    $freq -= $promoQuantity;
                    }
                }
                // above we went ahead calculated all the items that fit our promo criteria. Now we calculate the remaining items of the same type

                $currentItemTotalPrice += $freq * $normalPrice;

                // Inserting all relevant data into the  orderitems table, which contains individual orders for specific products


                $query = "INSERT INTO orderitems
                          (OrderID, Name, Quantity, TotalPrice)
                        VALUES
                          (:orderid, :productName, :quantity, :totalprice)";
                $statement = $db->prepare($query);
                $statement->bindValue(':orderid', $OrderID); // this is the ID of the Order itself, which is going to be the same for all items in this loop.
                $statement->bindValue(':productName', $productName);
                $statement->bindValue(':quantity', $quantity);
                $statement->bindValue(':totalprice', $currentItemTotalPrice);
                $statement->execute();
                $statement->closeCursor();

                // html output for the invoice
                echo "<tr>
                        <td>$code</td>
                        <td>$quantity</td>
                        <td>$currentItemTotalPrice</td>
                </tr>";
                
                

                $currentOrderTotalPrice += $currentItemTotalPrice;

            };
            //html output for the invoice
            echo "<tr><td colspan='3'> Total Price: $currentOrderTotalPrice </tr><td>";
            echo "</table>";
            
            // updates the total amount number in the "orders" table
            $query = "UPDATE orders
                        SET TotalAmount = :totalamount
                            WHERE OrderID = :orderid ";

            $statement = $db->prepare($query);
            $statement->bindValue(':orderid', $OrderID);
            $statement->bindValue(':totalamount', $currentOrderTotalPrice);
            $statement->execute();
            $statement->closeCursor();


          }
    
          
         }?>

          <br></br>



          <a href="<?php echo $_SERVER['PHP_SELF'] ?>"> New Order</a>
   

 </main>
 </center>
</body>
</html>