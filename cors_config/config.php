<?php

$allowed_origins = [
    "http://localhost:5173",
    "https://recipe-book-nine-swart.vercel.app"
];

// Get the origin of the incoming request
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowed_origins)){
    header("Access-Control-Allow-Origin: $origin");
}
else{
    header('HTTP/1.1 403 Forbidden');
    echo "Access denied!";
    exit;
}

?>