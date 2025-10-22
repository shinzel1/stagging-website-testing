<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhonePe\Env;
use PhonePe\payments\v1\models\request\builders\InstrumentBuilder;
use PhonePe\payments\v1\models\request\builders\PgPayRequestBuilder;
use PhonePe\payments\v1\PhonePePaymentClient;

if($_SERVER['REQUEST_METHOD']=='POST'){ 
    try {
        $phonePePaymentsClient = new PhonePePaymentClient(API_MERCHAT_ID, API_KEY, API_KEY_INDEX, ENV,true);
        $order_id = $_POST['order_id'];
        $order_amount = $_POST['order_amount'] * 100; // Convert to paisa
        $request = PgPayRequestBuilder::builder()
            ->mobileNumber($_POST['mobileNumber'])
            ->callbackUrl(WEBHOOK_PATH) 
            ->redirectUrl(RESPONSE_PATH."?order_id=$order_id") 
            ->merchantId(API_MERCHAT_ID)
            ->amount($order_amount)
            ->merchantTransactionId($order_id)
            ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
            ->build();
        $response = $phonePePaymentsClient->pay($request);
        $PagPageUrl = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();
        echo "<script>location.href='".$PagPageUrl."';</script>";
        exit;   
    }catch (Throwable  $e) {
        echo $e;
        exit;
    }
}else{
    echo "<script>location.href='index.php';</script>";
}

