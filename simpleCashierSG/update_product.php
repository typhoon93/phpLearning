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


function valid_entries_product_update($price, $promoprice, $promoquantity){
    //Used when adding editing a product.
    //check all the entries and makes sure they are valid data, and returns an array
    // array[0] is a bool - true or false; true if data is valid, false if any of the data is invalid.
    // array[1] is a string that gives a bit of info on the entered data and what the problem with it is.

    if (!is_numeric($price) || !is_numeric($promoprice) || !is_numeric($promoquantity)){
      return [false, "Your Price, PromoPrice and PromoQuantity need to be numbers. "];
    } elseif ($price <= 0) {
      return [false, "Your price needs to be bigger than 0."];
    } elseif ($promoprice < 0 || $promoquantity < 0){
      return [false, "Your PromoPrice and PromoQuantity need to be 0 or a positive number. Enter 0 on both fields if the item does not have a promotion."];
    } else {
      return [true,'Entered data is valid.'];
    }
    
}

$newprice = filter_input(INPUT_POST, "price", FILTER_SANITIZE_STRING);
$newpromoprice = filter_input(INPUT_POST, "promoprice", FILTER_SANITIZE_STRING);
$newpromoquantity = filter_input(INPUT_POST, "promoquantity", FILTER_SANITIZE_STRING);

$updateID = filter_input(INPUT_POST, "updateID", FILTER_VALIDATE_INT);
if(isset($updateID)){
$updateID = test_input($updateID);
}

//Editing a new product

if(isset($newprice) && isset($newpromoprice) && isset($newpromoquantity)){
  
    $newprice = test_input($newprice);
    $newpromoprice = test_input($newpromoprice);
    $newpromoquantity = test_input($newpromoquantity);
    $submitted_data_is_valid = valid_entries_product_update($newprice, $newpromoprice, $newpromoquantity);
    
 }


?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content= "IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Update Product</title>

  <link rel="stylesheet" href="main.css">

</head>

<body>
<center>
 <main>
  <header>
    <h1>Update Product</h1>
    <br>
 </header>

 <section>
 <?php 
    require("database.php");

// updating product data
    if($submitted_data_is_valid[0] === true){
      $query = "UPDATE products
                    SET Price = :price, PromoPrice = :promoprice, PromoQuantity = :promoquantity
                    WHERE ID = :updateID";
                $statement = $db->prepare($query);
                $statement->bindValue(':updateID', $updateID);
                $statement->bindValue(':price', $newprice);
                $statement->bindValue(':promoprice', $newpromoprice);
                $statement->bindValue(':promoquantity', $newpromoquantity);
                $statement->execute();
                //uncomment below if you're db update doesn't work;
                //echo var_dump($statement->errorInfo());
                $statement->closeCursor();
        ;
    } else if ($submitted_data_is_valid[0] === false) {
      echo "<FONT COLOR='red'>".$submitted_data_is_valid[1]."</FONT><br><br>";
    }

//showing the product data:
  if(isset($updateID)){
    $query = 'SELECT * FROM products WHERE ID=:updateID';
    $statement = $db->prepare($query);
    $statement->bindValue(':updateID', $updateID);
    $statement->execute();
    $updateProduct = $statement->fetch();
    $statement->closeCursor();

    $productCode = $updateProduct['Code'];
    $productPrice= $updateProduct['Price'];
    $productPromoPrice =$updateProduct['PromoPrice'];
    $productPromoQuantity = $updateProduct['PromoQuantity'];
    $thisPage = $_SERVER['PHP_SELF'];
    echo "
    <form action='$thisPage' method = 'POST' >
           <input type='hidden' name='updateID' value='$updateID'>
          <label for='code'>Code:</label>
          <input type='text' id='code' name='code' maxlength='1' value='$productCode' required readonly>
          <label for='price'>Price:</label>
          <input type='text' id='price' name='price' value ='$productPrice' required>
          <label for='promoprice'>PromoPrice:</label>
          <input type='text' id='promoprice' name='promoprice' value='$productPromoPrice' required>
          <label for='promoquantity'>PromoQuantity:</label>
          <input type='text' id='promoquantity' name='promoquantity' value='$productPromoQuantity' required>
          <button>Update</button>
    </form>
    ";
  } else {
    echo "You haven't selected a product to update. Go back to:";
  }
    

 ?>
    <br><br>
    <a href="/">New Order</a> -
    <a href="/products.php">Products</a> -
    <a href="/orders.php">Orders</a>
 </section>

 </main>
 </center>
</body>
</html>