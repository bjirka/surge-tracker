<?php
$_CONFIG["db_host"] = "localhost";
$_CONFIG["db_username"] = "";
$_CONFIG["db_password"] = "";
$_CONFIG["db_database"] = "redd38_trackmysurge";

$dbLink = mysql_connect ($_CONFIG["db_host"], $_CONFIG["db_username"], $_CONFIG["db_password"]);
mysql_select_db($_CONFIG["db_database"], $dbLink);

$Query = "SELECT * FROM `products`";
$dbResults = mysql_query($Query);
while($dbRow = mysql_fetch_array($dbResults)){
	$_PRODUCTS[$dbRow["productId"]] = $dbRow["name"];
}

$Query = "SELECT locationId, latitude, longitude, locations.name AS locationName, timezones.name AS timezone FROM `locations` JOIN users using(userId) JOIN timezones using(timezoneId) WHERE membershipId > 0";
$dbResults = mysql_query($Query);
while($dbRow = mysql_fetch_array($dbResults)){
	date_default_timezone_set($dbRow["timezone"]);

	$url = "https://api.uber.com/v1/estimates/price?start_latitude=".$dbRow["latitude"]."&start_longitude=".$dbRow["longitude"]."&end_latitude=".$dbRow["latitude"]."&end_longitude=".$dbRow["longitude"]."&server_token=sPtvQ6qRIJFRHwURwdrylCiVgRasC2G-DD9oJ73C";
	//$url = '-H "Authorization: Token sPtvQ6qRIJFRHwURwdrylCiVgRasC2G-DD9oJ73C" -H "Content-Type: application/json" -H "Accept-Language: en_US" "https://api.uber.com/v1.2/products?latitude=37.7752315&longitude=-122.418075"';

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($curl);
	print_r($result);
	echo "<br>";
	$ret = false;

	if ($result !== false){
		$response = json_decode($result, true);

print_r($response);

		if(is_array($response["prices"])){

			foreach($response["prices"] as $product){
				$productId = array_search($product["localized_display_name"], $_PRODUCTS);
				if($productId === false){
					$Query = "INSERT INTO `products` (`name`) VALUES (\"".$product["localized_display_name"]."\")";
					mysql_query($Query);
					$productId = mysql_insert_id();
				}

				$lastSurge = 0;
				$Query = "SELECT * FROM `surge` WHERE locationId = ".$dbRow["locationId"]." AND productId = ".$productId." ORDER BY timestamp DESC LIMIT 1";
				$dbLastSurgeResult = mysql_query($Query);
				if(mysql_num_rows($dbLastSurgeResult)){
					$dbLastSurgeRow = mysql_fetch_array($dbLastSurgeResult);
					$lastSurge = $dbLastSurgeRow["surge"];
				}

				if($lastSurge != $product["surge_multiplier"]){
					$Query = "INSERT INTO `surge` (`locationId`,`timestamp`,`productId`, `surge`) VALUES (".$dbRow["locationId"].",".time().",'".$productId."',".$product["surge_multiplier"].")";
					mysql_query($Query);
				}
echo $product["localized_display_name"]." surge = ".$product["surge_multiplier"]."x<br /><br />";
 
				if($product["surge_multiplier"] > 1){
					$Query = "SELECT * FROM locationNotifications JOIN notifications USING(notificationId) JOIN notificationProducts USING(notificationId) WHERE locationId = ".$dbRow["locationId"]." AND productId = ".$productId;
					$dbNotificationResults = mysql_query($Query);
					while($dbNotificationRow = mysql_fetch_array($dbNotificationResults)){
						if(floor($product["surge_multiplier"]) > floor($dbNotificationRow["lastSurge"])){
							//send email
							mail($dbNotificationRow["email"],$product["localized_display_name"]." Surge Notice", $dbRow["locationName"]." surge is at ".$product["surge_multiplier"]." - ".date("g:ia"), "FROM:Surge Notification<notice@trackmysurge.com>");
							mysql_query("UPDATE `notificationProducts` SET lastNotification = ".time().", lastSurge = ".$product["surge_multiplier"]." WHERE notificationId = ".$dbNotificationRow["notificationId"]." AND productId = ".$productId);

						}else{
							if(time() - $dbNotificationRow["lastNotification"] > 3600){
								//send email
								mail($dbNotificationRow["email"],$product["localized_display_name"]." Surge Notice", $dbRow["locationName"]." surge is at ".$product["surge_multiplier"]." - ".date("g:ia"), "FROM:Surge Notification<notice@trackmysurge.com>");
								mysql_query("UPDATE `notificationProducts` SET lastNotification = ".time().", lastSurge = ".$product["surge_multiplier"]." WHERE notificationId = ".$dbNotificationRow["notificationId"]." AND productId = ".$productId);
							}
						}
					}
				}
			}
		}else{
			//something went wrong
			print_r($response);
			echo "<hr />";
			mail("ben@fishinhole.org","Cron error - TrackMySurge.com", "There is an error with the cron job for TrackMySurge.com", "FROM: error@trackmysurge.com");
		}
	}else{
		echo "$"."result === false";
	}

	curl_close($curl);
}
echo "done";