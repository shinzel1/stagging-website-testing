<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

const APP_URL = "http://localhost/nutrizone/";

// After payment success or failure redirect to this URL
const RESPONSE_PATH = APP_URL."verify.php";

// Past heere PRODUCTION Credentials...
// const ENV = "PRODUCTION";
// const CLIENT_ID = "";
// const CLIENT_SECRET = "";
// const CLIENT_VERSION = "1";

//TESTING Credentials... - OK
const ENV = "UAT";
const CLIENT_ID = "TEST-M22F61J4R0CYE_25052";
const CLIENT_SECRET = "ZTJmZjFlZGUtZTJjYS00NTZmLWI3MWEtMWE0MTk1ZGUyYzEz";
const CLIENT_VERSION = "1";


