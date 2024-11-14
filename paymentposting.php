<?php

require_once('./setup/functions.php');
header('Content-Type: application/json; charset=UTF-8');

$date = new DateTime("now", new DateTimeZone("UTC"));
$date->modify("+8 hours");

$array = array();

if($_SERVER["REQUEST_METHOD"] == "POST")
{
    $mysql = new phpmysql();

    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $idno = $data['idno'];
    $firstname = $data['firstname'];
    $middlename = $data['middlename'];
    $lastname = $data['lastname'];
    $amount = $data['amount'];

    if($data && isset($data['idno']) && isset($data['firstname']) && isset($data['middlename']) && isset($data['lastname']) && isset($data['amount']))
    {
        if($mysql->init(DBCON_ips, 'bdotestdb'))
        {
            if($mysql->clear())
            {
                $insertQuery = "INSERT INTO tb_mentrytest(idno,firstname,middlename,lastname,amount,created) VALUES(?,?,?,?,?,?)";
                $mysql->addparam("ssssss");
                $mysql->addparam($idno);
                $mysql->addparam($firstname);
                $mysql->addparam($middlename);
                $mysql->addparam($lastname);
                $mysql->addparam($amount);
                $mysql->addparam($date->format("Y-m-d H:i:s"));
                $mysql->addcmd($insertQuery);
                
                echo json_encode(
                    array(
                        "status" => 200,
                        "success" => true,
                        "message"=>"POST REQUEST SUCCESS", 
                        "timestamp"=>$date->format("Y-m-d H:i:s"),
                        "data"=>array(
                            "idno"=>$idno,
                            "firstname"=>$firstname,
                            "middlename"=>$middlename,
                            "lastname"=>$lastname,
                            "amount"=>$amount,
                            "created"=>$date->format("Y-m-d H:i:s")
                        )
                    )
                );
            }
            $requestCreated = $mysql->execcmd();
        }
    }
    else
    {
        echo json_encode(
            array(
                "Status" => 200,
                "Success" => false,
                "Message"=>"ENTRY FAILED", 
                "timestamp"=>$date->format("Y-m-d H:i:s"),
                "data"=>array(
                    "idno"=>$idno,
                    "firstname"=>$firstname,
                    "middlename"=>$middlename,
                    "lastname"=>$lastname,
                    "amount"=>$amount
                )
            )
        );
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

?>