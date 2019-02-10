<?php
//$secret = 'e88ebc73104651e3c8ee9af666c19b0626c9ecacd7f8f857e3633e355776baad92e67b7faf9b87744f8c6ce4303978ed65b4165f29534118c882c0fd95f52d0c';
$secret =  '85c6eacf186fe856603423d330aa685beea5988be784cfa54a8a781b2c5b868a8c397bb94310a3ba25dd1ae0265470c2eadf807480e7e05eadd5ca5f54a48def'

// Step 1, grab the onpay_* params and order them alphabetically
$toHashArray = [];
foreach($_POST as $key => $value) {
    if (0 === strpos($key, 'onpay_')) {
        $toHashArray[$key] = $value;
    }
    
}


ksort($toHashArray);

// Step 2, convert to a query string, and lower case
$queryString = strtolower(http_build_query($toHashArray));
// Output: onpay_accepturl=https%3a%2f%2fexample.com%2faccept&onpay_amount=12000&onpay_currency=dkk&onpay_gatewayid=20007895654&onpay_reference=af-847824

// Step 3 calculate the SHA1 HMAC\
$hmac = hash_hmac('sha1', $queryString, $secret);

echo $hmac;


// Output: 16586ad0b3446b58df92446296cf821500ac57d8
                          
?>