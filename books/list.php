<?php

$sql_condition="";
$q_query="";
$q_category="";
if(isset($_GET["category"]) && !empty(trim($_GET["category"]))) 
    if($_GET["category"] == "All")
        $_GET["category"]="";
$q_sort="";
$query="%";
if(isset($_GET["q"]) && !empty(trim($_GET["q"]))){    
    $q_query = trim($_GET["q"]);
    $query="%".$q_query."%";
    $sql_condition .= " WHERE (title LIKE '$query' OR info LIKE '$query' OR author LIKE '$query')";
} 
if(isset($_GET["category"]) && !empty(trim($_GET["category"]))){    
    $q_category = trim($_GET["category"]);
    if(isset($_GET["q"]) && !empty(trim($_GET["q"]))) {
        $sql_condition .= " AND " ;
    }
    else {
        $sql_condition .= " WHERE ";
    }
    $sql_condition .= "category = '$q_category'";
} 
if(isset($_GET["sort"]) && !empty(trim($_GET["sort"]))){    
    $q_sort = trim($_GET["sort"]);
    $sql_condition .= " ORDER BY ";
    if($q_sort=="a-z") $sql_condition .= "title";
    else if($q_sort=="z-a") $sql_condition .= "title DESC";
    else if($q_sort=="newest") $sql_condition .= "book.book_id DESC";
    else if($q_sort=="oldest") $sql_condition .= "book.book_id";
    else if($q_sort == "low-high")   $sql .= "price";
    else if($q_sort == "high-low")   $sql .= "price DESC";
} 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="../static/css/styles.css" />
    <link rel="stylesheet" href="../static/css/list.css" />
    <script src="https://unpkg.com/ionicons@5.2.3/dist/ionicons.js"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  </head>
  <body>
    <div class="container-top">
      <img
        class="img-top"
        src="title-img.jpg"
        alt="Cinque Terre"
        width="1000"
        height="300"
      />
      <div class="center product-title">Shop</div>
    </div>
    <div class="row">
      <div class="search_filters column-30">
        <div class="search">
          <!-- value="<?php echo $q_query ?>" -->
          <div>
            <input
              type="text"
              id="search"
              name="q"
              placeholder="Search..."
              form="search_filter_form"
            />
          </div>
          <div>
            <button
              type="submit"
              form="search_filter_form"
              class="search_button"
            >
              <ion-icon name="search"></ion-icon>
            </button>
          </div>
        </div>
        <div class="container-fluid">
          <form id="search_filter_form">
            <!-- Price Range
                    <br>
                    <input type="nu mber" id="lowest_price" value="<?php echo $q_lowest ?>"></input>-
                    <input type="number" id="highest_price" value="<?php echo $q_highest ?>"></input>
                    <br> -->
            <h3>Sort By</h3>
            <select id="sort" name="sort">
              <option value="low-high">$-$$$</option>
              <option value="high-low">$$$-$</option>
              <option value="a-z">A-Z</option>
              <option value="z-a">Z-A</option>
              <option value="newest">Newest</option>
              <option value="oldest">Oldest</option>
            </select>
            <br />
            <h3>Category</h3>
            <div class="option">
              <input
                type="radio"
                id="Textbook"
                name="category"
                value="Textbook"
              />
              <label for="Textbook">Textbook</label>
            </div>
            <div class="option">
              <input type="radio" id="Memoir" name="category" value="Memoir" />
              <label for="Memoir">Memoir</label>
            </div>
            <div class="option">
              <input type="radio" id="Essays" name="category" value="Essays" />
              <label for="Essays">Essays</label>
            </div>
            <div class="option">
              <input
                type="radio"
                id="Action and Adventure"
                name="category"
                value="Action and Adventure"
              />
              <label for="Action and Adventure">Action and Adventure</label>
            </div>
            <div class="option">
              <input
                type="radio"
                id="Classics"
                name="category"
                value="Classics"
              />
              <label for="Classics">Classics</label>
            </div>
            <div class="option">
              <input
                type="radio"
                id="Comic Book or Graphic Novel"
                name="category"
                value="Comic Book or Graphic Novel"
              />
              <label for="Comic Book or Graphic Novel"
                >Comic Book or Graphic Novel</label
              >
            </div>
            <div class="option">
              <input
                type="radio"
                id="Fiction"
                name="category"
                value="Fiction"
              />
              <label for="Fiction">Fiction</label>
            </div>
            <div class="option">
              <input type="radio" id="All" name="category" value="All" />
              <label for="All">All</label>
            </div>
            <button type="submit" id="apply">Apply</button>
          </form>
        </div>
      </div>
      <div class="column-60">
        <div id="selectionContainer">
          <button class="selection sel-active" onclick="filterSelection('all')">
            All books
          </button>
          <button class="selection" onclick="filterSelection('buy')">
            Books for sale
          </button>
          <button class="selection" onclick="filterSelection('rent')">
            Books for rent
          </button>
        </div>
        <div class="parent">
          <?php
                    $sql = "SELECT book.book_id, title, cover_image, monthly_rate AS price, 'rent' AS type, category FROM book INNER JOIN book_for_rent ON book.book_id=book_for_rent.book_id UNION SELECT book.book_id, title, cover_image, price, 'buy' AS type, category FROM book RIGHT JOIN book_for_sale ON book.book_id=book_for_sale.book_id".$sql_condition;
                    // echo $sql;
                    if($result = mysqli_query($link, $sql)){ ?>
          <div class="page-header clearfix">
            <h2 class="pull-left">
              Books for rent
              <?php echo mysqli_num_rows($result); ?>
            </h2>
          </div>
          <?php if(mysqli_num_rows($result) >
          0){ ?>
          <div class="row books">
            <?php while($row = mysqli_fetch_array($result)){ ?>
            <div class="child filterDiv rent" data-id="<?php echo $row["book_id"] ?>"
            onclick="goToDetail()">
            <div class="container">
              <img style="height: 200px" src="../uploads/<?php echo $row["cover_image"]; ?>"
              alt="" />
              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              <?php echo $row["title"]; ?>
            </div>
            <p style="text-align: center">
              Rs.
              <?php echo $row["price"] ?><?php if($row["type"] === "rent") echo "/month"; ?>
            </p>
          </div>
          <?php } }}?>
          <div class="child filterDiv rent" data-id="1" onclick="goToDetail()">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />
              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv rent">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART </a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
              <p style="text-align: center">$5</p>
            </div>
          </div>

          <div class="child filterDiv buy">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#">ADD TO CART </a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv buy">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />
              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv buy">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv buy">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv buy">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv rent">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
          <div class="child filterDiv rent">
            <div class="container">
              <img style="height: 200px" src="book.png" alt="" />

              <div class="overlay"></div>
              <div class="button"><a href="#"> ADD TO CART</a></div>
            </div>
            <div
              class="product-title"
              style="font-size: 22px; text-align: center"
            >
              Most Popular Edition
            </div>
            <p style="text-align: center">$5</p>
          </div>
        </div>
      </div>
    </div>
    <script>
      filterSelection("all");
      function filterSelection(c) {
        var x, i;
        x = document.getElementsByClassName("filterDiv");
        if (c == "all") c = "";
        for (i = 0; i < x.length; i++) {
          w3RemoveClass(x[i], "show");
          if (x[i].className.indexOf(c) > -1) w3AddClass(x[i], "show");
        }
      }

      function w3AddClass(element, name) {
        var i, arr1, arr2;
        arr1 = element.className.split(" ");
        arr2 = name.split(" ");
        for (i = 0; i < arr2.length; i++) {
          if (arr1.indexOf(arr2[i]) == -1) {
            element.className += " " + arr2[i];
          }
        }
      }

      function w3RemoveClass(element, name) {
        var i, arr1, arr2;
        arr1 = element.className.split(" ");
        arr2 = name.split(" ");
        for (i = 0; i < arr2.length; i++) {
          while (arr1.indexOf(arr2[i]) > -1) {
            arr1.splice(arr1.indexOf(arr2[i]), 1);
          }
        }
        element.className = arr1.join(" ");
      }

      // Add active class to the current button (highlight it)
      var btnContainer = document.getElementById("selectionContainer");
      var btns = btnContainer.getElementsByClassName("selection");
      for (var i = 0; i < btns.length; i++) {
        btns[i].addEventListener("click", function () {
          var current = document.getElementsByClassName("sel-active");
          current[0].className = current[0].className.replace(
            " sel-active",
            ""
          );
          this.className += " sel-active";
        });
      }

      function goToDetail(e) {
        console.log($(e.target).data("id"));
      }
      
    document.getElementById("sort").value ='<?php echo $q_sort ?>';
    var value="<?php echo $q_category; ?>";
    if(value=="") value="All";
    value.replaceAll('+',' ');
    console.log(value);
    $("input[name=category][value='" + value + "']").prop('checked', true);
    </script>
  </body>
</html>
 