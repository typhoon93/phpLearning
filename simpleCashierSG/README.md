This is a project I created during a job application process.

Please have in mind that this is basically the first program I have written in PHP and I haven't gone through any formal training.

So it is safe to say that I haven't used best practices and no design pattern whatsoever. Regardless, I tried to put multiple checks in place for the entered data and tried to make it secure.

The database schema - I had a bit of experience with databases before when I was creating one of my Django applications, but this project was way beyond that, and I am glad to say it taught me a lot, and I feel a bit more comfortable working with DBs now.

Below I will describe the pages in a bit more detail.

The main behavior - placing orders -  can be tested out in the index.php page.

You can place orders with your provided syntax, using A, B, C, D;

An example syntax would be: AAAA

You place an ORDER, if the syntax is valid, it is added to the database. We modify two tables:

1) orders - this table saves the general info about the order:

- OrderID
- OrderCodes(the string you used to place the order)
- Total amount.

2) orderitems - this table saves the individual orders for the products - i.e. if you had ordered 3 A's and 2 B's in the same order, I would save them separately here. It has the following columns:

-OrderItemID(unique for each)
-OrderID (foreign key from the orders table, that references the specific order the items were a part of)
-Name (code of the specific product)
-Quantity (the number of individual items)
-Total price (total price for the set of items.)

The third table that we use is: "products" (it is not modified here, we just use the info from it), it has the info on our products and the promotions for them. The columns are:

-ID - unique, auto-incremented
-Code - specific code for the product, unique
-Price - regular rate
-PromoPrice - the promotional rate
-PromoQuantity - how many items a person needs to buy to take advantage of the promo rate.

-----

/products.php

On the above page you can:

1) Input a new product
2) View all current products
3) Delete a product
4) Update a product

I have also created another page, just so it is a bit more user-friendly.

/orders.php

On the above page, you can view all placed orders. You can also click on "View" next to each order and see a breakdown of the invoice and what was purchased.

Installing on another server:

1. Unzip the attached files, and add them to the main folder of the server.
2. Create a DB / user/pass combination in your server. Import the SQL file from the ZIP there. This will create the DB tables/columns necessary. By default, it has the 4 products that were requested in the main exercise.
3. Edit the "databases.php" file. Variables to edit: 
-$dsn - only modify the DB name;
- $username / $password should be set to the user of that database.
