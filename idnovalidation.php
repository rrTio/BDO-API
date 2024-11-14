<?php

require_once('./setup/functions.php');
header('Content-Type: application/json; charset=UTF-8');

$date = new DateTime("now", new DateTimeZone("UTC"));
$date->modify("+8 hours");

$array = array();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $mysql = new phpmysql();
    
    if($mysql->init(DBCON_ips,'payments'))
    {
        $rawData = file_get_contents("php://input");
        $data = json_decode($rawData, true);

        if($data && isset($data['idno']))
        {
            $idno = $data['idno'];
            if($mysql->clear())
            {
                $selectQuery = "SELECT COALESCE(idno, adconno) idno, lastname, firstname, middlename, payeestatus, qualified, schyr, sem, srp, bncchk, balance, unclearcheck, visa,
                            CASE
                                WHEN (
                                    payeetype ='Student' AND
                                    COALESCE(idno,adconno) IS NOT NULL AND 
                                    (payeestatus IN ('ENTITY-Application - Submitted','ENTITY-Active Student', 'ENTITY-Application - Admitted') OR balance > 0) AND 
                                    srp = 'Pass' AND 
                                    bncchk = 'N' AND 
                                    LENGTH(COALESCE(idno,adconno)) = 10 AND 
                                    lastname NOT LIKE '%INACTIVE%' AND 
                                    lastname NOT LIKE '%REMOVED%' AND 
                                    lastname NOT LIKE '%TEST%' AND 
                                    firstname NOT LIKE '%TEST%' AND 
                                    COALESCE(program,'') NOT LIKE 'CVT%' AND 
                                    CASE WHEN idno IS NULL THEN LEFT(adconno,3) >= CONCAT('8',DATE_FORMAT(TIMESTAMPADD(YEAR, -1, CURRENT_DATE),'%y')) ELSE 1=1 END AND 
                                    IFNULL(subsidiary,'-') = 'FEU'
                                ) THEN 'YES'
                                ELSE 'NO'
                            END AS valid
                            FROM payments.tb_mentities 
                            WHERE idno = ?;";

                $mysql->addparam("s");
                $mysql->addparam($idno);
                $query = $mysql->bldrs($selectQuery);

                if($query!==false)
                {
                    foreach($query as $rs)
                    { displayJSON(200, true, "REQUEST SUCCESS", $date->format("Y-m-d H:i:s"), test_input($rs['idno']), test_input($rs['firstname']), test_input($rs['middlename']), test_input($rs['lastname']), test_input($rs['valid'])); }
                }

                else
                { displayJSON(200, false, "ID NUMBER DOES NOT EXIST", $date->format("Y-m-d H:i:s"), $idno, "null", "null", "null", "null"); }
            }
        }
    }
}
else
{
    echo json_encode
    (
        array
        (
            "Message"=>"POST METHOD REQUIRED", 
            "timestamp"=>$date->format("Y-m-d H:i:s")
        )
    );
}

function displayJSON($status, $success, $message, $timestamp, $idNumber, $fname, $mname, $lname, $validity)
{
    $jsonArray = array(
        "status" => $status,
        "success" => $success,
        "message" => $message,
        "timestamp" => $timestamp,
        "data" => array
        (
            "idno" => $idNumber,
            "firstname" => $fname,
            "middlename" => $mname,
            "lastname" => $lname,
            "valid" => $validity
        )
    );

    echo json_encode($jsonArray);
}

?>