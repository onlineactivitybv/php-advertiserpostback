# php-advertiserpostback
PHP Library to setup postback tracking

[Postback concept explained](doc/postbackconcept.md)

### Installation

Composer: 

    $ composer require onlineactivitybv/php-advertiserpostback "^1"
    
#### Requirements

PHP 7.0+, Curl
### Usage

Init:

```php
require __DIR__ . '/vendor/autoload.php';

use OnlineActivityBV\AdvertiserPostback\AdvertiserPostback; 

$oa = new AdvertiserPostback(1, /* Here advertiser ID given by OA **/
     'abdddsdsdsjhdshjshj33jhhjdshjc', /* Here advertiser hash given by OA **/ 
     'oa_clickid' /* $_GET variable name used to send click id to lander **/); 
```

----------
Landingpage: 

Add this code to the TOP of your landing page (sets cookie so needs to be before any other output.
```php
$oa->landingPage(); 
```

----------
Thank you page:

Save OA ClickId to your database and link to conversion
```php
$clickId = $oa->getClickId(); 
```

With ClickID you can either choose to run postback on thank you page or you can call the postback on a later moment, with the clickId extracted from your database.

```php
// first outout thank you HTML.
fastcgi_finish_request(); // optional, recommended not to delay output of thank you HTML.
$conversionStatus = $oa->addConversion($lead_id = 376, $clickId);
```

