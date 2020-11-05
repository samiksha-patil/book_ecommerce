<?php 

// Include config file
include 'config.php';

/* Attempt to connect to MySQL database */
$link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . $link->connect_error);
}

// Create database bookrental
$sql = "CREATE DATABASE IF NOT EXISTS bookrental";
if (mysqli_query($link, $sql)) {
  echo "Database created successfully";
} else {
  echo "Error creating database: " . mysqli_error($link);
}

echo"<br />";

// Use database
$sql = "USE bookrental";
if (mysqli_query($link, $sql)) {
    echo "Database changed successfully";
} else {
    echo "Error using database: " . mysqli_error($link);
}

echo"<br />";

// TABLES

// Customer
$sql = "CREATE TABLE IF NOT EXISTS customer (
	user_id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(40) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    country VARCHAR(255),
    state VARCHAR(255),
    city VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id),
    UNIQUE (email)
);";
if (mysqli_query($link, $sql)) {
    echo "Customer user table created successfully";
} else {
    echo "Error creating customer table: " . mysqli_error($link);
}

echo"<br />";

// Phone number
$sql = "CREATE TABLE IF NOT EXISTS phone_no (
    phone_no VARCHAR(15),
    user_id INT NOT NULL,
    PRIMARY KEY (phone_no),
    FOREIGN KEY (user_id) REFERENCES customer(user_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Phone number table created successfully";
} else {
    echo "Error creating phone number table: " . mysqli_error($link);
}

echo"<br />";

// Book
$sql = "CREATE TABLE IF NOT EXISTS book (
	book_id INT NOT NULL AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    cover_image varchar(255) COLLATE utf8_unicode_ci,
    info VARCHAR(500),
    author VARCHAR(255),
    lang VARCHAR(255),
    no_of_pages INT DEFAULT 0,
    category VARCHAR(255) NOT NULL,
    PRIMARY KEY (book_id)
);";
if (mysqli_query($link, $sql)) {
    echo "Book table created successfully";
} else {
    echo "Error creating book table: " . mysqli_error($link);
}

echo"<br />";

// Cart Items
$sql = "CREATE TABLE IF NOT EXISTS cart_item (
    cart_item_id INT AUTO_INCREMENT,
    is_ordered BOOLEAN DEFAULT FALSE,
    order_id INT,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (cart_item_id),
    FOREIGN KEY (book_id) REFERENCES book(book_id),
    FOREIGN KEY (user_id) REFERENCES customer(user_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Cart item table created successfully";
} else {
    echo "Error creating cart item table: " . mysqli_error($link);
}

echo"<br />";

// Book for sale
$sql = "CREATE TABLE IF NOT EXISTS book_for_sale(
    price INT NOT NULL,
    discount_price INT,
    book_id INT NOT NULL,
    FOREIGN KEY (book_id) REFERENCES book(book_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Book for sale table created successfully";
} else {
    echo "Error creating book for sale table: " . mysqli_error($link);
}

echo"<br />";

// Book for rent
$sql = "CREATE TABLE IF NOT EXISTS book_for_rent (
    monthly_rate INT NOT NULL,
    rating INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    book_id INT NOT NULL,
    FOREIGN KEY (book_id) REFERENCES book(book_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Book for rent table created successfully";
} else {
    echo "Error creating book for rent table: " . mysqli_error($link);
}

echo"<br />";

// Queue
$sql = "CREATE TABLE IF NOT EXISTS queue (
    queue_id INT AUTO_INCREMENT,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM ('Waiting','Pending', 'Cancelled', 'Currently renting', 'Returned'),
    date_of_request DATETIME DEFAULT NOW(),
    date_granted DATETIME,
    date_of_return DATETIME,
    PRIMARY KEY(queue_id),
    FOREIGN KEY (user_id) REFERENCES customer(user_id),
    FOREIGN KEY (book_id) REFERENCES book(book_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Queue table created successfully";
} else {
    echo "Error creating queue table: " . mysqli_error($link);
}

echo"<br />";

// Notifications
$sql = "CREATE TABLE IF NOT EXISTS notification (
    notif_id INT AUTO_INCREMENT,
    queue_id INT NOT NULL,
    timestamp DATETIME DEFAULT NOW(),
    PRIMARY KEY(notif_id),
    FOREIGN KEY (queue_id) REFERENCES queue(queue_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Notification table created successfully";
} else {
    echo "Error creating notification table: " . mysqli_error($link);
}

echo"<br />";

// Order items
$sql = "CREATE TABLE IF NOT EXISTS order_item (
    order_id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    street VARCHAR(255) NOT NULL,
    zipcode VARCHAR(6) NOT NULL,
    state VARCHAR(255) NOT NULL,
    PRIMARY KEY (order_id),
    FOREIGN KEY (user_id) REFERENCES customer(user_id)
    ON DELETE CASCADE
);";
if (mysqli_query($link, $sql)) {
    echo "Order table created successfully";
} else {
    echo "Error creating order items table: " . mysqli_error($link);
}

echo"<br />";

// Constraint
$sql = "ALTER TABLE cart_item
ADD CONSTRAINT FOREIGN KEY (order_id) REFERENCES order_item(order_id);";
if (mysqli_query($link, $sql)) {
    echo "Constraint added successfully";
} else {
    echo "Error adding constraint: " . mysqli_error($link);
}

echo"<br />";

// Payment
$sql = "CREATE TABLE IF NOT EXISTS payment(
    payment_date DATETIME  DEFAULT NOW(),
    payment_amount FLOAT,
    mode_of_payment ENUM ('Net Banking','Cash on Delivery','Credit Card'),
    order_id INT,
    FOREIGN KEY (order_id) REFERENCES order_item(order_id)
);";
if (mysqli_query($link, $sql)) {
    echo "Payment table created successfully";
} else {
    echo "Error creating payment table: " . mysqli_error($link);
}

echo"<br />";

// TRIGGERS

// Create notification
$sql= "CREATE TRIGGER IF NOT EXISTS `create_notif`
AFTER UPDATE ON `queue`
FOR EACH ROW begin
if new.status='Pending' and old.status<>'Pending' then
insert into notification (queue_id) values (new.queue_id);
end if;
end";
if (mysqli_query($link, $sql)) {
    echo "Notification trigger created successfully";
} else {
    echo "Error creating notification trigger: " . mysqli_error($link);
}

// VIEWS

// Book for rent
// $sql= "";
// if (mysqli_query($link, $sql)) {
//     echo "Notification trigger created successfully";
// } else {
//     echo "Error creating notification trigger: " . mysqli_error($link);
// }

echo"<br />";

// Book for sale
// $sql= "";
// if (mysqli_query($link, $sql)) {
//     echo "Book rent view created successfully";
// } else {
//     echo "Error creating book rent view: " . mysqli_error($link);
// }

// Book
$sql='CREATE VIEW IF NOT EXISTS book_view AS
SELECT a.book_id, title, info, author, lang, no_of_pages, cover_image, price, type, category, is_available, user_id AS uploaded_by
FROM (SELECT book.book_id, title, info, author, lang, no_of_pages, cover_image, monthly_rate AS price, "rent" AS type, category, user_id 
      FROM book INNER JOIN book_for_rent 
      ON book.book_id=book_for_rent.book_id) AS a 
      INNER JOIN 
      (SELECT book_for_rent.book_id, IF(c, 0, 1) AS is_available 
       FROM (SELECT book_id, count(*) AS c 
             FROM queue 
             WHERE status="Waiting" or status="Pending" or status="Currently Renting" 
             GROUP BY book_id) AS t
       RIGHT JOIN book_for_rent ON book_for_rent.book_id=t.book_id) AS b 
       ON a.book_id=b.book_id

UNION

SELECT a.book_id, title, info, author, lang, no_of_pages, cover_image, price, type, category, is_available, user_id AS uploaded_by
FROM (SELECT book.book_id, title, info, author, lang, no_of_pages, cover_image, price, "buy" AS type, category, user_id
      FROM book 
      RIGHT JOIN book_for_sale 
      ON book.book_id=book_for_sale.book_id) AS a 
      INNER JOIN (SELECT book_for_sale.book_id, IF(is_ordered, 0, 1) AS is_available 
                  FROM (select book_id, "1" AS is_ordered 
                        FROM cart_item 
                        WHERE is_ordered=1 GROUP BY book_id) AS t 
                  RIGHT JOIN book_for_sale 
                  ON book_for_sale.book_id=t.book_id) AS b 
                  ON a.book_id=b.book_id
                  
ORDER BY book_id';
if (mysqli_query($link, $sql)) {
    echo "Book view created successfully";
} else {
    echo "Error creating book view: " . mysqli_error($link);
}


echo"<br />";

// Queue
$sql= "CREATE VIEW IF NOT EXISTS queue_view AS
SELECT queue.user_id, queue.queue_id, status, book.book_id, title, cover_image, customer.first_name, customer.last_name
FROM queue INNER JOIN book ON book.book_id=queue.book_id INNER JOIN customer ON queue.user_id=customer.user_id WHERE status<>'Returned' AND status<>'Cancelled';";
if (mysqli_query($link, $sql)) {
    echo "Queue view created successfully";
} else {
    echo "Error creating queue view: " . mysqli_error($link);
}

echo"<br />";

// Notification
$sql= "CREATE VIEW IF NOT EXISTS notification_view AS
SELECT notif_id, queue.user_id, queue.queue_id, status, book.book_id, title, cover_image, unix_timestamp(timestamp) as unix_time  
FROM notification INNER JOIN queue ON queue.queue_id=notification.queue_id INNER JOIN book ON book.book_id=queue.book_id;";
if (mysqli_query($link, $sql)) {
    echo "Notification view created successfully";
} else {
    echo "Error creating notification view: " . mysqli_error($link);
}

?>