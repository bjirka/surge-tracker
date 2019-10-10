<?php require("inc-db.php"); require("class.surge.php");
$locationId = (int)$_GET["l"];
if($locationId==0){
	$Query = "SELECT * FROM `locations` WHERE isDefault";
	$dbResult = mysql_query($Query);
	$dbRow = mysql_fetch_array($dbResult);
	$locationId = $dbRow["locationId"];
}
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>College Station Uber Surge History</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
$(function(){
	$(".scrollbox").on('scroll',function(){
		$(".scrollbox").scrollLeft($(this).scrollLeft());
	});
	$("#datepicker").datepicker({minDate:"-2Y", maxDate:"-1D",onSelect: function(dateText, inst){
		$(this).hide();
		dateParts = dateText.split("/");
		window.location.assign('http://surgeninja.com/?l=<?php echo $locationId;?>&y=' + dateParts[2] + '&m=' + dateParts[0] + '&d=' + dateParts[1])
	}});
	$("#surgeHistoryHeader").click(function(){
		$("#surgeHistoryBox").toggle();
	});
	//$("#currentSurge .scrollbox").scrollLeft(350);
});
</script>
<style>
body{margin:0;}
div{
	position:relative;
	font-family:Verdana;
	font-size:10px;
}
#datepicker{
	display:block;
	margin:0 auto;
	background:#ccc;
	font-weight:bold;
	text-align:Center;
}
</style>
</head>
 
<body onLoad="window.scroll(0,25);">
<div><select style="width:100%;height:25px;" onChange="window.location.assign('http://surgeninja.com/?l='+this.value)"><?php
$Query = "SELECT * FROM `locations` ORDER BY name";
$dbResult = mysql_query($Query);
while($dbRow = mysql_fetch_array($dbResult)){
	echo "<option value=".$dbRow["locationId"];
	if($dbRow["locationId"] == $locationId) echo " selected";
	echo ">".$dbRow["name"]."</option>";
}
?></select></div>
<div id="surgeHistoryHeader" style="font-weight:bold;font-size:14px;line-height:40px;background:black;color:white;">surge history</div>
<div id="surgeHistoryBox" style="display:none;">
<?php
$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge > 1 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;font-size:14px;'>Last Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 2 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 2x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 3 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 3x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 4 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 4x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 5 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 5x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 6 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 6x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 7 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 7x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";

$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND surge >= 8 ORDER BY timestamp DESC LIMIT 1";
$dbResult = mysql_query($Query);
$dbRow = mysql_fetch_array($dbResult);
echo "<div style='font-weight:bold;float:left;padding-right:7px;'>Last 8x Surge: <span style='font-weight:normal'>".date("n/j g:ia",$dbRow["timestamp"])." (".$dbRow["surge"]."x)</span></div>";
?>
<div style='clear:both;'>&nbsp;</div>
</div>
<?php $surge = new Surge();
if(isset($_GET["y"]) && isset($_GET["m"]) && isset($_GET["d"])){
	$year = (int)$_GET["y"];
	$month = (int)$_GET["m"];
	$day = (int)$_GET["d"];
	$surge->showSurge($locationId, strtotime($month."/".$day."/".$year)+100800, 28, date('l - M jS, Y',strtotime($month."/".$day."/".$year))." - <span class='surgeIndex' style='color:#acf2ff;font-weight:bold;'>".($surge->getSurgeIndex(strtotime($month."/".$day."/".$year." 10am")))."</span>", "customSurge1");
}else{
	$surge->showCurrentSurge($locationId);
}
?>
<div style="width:100%;padding-bottom:50px;"><input type="text" id="datepicker" value="Select Custom Date" readonly></div>
</body>
</html>