<?php
require_once('.config');
require_once('aws-sdk/aws-autoloader.php');

$cloudFront = new Aws\CloudFront\CloudFrontClient([
    'region'  => 'eu-west-2',
    'version' => '2016-01-28'
]);

$expires = time() + 60*60;
$customPolicy = <<<POLICY
{
    "Statement": [
        {
            "Resource": "http://www.",
            "Condition": {
                "DateLessThan": {"AWS:EpochTime": {$expires}}
            }
        }
    ]
}
POLICY;

// Create a signed cookie for the resource using a custom policy
$signedCookieCustomPolicy = $cloudFront->getSignedCookie([
	'policy' => $customPolicy,
	'private_key' => SITE_PRIVKEY_LOC,
	'key_pair_id' => SITE_KEYPAIR_ID
]);
// headers specific for explorer
//drupal_add_http_header('P3P:CP',"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT");
foreach ($signedCookieCustomPolicy as $name => $value) {
    setcookie($name, $value, strtotime(SITE_COOKY_EXPIRY), "/", SITE_COOKIE_DOMAIN, false, true);
}
?>
<html>
<head>
  <title>Race Disparity Unit</title>
  <meta http-equiv="refresh" content="0; url=<?php echo SITE_DOMAIN_NAME; ?>">
</head>
<body>
  <a href="<?php echo SITE_DOMAIN_NAME; ?>">Go to site</a>
</body>
</html>
