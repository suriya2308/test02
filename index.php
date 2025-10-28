<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="style.css">
<body>

<?php
session_start();

// Database credentials
$servername = "localhost";
$username = "suriyafunflys_user";
$password = "Suriya@123456";
$dbname = "suriyafunflys_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// --------------------
// ADD TO CART FUNCTION
// --------------------
if (isset($_POST['ac'])) {
    $bookID = $_POST['ac'];
    $quantity = intval($_POST['quantity']);

    // Get book details
    $sql = "SELECT * FROM Book WHERE BookID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bookID);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        $price = $book['Price'];
        $totalPrice = $price * $quantity;

        // Insert into cart
        $insert = $conn->prepare("INSERT INTO cart (BookID, Quantity, Price, TotalPrice) VALUES (?, ?, ?, ?)");
        $insert->bind_param("sidd", $bookID, $quantity, $price, $totalPrice);
        $insert->execute();
    }
}

// --------------------
// EMPTY CART FUNCTION
// --------------------
if (isset($_POST['delc'])) {
    $conn->query("DELETE FROM cart");
}

// --------------------
// PAGE HEADER
// --------------------
if (isset($_SESSION['id'])) {
    echo '<header><blockquote>';
    echo '<a href="index.php"><img src="image/logo.png" alt="Logo"></a>';
    echo '<form class="hf" action="logout.php"><input class="hi" type="submit" value="Logout"></form>';
    echo '<form class="hf" action="edituser.php"><input class="hi" type="submit" value="Edit Profile"></form>';
    echo '</blockquote></header>';
} else {
    echo '<header><blockquote>';
    echo '<a href="index.php"><img src="image/logo.png" alt="Logo"></a>';
    echo '<form class="hf" action="register.php"><input class="hi" type="submit" value="Register"></form>';
    echo '<form class="hf" action="login.php"><input class="hi" type="submit" value="Login"></form>';
    echo '</blockquote></header>';
}

// --------------------
// DISPLAY BOOK LIST
// --------------------
echo '<blockquote>';
echo "<table id='myTable' style='width:80%; float:left'>";
echo "<tr>";

$sql = "SELECT * FROM Book";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<td>";
        echo "<table>";
        echo '<tr><td><img src="' . $row["Image"] . '" width="80%"></td></tr>';
        echo '<tr><td style="padding:5px;">Title: ' . htmlspecialchars($row["BookTitle"]) . '</td></tr>';
        echo '<tr><td style="padding:5px;">ISBN: ' . htmlspecialchars($row["ISBN"]) . '</td></tr>';
        echo '<tr><td style="padding:5px;">Author: ' . htmlspecialchars($row["Author"]) . '</td></tr>';
        echo '<tr><td style="padding:5px;">Type: ' . htmlspecialchars($row["Type"]) . '</td></tr>';
        echo '<tr><td style="padding:5px;">RM' . $row["Price"] . '</td></tr>';
        echo '<tr><td style="padding:5px;">
                <form action="" method="post">
                    Quantity: <input type="number" value="1" name="quantity" min="1" style="width:20%"/><br>
                    <input type="hidden" name="ac" value="' . $row['BookID'] . '"/>
                    <input class="button" type="submit" value="Add to Cart"/>
                </form>
              </td></tr>';
        echo "</table></td>";
    }
}
echo "</tr></table>";

// --------------------
// DISPLAY CART
// --------------------
$sql = "SELECT Book.BookTitle, Book.Image, Cart.Price, Cart.Quantity, Cart.TotalPrice 
        FROM Book INNER JOIN Cart ON Book.BookID = Cart.BookID";
$result = $conn->query($sql);

echo "<table style='width:20%; float:right;'>";
echo "<th style='text-align:left;'>
        <i class='fa fa-shopping-cart' style='font-size:24px'></i> Cart 
        <form style='float:right;' action='' method='post'>
            <input type='hidden' name='delc' value='1'/>
            <input class='cbtn' type='submit' value='Empty Cart'>
        </form>
      </th>";

$total = 0;
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>";
        echo '<img src="' . $row["Image"] . '" width="20%"><br>';
        echo htmlspecialchars($row['BookTitle']) . "<br>RM" . $row['Price'] . "<br>";
        echo "Quantity: " . $row['Quantity'] . "<br>";
        echo "Total Price: RM" . $row['TotalPrice'];
        echo "</td></tr>";
        $total += $row['TotalPrice'];
    }
    echo "<tr><td style='text-align:right;background-color:#f2f2f2;'>";
    echo "Total: <b>RM" . $total . "</b>";
    echo "<center><form action='checkout.php' method='post'>
            <input class='button' type='submit' name='checkout' value='CHECKOUT'>
          </form></center>";
    echo "</td></tr>";
} else {
    echo "<tr><td>Your cart is empty.</td></tr>";
}

echo "</table>";
echo '</blockquote>';

$conn->close();
?>

</body>
</html>

