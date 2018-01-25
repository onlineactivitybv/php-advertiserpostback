<?php
declare(strict_types = 1);
ini_set('display_startup_errors', '1');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Composer autoload
require '../vendor/autoload.php';

use OnlineActivityBV\AdvertiserPostback\AdvertiserPostback; 

$oa = new AdvertiserPostback(1, 'abdddsdsdsjhdshjshj33jhhjdshjc', 'oa_clickid'); 

$clickId = $oa->getClickId(); // STORE CLICKID IN YOUR DATABASE

// Run this on thank-you page, or in background later, or both.
// lead-id should be YOUR unique identifier of the conversion.
// clickId is the click-id passed as GET variable and saved on the landingpage
$conversionStatus = $oa->addConversion($lead_id = 376, $clickId);

var_export($conversionStatus);