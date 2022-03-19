<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content= "IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>All Orders</title>

  <link rel="stylesheet" href="main.css">

</head>

<body>
<center>
 <main>
 
  <header>
    <h1>All Orders</h1>
 </header>
<br>



 <section>
 <?php 
require("database.php");


// SHOWING ALL ORDERS:


$query = 'SELECT * FROM orders';
$statement = $db->prepare($query);
$statement->execute();
$allOrders = $statement->fetchAll();
$statement->closeCursor();


echo "<table>
                    <tr>
                        <th>OrderID</th>
                        <th>Products</th>
                        <th>Total</th>
                        <th>Invoice</th>

                  
                    </tr>";

foreach($allOrders as $order){

    $orderID = $order['OrderID'];
    $products= $order['OrderCodes'];
    $total =$order['TotalAmount'];
    
    echo          "<tr>          
                      <td>$orderID</td>
                        <td>$products</td>
                        <td>$total</td>
                        <td>
                          </form>
                          <form  action='/show_invoice.php' method='POST'>
                            <input type='hidden' name='orderID' value='$orderID'>
                            <button>View</button>
                          </form>
                        </td>
                  </tr>";
}
 
   echo "</table>";
 ?>
 </section>
  <br></br>



          <a href="/">New Order</a> -
          <a href="/products.php"> Products</a>
 </main>
 </center>
</body>
</html>