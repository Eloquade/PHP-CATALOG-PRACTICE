<?php
	session_start();
	require_once("dbcontroller.php");
	$db_handle = new DBController();
	if(!empty($_GET["action"])) {
		switch($_GET["action"]) {
			case "add":
				if(!empty($_POST["quantity"])) {
					$productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
					$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));

					if(!empty($_SESSION["cart_item"])) {
						if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
							foreach($_SESSION["cart_item"] as $k => $v) {
									if($productByCode[0]["code"] == $k) {
										if(empty($_SESSION["cart_item"][$k]["quantity"])) {
											$_SESSION["cart_item"][$k]["quantity"] = 0;
										}
										$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
									}
							}
						} else {
							$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
						}
					} else {
						$_SESSION["cart_item"] = $itemArray;
					}
				}
			break;
			case "remove":
				if(!empty($_SESSION["cart_item"])) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($_GET["code"] == $k)
								unset($_SESSION["cart_item"][$k]);				
							if(empty($_SESSION["cart_item"]))
								unset($_SESSION["cart_item"]);
					}
				}
			break;
			case "empty":
				unset($_SESSION["cart_item"]);
			break;	
		}
	}
?>
<html>
	<head>
		<title>Catalog PHP</title>
		<link href="style.css" type="text/css" rel="stylesheet" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	</head>
	<body>
		<div id="shopping-cart">
		<div class="txt-heading"><h2>Shopping Cart</h2></div>

		<a id="btnEmpty" href="catalog.php?action=empty" class="btn btn-outline-danger">Empty Cart</a>
		<?php
			if(isset($_SESSION["cart_item"])){
			    $total_quantity = 0;
			    $total_price = 0;
		?>	
		<table class="tbl-cart" cellpadding="10" cellspacing="1">
		<tbody>
			<tr>
				<th style="text-align:left;">Name</th>
				<th style="text-align:left;">Code</th>
				<th style="text-align:right;" width="5%">Quantity</th>
				<th style="text-align:right;" width="10%">Unit Price</th>
				<th style="text-align:right;" width="10%">Price</th>
				<th style="text-align:center;" width="5%">Remove</th>
			</tr>	
			<?php		
		    	foreach ($_SESSION["cart_item"] as $item){
		    	    $item_price = $item["quantity"]*$item["price"];
					?>
							<tr>
								<td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
								<td><?php echo $item["code"]; ?></td>
								<td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
								<td  style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
								<td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
								<td style="text-align:center;"><a href="catalog.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
							</tr>
							<?php
							$total_quantity += $item["quantity"];
							$total_price += ($item["price"]*$item["quantity"]);
				}
			?>

			<tr>
				<td colspan="2" align="right">Total:</td>
				<td align="right"><?php echo $total_quantity; ?></td>
				<td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
				<td></td>
			</tr>
		</tbody>
		</table>		
		  <?php
		} else {
		?>
		
		<div class="no-records">
			<div class="alert alert-success" role="alert">
			  <h4 class="alert-heading">Your Cart is Empty</h4>
			  <hr>
			  <p class="mb-0">Keep on Shopping.</p>
			</div>
		</div>
		<?php 
		}
		?>
		</div>

		<div id="product-grid">
			<div class="txt-heading"><h1>Products</h1></div>
			<?php
				$product_array = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
				if (!empty($product_array)) { 
					foreach($product_array as $key=>$value){
				?>
					<form method="post" action="catalog.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
						<div class="container mt-5 mb-5">
						    <div class="d-flex justify-content-center row">
						        <div class="col-md-10">
						            <div class="row p-2 bg-white border rounded mt-2">
						                <div class="col-md-3 mt-1"><img class="img-fluid img-responsive rounded product-image" src="<?php echo $product_array[$key]["image"]; ?>"></div>
						                <div class="col-md-6 mt-1">
						                    <h5><?php echo $product_array[$key]["name"]; ?></h5>
						                    <p class="text-justify text-truncate para mb-0">
												Lorem ipsum dolor sit amet, consectetur adipiscing elit. <br>
												Proin vel dui elit. Vivamus ultricies elit vel egestas. <br>
												Proin ut luctus augue. Sed at interdum nisi. Sed at fe.<br>
												Nulla elementum scelerisque sem a accumsan. Curabitur so. 
											</p>
						                </div>
						                <div class="align-items-center align-content-center col-md-3 border-left mt-1">
						                    <div class="d-flex flex-row align-items-center">
						                        <h4 class="mr-1"><?php echo "$".$product_array[$key]["price"]; ?></h4>
						                    </div>
						                    <h6 class="text-success">Free shipping</h6>
						                    <div class="d-flex flex-column mt-4">
												<div class="cart-action">
													<input type="text" class="product-quantity" name="quantity" value="1" size="2" />
													<input type="submit" value="Add to Cart" class="btnAddAction btn btn-outline-primary" />
												</div>
											</div>
						                </div>
						            </div>
						        </div>
						    </div>
						</div>
					</form>
			<?php
				}
			}
			?>
		</div>

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
	</body>
</html>
