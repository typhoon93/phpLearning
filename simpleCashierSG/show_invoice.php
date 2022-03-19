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

$orderID = filter_input(INPUT_POST, "orderID", FILTER_VALIDATE_INT);

if(isset($orderID)){
$orderID = test_input($orderID);
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content= "IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Invoice</title>

  <link rel="stylesheet" href="main.css">

</head>

<body>
<center>
 <main>
 
  <header>
    <h1>OrderID: <?php echo $orderID ?></h1>
 </header>
<br>



 <section>
 <?php 
require("database.php");


// SHOWING INVOICE FOR SPECIFIC ORDER:

if(isset($orderID)){
    //getting individual orders
    $query = 'SELECT * FROM orderitems WHERE OrderID=:orderID';
    $statement = $db->prepare($query);
    $statement->bindValue(':orderID', $orderID);
    $statement->execute();
    $allOrderItems = $statement->fetchAll();
    $statement->closeCursor();

    $query = 'SELECT * FROM orders WHERE OrderID=:orderID';
    $statement = $db->prepare($query);
    $statement->bindValue(':orderID', $orderID);
    $statement->execute();
    $order = $statement->fetch();
    $totalPrice = $order["TotalAmount"];
    $statement->closeCursor();

    
    echo "<table>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>";

    foreach($allOrderItems as $orderItem){

        
        $product= $orderItem['Name'];
        $quantity = $orderItem['Quantity'];
        $price =$orderItem['TotalPrice'];
        
        echo          "<tr>          
                          <td>$product</td>
                            <td>$quantity</td>
                            <td>$price</td>
                      </tr>";
    }
    echo "<tr><td colspan='3'> Total Price: $totalPrice </tr><td>";
    echo "</table>";

  } else {
    echo "You haven't selected an order: Go back to:";
  }

 ?>
 </section>
  <br></br>



  <a href="/">New Order</a> -
  <a href="/products.php">Products</a> -
  <a href="/orders.php">Orders</a>
 </main>
 </center>
</body>
</html>