<?php require("inc-db.php");

$month = 12;
$day = 8;
$year = 2015;

$currentTimestamp = time();

do{
	$timestampStart = strtotime($month."/".$day."/".$year." 6:00am");
	if($day == date("t", $timestampStart)){
		$nextDay = 1;
		if($month == 12){
			$nextMonth = 1;
			$nextYear = $year + 1;
		}else{
			$nextMonth = $month + 1;
			$nextYear = $year;
		}
	}else{
		$nextDay = $day + 1;
		$nextMonth = $month;
		$nextYear = $year;
	}
	$timestampEnd = strtotime($nextMonth."/".$nextDay."/".$nextYear." 6:00am");
//	echo date("n/j/y h:i:sa",$timestampStart)." - ".date("n/j/y h:i:sa",$timestampEnd)." := ";

	$surgeIndex = 0;
	$prevTS = $timestampStart;
	$prevSurge = 1;
	$Query = "SELECT * FROM `surge` WHERE locationId = '1' AND productId = '1' AND timestamp >= ".$timestampStart." AND timestamp < ".$timestampEnd;
	$dbResult = mysql_query($Query);
	while($dbRow = mysql_fetch_array($dbResult)){
		$prevDuration = round(($dbRow["timestamp"] - $prevTS) / 60);
		$surgeIndex += $prevDuration * ($prevSurge - 1);
		$prevTS = $dbRow["timestamp"];
		$prevSurge = $dbRow["surge"];
	}
	$prevDuration = round(($timestampEnd - $prevTS) / 60);
	$surgeIndex += $prevDuration * ($prevSurge - 1);

//	echo "Surge Index: ".$surgeIndex."<br />";

	$Query = "INSERT INTO `surgeIndex` (`locationId`,`productId`,`day`,`month`,`year`,`surgeIndex`) VALUES (1,1,".$day.",".$month.",".$year.",".$surgeIndex.")";
	//mysql_query($Query);

echo $Query."<br />";

	$month = $nextMonth;
	$day = $nextDay;
	$year = $nextYear;

}while($timestampEnd < $currentTimestamp);