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

function productExists($code){
    //products in the database need to be unique. This funciton checks if the product already exists

    require("database.php");

    $query = 'SELECT * FROM products';
        $statement = $db->prepare($query);
        $statement->execute();
        $allProducts = $statement->fetchAll();
        $statement->closeCursor();
    
    foreach($allProducts as $product){
        $allProductsArray[] = $product['Code'];
    }
    
    if(in_array($code, $allProductsArray)){

            return True;
    }

    return False;

}


function valid_entries($newcode, $price, $promoprice, $promoquantity){
  //Used when adding a new product.
  //check all the entries and makes sure they are valid data, and returns an array
  // array[0] is a bool - true or false; true if data is valid, false if any of the data is invalid.
  // array[1] is a string that gives a bit of info on the entered data and what the problem with it is.

    if (!ctype_alpha($newcode)){
      return [false, "You code/product name needs to be a letter from the alphabet."];
    } elseif (productExists($newcode)){
      return [false, "The code/product you have entered already exists in the database."];
    } elseif (strlen($newcode) != 1){
      return [false, "The code/product you have needs to be only 1 character long."];
    } elseif (!is_numeric($price) || !is_numeric($promoprice) || !is_numeric($promoquantity)){
      return [false, "Your Price, PromoPrice and PromoQuantity need to be numbers. "];
    } elseif ($price <= 0) {
      return [false, "Your price needs to be bigger than 0."];
    } elseif ($promoprice < 0 || $promoquantity < 0){
      return [false, "Your PromoPrice and PromoQuantity need to be 0 or a positive number. Enter 0 on both fields if the item does not have a promotion."];
    } else {
      return [true,'Entered data is valid.'];
    }
    
}

$newcode = filter_input(INPUT_POST, "newcode", FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, "price", FILTER_SANITIZE_STRING);
$promoprice = filter_input(INPUT_POST, "promoprice", FILTER_SANITIZE_STRING);
$promoquantity = filter_input(INPUT_POST, "promoquantity", FILTER_SANITIZE_STRING);

$deleteID = filter_input(INPUT_POST, "deleteID", FILTER_VALIDATE_INT);


//Adding a new product

if(isset($newcode) && isset($price) && isset($promoprice) && isset($promoquantity)){
    $newcode = test_input($newcode);
    $price = test_input($price);
    $promoprice = test_input($promoprice);
    $promoquantity = test_input($promoquantity);

    $submitted_data_is_valid = valid_entries($newcode, $price, $promoprice, $promoquantity);
 }

 // DELETING A PRODUCT
if(isset($deleteID)){
  $deleteID = test_input($deleteID);
}

?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content= "IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>All Products</title>

  <link rel="stylesheet" href="main.css">

</head>

<body>
<center>
 <main>
 
  <header>
    <h1>All Products</h1>
    <br>
    <br>
 </header>
  <section>
        <h2>Insert Product</h2>
        <p>A product/code name should be an alphabetic character, only 1 letter. Price should be greater than zero. 
        PromoCode / PromoPrice should be set to 0 if there are no active promotions. They cannot be set to negative numbers.</p>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method = "POST" >
          <label for="newcode">Code:</label>
          <input type="text" id="newcode" name="newcode" maxlength="1" required>
          <label for="price">Price:</label>
          <input type="text" id="price" name="price" required>
          <label for="promoprice">PromoPrice:</label>
          <input type="text" id="promoprice" name="promoprice" value="0" required>
          <label for="promoquantity">PromoQuantity:</label>
          <input type="text" id="promoquantity" name="promoquantity" value="0" required>
          <button>Submit</button>
        </form>
  </section>
<br>
<br>


 <section>
 <?php 
require("database.php");

//ADDING A NEW PRODUCT

if($submitted_data_is_valid[0] === true){
  $query = "INSERT INTO products
                          (Code, Price, PromoPrice, PromoQuantity)
                        VALUES
                          (:newcode, :price, :promoprice, :promoquantity)";
            $statement = $db->prepare($query);
            $statement->bindValue(':newcode', $newcode);
            $statement->bindValue(':price', $price);
            $statement->bindValue(':promoprice', $promoprice);
            $statement->bindValue(':promoquantity', $promoquantity);
            $statement->execute();
            //uncomment below if you're db update doesn't work;
            //echo var_dump($statement->errorInfo());
            $statement->closeCursor();
  
} else if($submitted_data_is_valid[0] === false) {
  echo "<FONT COLOR='red'>".$submitted_data_is_valid[1]."</FONT><br><br>";

}

// DELETING A PRODUCT

if(isset($deleteID)){

    $query = 'DELETE FROM products
                WHERE ID= :deleteID';
    $statement = $db->prepare($query);
    $statement->bindValue(':deleteID', $deleteID);
    $success = $statement->execute();
    $statement->closeCursor();
}

// SHOWING ALL PRODUCTS:

echo "<h2>Current Products: </h2>";

$query = 'SELECT * FROM products';
$statement = $db->prepare($query);
$statement->execute();
$allProducts = $statement->fetchAll();
$statement->closeCursor();


echo "<table>
                    <tr>
                        <th>Code</th>
                        <th>Price</th>
                        <th>PromoPrice</th>
                        <th>PromoQuantity</th>
                        <th>Edit/Delete</th>
                  
                    </tr>";

foreach($allProducts as $product){

    $productCode = $product['Code'];
    $productPrice= $product['Price'];
    $productPromoPrice =$product['PromoPrice'];
    $productPromoQuantity = $product['PromoQuantity'];
    $productID = $product['ID'];
    $thisPage = $_SERVER['PHP_SELF'];
    
    echo          "<tr>          
                      <td>$productCode</td>
                        <td>$productPrice</td>
                        <td>$productPromoPrice</td>
                        <td>$productPromoQuantity</td>
                        <td>
                          <form class='delete' action='$thisPage' method='POST'>
                            <input type='hidden' name='deleteID' value='$productID'>
                            <button>Delete</button>
                          </form>
                          <form class='delete' action='/update_product.php' method='POST'>
                            <input type='hidden' name='updateID' value='$productID'>
                            <button>Update</button>
                          </form>
                        </td>
                  </tr>";
}
 
   echo "</table>";
 ?>
 </section>
  <br></br>



          <a href="/"> New Order</a> - 
          <a href="/orders.php">Orders</a>
 </main>
 </center>
</body>
</html>