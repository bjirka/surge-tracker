<?php
class Surge{
	private $timestamp;
	private $minuteWidth = 3; //how many pixels wide each minute is

	function __construct($timestamp=0){
		if($timestamp == 0) $timestamp = time();
		$this->timestamp = $timestamp;
	}

	function showCurrentSurge($locationId){
		$this->showSurge($locationId, time()+7200, 12, "Last 12 Hours", "currentSurge");
		$this->showSurge($locationId, strtotime("-1 week")+7200, 12, "One Week Ago - <span class='surgeIndex' style='color:#acf2ff;font-weight:bold;'>".($this->getSurgeIndex(strtotime("-1 week")))."</span>", "pastSurge1");
		$this->showSurge($locationId, strtotime("-2 week")+7200, 12, "Two Weeks Ago - <span class='surgeIndex' style='color:#acf2ff;font-weight:bold;'>".($this->getSurgeIndex(strtotime("-2 week")))."</span>", "pastSurge2");
	}

	function showSurge($locationId, $timestamp = 0,$hours=12,$title="",$id=""){
		$Query = "SELECT * FROM `surge` WHERE locationId = ".$locationId." AND productId = 1 AND timestamp <= ".$timestamp." ORDER BY timestamp DESC";
		$dbResult = mysql_query($Query);
		$lastTS = $timestamp;
		$totalWidth = (60 * $hours * $this->minuteWidth);
		$remainingWidth = $totalWidth;

		echo "<div id='".$id."' style='padding-bottom:20px;'><div style='background:#b5b5ba;color:#fff;font-weight:bold;font-size:11px;padding-left:5px;line-height:20px;'>&nbsp;".$title."</div><div style='width:100%;height:150px;background:#e9e5dc;'>";
			echo "<div style='border-top:1px solid #f4f2ee;width:100%;height:63px;position:absolute;bottom:0px;'></div>";
			echo "<div style='border-top:1px solid #f4f2ee;width:100%;height:88px;position:absolute;bottom:0px;'></div>";
			echo "<div style='border-top:1px solid #f4f2ee;width:100%;height:113px;position:absolute;bottom:0px;'></div>";
			echo "<div style='border-top:1px solid #f4f2ee;width:100%;height:138px;position:absolute;bottom:0px;'></div>";
			echo "<div style='position:absolute;left:2px;top:79px;z-index:999;'>2.0</div>";
			echo "<div style='position:absolute;left:2px;top:54px;z-index:999;'>3.0</div>";
			echo "<div style='position:absolute;left:2px;top:29px;z-index:999;'>4.0</div>";
			echo "<div style='position:absolute;left:2px;top:4px;z-index:999;'>5.0</div>";
			echo "<div class='scrollbox' style='width:100%;height:165px;overflow-x:scroll;direction:rtl;' id='surgeChart2'><div style='height:130px;position:relative;right:0;'><div id='divBox2' style='position:absolute;bottom:0;right:0;width:".$totalWidth."px;'>";


		if($lastTS > time()){
			$duration = round(($lastTS - time()) / 60);
			$width = $duration * $this->minuteWidth - 1;
			$background = "background:url(images/future-bg.gif);";
			echo "<div style='".$background."width:".$width."px;height:128px;position:relative;bottom:0;display:inline-block;border-left:1px solid black;'></div>";
			$lastTS = time();
			$remainingWidth -= ($width + 1);
		}


		while($dbRow = mysql_fetch_array($dbResult)){
			$duration = round(($lastTS - $dbRow["timestamp"]) / 60);
			$surge = $dbRow["surge"];
			$width = $duration * $this->minuteWidth;
			if($width > $remainingWidth) $width = $remainingWidth;
			switch($surge){
				case -1:
					$height = 50;
					$background = "background:red;";
					break;
				case 1:
					$height = 10;
					$background = "background:#000;";
					break;
				default:
					$height = round($dbRow["surge"] * 25 - 10);
					$background = "background:#1fbad6;";
					break;
			}
			echo "<div style='".$background."width:".$width."px;height:".$height."px;position:relative;bottom:0;display:inline-block;'></div>";
			$lastTS = $dbRow["timestamp"];
			$remainingWidth -= $width;
			if($dbRow["timestamp"] < ($timestamp - (3600 * $hours))) break;
		}

		echo "</div><div id='timeBox2' style='background:#fff;width:".$totalWidth."px;height:20px;position:absolute;right:0;bottom:-18px;text-align:right;overflow:hidden;'>";
		$minuteInterval = 15;
		$currentMinute = date("i", $timestamp);
		$extraMinutes = $currentMinute % $minuteInterval;
		$firstWidth = ($minuteInterval  + $extraMinutes) * $this->minuteWidth;
		echo "<div style='font-size:9px;width:".($firstWidth)."px;display:inline-block;padding-top:7px;position:relative;'><div style='width:1px;height:15px;background:#ccc;position:absolute;top:0;right:1px;'></div><div>".date("h:i",$timestamp)."</div></div>";
		$ts = ($timestamp) - (($minuteInterval + $extraMinutes) * 60);
		for($i=1;$i<($hours * (60 / $minuteInterval));$i++){
			echo "<div style='font-size:9px;width:".($minuteInterval * $this->minuteWidth)."px;display:inline-block;padding-top:7px;position:relative;'><div style='width:1px;height:15px;background:#ccc;position:absolute;top:0;right:-1px;'></div><div style='margin-right:-12px;'>".date("h:i",$ts)."</div></div>";
			$ts -= (60 * $minuteInterval);
		}
		echo "</div></div></div></div></div>";
	}

	function getSurgeIndex($ts){
		if(date("G",$ts) < 6){
			$ts = $ts - 43200;
		}
		$day = date("j",$ts);
		$month = date("n",$ts);
		$year = date("Y",$ts);
		$Query = "SELECT * FROM `surgeIndex` WHERE day = ".$day." AND month = ".$month." AND year = ".$year;
		$dbResult = mysql_query($Query);
		$dbRow = mysql_fetch_array($dbResult);
		return $dbRow["surgeIndex"];
	}

	function renderSurgeDiv(){
	}
}