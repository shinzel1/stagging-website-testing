<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/vendor/autoload.php';
error_reporting(0);
use PhonePe\Env;
use PhonePe\payments\v1\PhonePePaymentClient;
try 
{
    if(empty($_GET['order_id'])){
        echo "No Transaction Id Found in URL";
        exit;
    }
    $phonePePaymentsClient = new PhonePePaymentClient(API_MERCHAT_ID, API_KEY, API_KEY_INDEX, ENV,true);
    $order_id = $_GET['order_id'];
    $checkStatus = $phonePePaymentsClient->statusCheck($order_id);
    // echo "<pre>";
    // print_r($checkStatus);
    // echo "</pre>";
    // exit;
    
    // echo "Response Code : ". $checkStatus->getResponseCode()  . "<br>";
    // echo "Status : ". $checkStatus->getState() . "<br>";
    // echo "PhonePe Transaction Id : ". $checkStatus->getTransactionId() . "<br>";
    // echo "Merchant Id : ". $checkStatus->getMerchantTransactionId() . "<br>";
    // echo "Amount : ". $checkStatus->getAmount() . "<br>";
    
}catch(Exception $e){
    echo "Error : ". $e->getMessage();
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Pe Payment Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
</head>
<style>
body {
    background-color:black;
    color:red;
}
</style>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class='col-12 my-2 d-flex justify-content-center mb-4'>
                <div class="spinner-border text-primary mx-2" role="status" style="width:1.8rem;height:1.8rem">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <h4 class='text-center text-primary'> Phone Pe Payment Process Completed </h4>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4 text-success"><u>Order Payment Details</u></h3>
                        <?php if($checkStatus->getState()!=='COMPLETED'): ?>
                            <span class='my-2 text-light bg-danger d-flex justify-content-center'> Payment Incomplete, <a href="<?= APP_URL."/" ?>" class='text-warning fw-bold mx-2'> click here </a>  to the proceed again payment.. </span>
                        <?php endif; ?>
                        <div class='row'>
                            <div class="mb-3 col-md-6">
                                <label for="order_id" class="form-label">Order Status <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= $checkStatus->getState() ?>" readonly>
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="order_id" class="form-label">Order ID <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= $order_id ?>" readonly>
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="order_id" class="form-label">Order_PhonePe Id <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= $checkStatus->getTransactionId() ?>" readonly>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="order_amount" class="form-label">Order Amount <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= ($checkStatus->getAmount()/100) . " INR"?>" readonly>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>


