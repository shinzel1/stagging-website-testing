<?php
error_reporting(0);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/PhonePeHelper.php';
try 
{
    if(empty($_GET['order_id'])){
        echo "No Transaction Id Found in URL";
        exit;
    }
    $phonePeHelper = new PhonePeHelper(CLIENT_ID, CLIENT_SECRET, CLIENT_VERSION, 'UAT');
    $response = $phonePeHelper->checkPaymentStatus($_GET['order_id']);
    $payment = $response['paymentDetails'];
    if($response['state']!=='COMPLETED'){
        //if Same Order Id ==COMPLETED,  AND  if call again payment link , it will mark as pending
        $reponse_path = RESPONSE_PATH . "?order_id=" . $_GET['order_id'];
        $response1 = $phonePeHelper->createPayment($_GET['order_id'], $response['amount'], $reponse_path);
        $again_url = $response1['redirectUrl'] ?? '';
    }
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
                        <?php if($response['state']!=='COMPLETED'): ?>
                            <span class='my-2 text-light bg-danger d-flex justify-content-center'> Payment Incomplete, <a href="<?= $again_url ?>" class='text-warning fw-bold mx-2'> click here </a>  to the proceed again payment.. </span>
                        <?php endif; ?>
                        <div class='row'>
                            <div class="mb-3 col-md-6">
                                <label for="order_id" class="form-label">Order Status <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= $response['state'] ?>" readonly>
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="order_id" class="form-label">Order ID <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= $_GET['order_id'] ?>" readonly>
                            </div>
                            
                            <div class="mb-3 col-md-6">
                                <label for="order_id" class="form-label">Order_PhonePe Id <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= $response['orderId'] ?? '' ?>" readonly>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label for="order_amount" class="form-label">Order Amount <span class='text-danger'>*</span></label>
                                <input type="text" class="form-control" value="<?= ($response['amount']/100 ?? 0) . " INR"?>" readonly>
                            </div>
                            
                        </div>
                        
                    </div>
                </div>
                <div class="text-center">
                    <a href="<?= APP_URL . "/" ?>" class='btn btn-success mt-4'>Back to Home</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>


