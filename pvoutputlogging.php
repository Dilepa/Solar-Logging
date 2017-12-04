<?php
// Configuration Options
$dataManagerIP = "Your fronius inverter IP";
$pvOutputApiURL = "http://pvoutput.org/service/r2/addstatus.jsp?";
$pvOutputApiKEY = "Your PVoutput API Key";
$pvOutputSID = "Your PVoutput SSID number";

//Zwave meter options
$meterip = "Your domoticz server address";
$meteridx = "IDX number of the energy monitor";
$metercommand = "/json.htm?type=devices&rid=";


// Inverter & Smart Meter API URLs
$inverterDataURL = "http://".$dataManagerIP."/solar_api/v1/GetInverterRealtimeData.cgi?Scope=Device&DeviceID=1&DataCollection=CommonInverterData";
$meterDataURL = "$meterip$metercommand$meteridx";

// Define Date & Time
date_default_timezone_set("Australia/Melbourne");
$system_time= time();
$date = date('Ymd', time());
$time = date('H:i', time());

// Read Meter Data
$meterJSON = file_get_contents($meterDataURL);
$meterData = json_decode($meterJSON, true);
$meterEnergyTotal = $meterData ["result"][0]["CounterToday"];
$meterEnergyTotal = floatval($meterEnergyTotal) * 1000;

// Read Inverter Data
$inverterJSON = file_get_contents($inverterDataURL);
$inverterData = json_decode($inverterJSON, true);
$inverterPowerLive = $inverterData["Body"]["Data"]["PAC"]["Value"];
$inverterEnergyDayTotal = $inverterData["Body"]["Data"]["DAY_ENERGY"]["Value"];
$inverterVoltageLive = $inverterData["Body"]["Data"]["UAC"]["Value"];

// Push to PVOutput
$pvOutputURL = $pvOutputApiURL
                . "key=" .  $pvOutputApiKEY
                . "&sid=" . $pvOutputSID
                . "&d=" .   $date
                . "&t=" .   $time
                . "&v1=" .  $inverterEnergyDayTotal
                . "&v2=" .  $inverterPowerLive
                . "&v3=" .  $meterEnergyTotal
                . "&v6=" .  $inverterVoltageLive;
file_get_contents(trim($pvOutputURL));
//Print Values to Console
Echo "\n";
Echo "d \t $date\n";
Echo "t \t $time\n";
Echo "v1 \t $inverterEnergyDayTotal\n";
Echo "v2 \t $inverterPowerLive\n";
Echo "v3 \t $meterEnergyTotal\n";
Echo "v6 \t $inverterVoltageLive\n";
Echo "\n";
Echo "Sending data to PVOutput.org \n";
Echo "$pvOutputURL \n";
Echo "\n";

?>
