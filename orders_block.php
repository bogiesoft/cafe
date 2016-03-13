<script>
/**change block's style after click pay,edit,delete and more buttons*/	
	function paidstyle(orid){	
		document.getElementById('obnav'+orid).style.border='2px solid #0ab159';
		document.getElementById('obnav'+orid).style.boxShadow='0px 0px 5px #0ab159';
		document.getElementById('paid'+orid).style.visibility='visible';
		document.getElementById('btn2'+orid).style.display='none';
		if(document.getElementById('more'+orid)){
			document.getElementById('more'+orid).style.wordSpacing='37px'; 
		}
	}
	function submit(orid){
		document.getElementById('formd'+orid).submit();
	}
	function foldbtn(orid){
		document.getElementById('ob'+orid).style.height='300px';
		document.getElementById('obnav'+orid).style.height= '280px';
		document.getElementById('more'+orid).style.display='inline';
		document.getElementById('fold'+orid).style.display='none';
		var x1 = document.getElementsByClassName('obtd'+orid);
		var i1;
		for (i1=0;i1<x1.length;i1++){
			x1[i1].style.display= 'none';
		}
	}
	function morebtn(orid,t){
		document.getElementById('ob'+orid).style.height= ((40*(t-3))+300)+'px';
		document.getElementById('obnav'+orid).style.height= ((40*(t-3))+280)+'px';
		document.getElementById('more'+orid).style.display='none';
		document.getElementById('fold'+orid).style.display='inline';
		document.getElementById('fold'+orid).style.borderTop='none';
		var x = document.getElementsByClassName('obtd'+orid);
		var i;
		for (i=0;i<x.length;i++){
			x[i].style.display= 'inline';
		}
	}
	
	function payOrder(orid){
		if(confirm('Do you want to Pay order No.'+orid+'?')){
			document.getElementsByName('paid'+orid)[0].click();
		}
	}
	function deleteOrder(orid){
	if(confirm('Do you want to delete '+orid+' order ?')){
		document.getElementById('del'+orid).click();
	}
}
</script>
<?php
	include 'timecond.php';
/**query all the orders and customer information in limited condition*/	
	$sql_orders = "select Order_id,o.cus_id,firstname as fname,lastname as lname,Date,Time,payed from orders as o LEFT JOIN customer_info as c ON o.cus_id = c.cus_id $condition ORDER BY order_id DESC";
	$result = $mysql->query($sql_orders);
	while($row_order = $mysql->fetch($result)) {
		if(empty($row_order['lname'])&&empty($row_order['fname'])){
			$cusname='Unknown';
		}else{
			$cusname=$row_order['fname']."&nbsp".$row_order['lname'];
		}
?>
<div class='order_block' id='ob<?php echo $row_order[0];?>'>
  <div class='ob_nav' id='obnav<?php echo $row_order[0];?>'>
	<table class ='table-stripped' id='ob_tbl'>
		<tr>
		<?php
/**count one orders' total price and item number*/
		$sql_order_price = "select sum(f.price * quantity) as order_price, count(*) as num from order_food inner join food_catalogue as f ON order_food.food_id = f.food_id where order_id = {$row_order['Order_id']}";
			$res=$mysql->query($sql_order_price);
			$row_item=$mysql->fetch($res);
			$num=$row_item['num'];
			echo "<th colspan='2'>$cusname</th>";
			echo "<th class='text-right' colspan='2'>".substr($row_order['Date'],5)."&nbsp".substr($row_order['Time'],0,5)."</th>";
			?>
		</tr>
		<tr id='ob_tbl_th'>
			<td contenteditable="true">Food Name</td>
			<td>Quantity</td>
			<td>Single Price</td>
			<td>Total Price</td>
		</tr>
		<?php
/**find and show detail information of each order*/
		$sql_item_detail = "SELECT Item_id,F.order_id,cus.lastname as lname,Cs.cata_name as Food_name,Cp.Cata_name,Quantity,Cs.price as Single_Price,(Cs.price*quantity)as Total_Price,F.food_id from order_food as F JOIN orders as O on F.order_id = O.order_id JOIN food_catalogue as Cs ON F.food_id = Cs.food_id JOIN food_catalogue as Cp ON Cp.food_id = Cs.catalog_id LEFT JOIN customer_info as cus ON cus.cus_id = O.cus_id WHERE F.order_id= {$row_order['Order_id']}";
			$result_item_detail = $mysql->query($sql_item_detail);
/**action to create_order to edit order if user need*/
			echo "<form id='formd{$row_order['Order_id']}' method='post' action='index.php?page=create_order'>";
			$showtimes=0;
			while($row_item_detail = $mysql->fetch($result_item_detail)) {
				$showtimes++;
/**if an order have more than 4 items, it should be fold*/
				if ($showtimes<4){
					echo "<tr id='ob_tbl_tb' >
							<td>".$row_item_detail['Food_name']." </td>
							<td>".$row_item_detail['Quantity']." </td>
							<td>&#165;".$row_item_detail['Single_Price']." </td>
							<td>&#165;".$row_item_detail['Total_Price']." </td>
						</tr>";
				}else{
					echo "<tr id='ob_tbl_tb1'>
							<td  class='obtd$row_order[0]'>".$row_item_detail['Food_name']." </td>
							<td  class='obtd$row_order[0]'>".$row_item_detail['Quantity']." </td>
							<td  class='obtd$row_order[0]'>&#165;".$row_item_detail['Single_Price']." </td>
							<td  class='obtd$row_order[0]'>&#165;".$row_item_detail['Total_Price']." </td>
						</tr>";
				}
/**save food_id, quantity, order_id and cus_id in hidden input*/	
				echo "<input type='hidden' name='fd_quan[{$row_item_detail['food_id']}]' value='{$row_item_detail['Quantity']}'/>";
			}
			echo "<input type='hidden' name='od_cus[{$row_order['Order_id']}]' value='{$row_order['cus_id']}'/>
				</form>";
			/*fill in blank row if the order have less than 3 items*/	
			for($n=$num;$n<3;$n++){
				echo "<tr id='ob_tbl_tb'><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td></tr>";
			}		
			echo "<tr id='ob_tbl_tb'>
					<th colspan='2' id='paid'><span id='paid$row_order[0]'>Paid</span></th>
					<th class='text-right' colspan='2'>{$row_item['order_price']}&nbspRMB</th>
				</tr>";
		?>
	</table>
		<nav id='paybtn'>
			<span id="btn2<?php echo $row_order[0];?>">
				<form method='get' action=''>
					<button type="primary" name='paid<?php echo $row_order[0];?>' style='display:none;'/>
				</form>
				<button type="primary"  onclick="payOrder('<?php echo $row_order[0];?>')">Pay</button>
				<button type='submit' name='edit' onclick="submit('<?php echo $row_order[0];?>')">Edit</button>
				<form method='post' action=''>
					<kbd onclick="deleteOrder('<?php echo $row_order['Order_id'];?>')">x</kbd>
					<input id='del<?php echo $row_order['Order_id'];?>' type='submit' name='nam' value='<?php echo $row_order['Order_id'];?>' style='display:none;'/>
				</form>
			</span>
			<?php
/**function of pay order*/
			if(isset($_GET["paid$row_order[0]"])){
				$sql_pay = "UPDATE orders SET payed=1 WHERE order_id= $row_order[0]";
				$mysql->query($sql_pay);
				echo "<script>paidstyle($row_order[0]);</script>";
			}
/**change style if more than 3 row*/
			if($num > 3){	
				echo "<div id='more$row_order[0]' class='morerow' onclick='morebtn($row_order[0],$num)'>
						&nbsp&nbsp&nbsp;<a>More</a>&nbsp &nbsp &nbsp;
					</div>
					<div id='fold$row_order[0]' class='morerow' style='display:none;' onclick='foldbtn($row_order[0])'>
						&nbsp&nbsp&nbsp;<a>Fold</a>&nbsp &nbsp &nbsp;
					</div>";
			}
			if($row_order['payed']==1){
				echo "<script>paidstyle('$row_order[0]')</script>";
			}
			?>
		</nav>
  </div>
</div>
<?php
	}
/**function of delete order*/
	if(isset($_POST['nam'])){
			$delId = $_POST['nam'];
			echo "<script>document.getElementById('obnav'+$delId).style.display='none';</script>";
			$mysql->query("DELETE FROM orders WHERE order_id = $delId");
		}
/**if row_item is not empty,row_order must not be empty(no orders).Because it's outside of while loop,so it only can use row_item*/		
	if(empty($row_item)){
		echo "No orders for $timestamp yet";
	}
	
?>
