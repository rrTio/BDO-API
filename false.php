<?php
header('Content-Type: application/json; charset=UTF-8');
$date = new DateTime("now", new DateTimeZone("UTC"));
$date->modify("+8 hours");

if($_SERVER["REQUEST_METHOD"] == "GET")
{
    echo json_encode
    (
        array
        (
            "status"=>200,
            "success"=>false,
            "message"=>"INVALID TOKEN",
            "timestamp"=>$date->format("Y-m-d H:i:s")
        )
    );
}
?>