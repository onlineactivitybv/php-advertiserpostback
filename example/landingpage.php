<?php
declare(strict_types = 1);
ini_set('display_startup_errors', '1');
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Composer autoload
require '../vendor/autoload.php';

use OnlineActivityBV\AdvertiserPostback\AdvertiserPostback; 

$oa = new AdvertiserPostback(1, 'abdddsdsdsjhdshjshj33jhhjdshjc', 'oa_clickid'); 
$oa->landingPage(); 

?>
<strong>Cookies: </strong><br>
<pre><?php
	var_export($_COOKIE ?? null);
?></pre><br><br>
<strong>Session: </strong><br>
<pre><?php
	var_export($_SESSION ?? null);
?></pre>
<br><br>
<strong>ClickId: </strong><br>
<pre><?php
	var_export($oa->getClickId()); 
?></pre>

<a href="thanks.php">Test conversion</a>