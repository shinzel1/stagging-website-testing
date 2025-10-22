<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/PhonePeHelper.php';

if($_SERVER['REQUEST_METHOD']=='POST'){ 
    try {
        $data = $_POST;
        // $data['order_id'] = "order_68c903030d8ac";
        $reponse_path = RESPONSE_PATH . "?order_id=" . $_POST['order_id'];
        $data['order_amount'] = $data['order_amount']*100; 
        $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, ENV);
        $response = $phonePeHelper->createPayment($data['order_id'], $data['order_amount'], $reponse_path);
        echo "<script>location.href='".$response['redirectUrl']."';</script>";
        exit;   
    }catch (Throwable  $e) {
        echo $e;
        exit;
    }
}else{
    echo "<script>location.href='index.php';</script>";
}

