<?php

/**
With this endpoint you can obtain Access Token required to access secured endpoints. 
To do so, you have to provide API Key & API Secret. They can be generated on Profile website or, 
if you have Access Token already, with POST request to /developer/api-access.
https://profile.timeular.com/#/app/account
*/
$signin = array();
$signin['apiKey'] = '';
$signin['apiSecret'] = '';

define('stundenlohn',140);
define('mehrwertsteuersatz',19);
define('emptyText','Entwicklung');

define('BaseURL','https://api.timeular.com/api/v2');

setlocale(LC_MONETARY, 'de_DE');


