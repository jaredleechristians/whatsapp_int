<h1>WhatsApp webhook</h1>
<?php

    $data = json_decode(file_get_contents('php://input'), true);
    $data = $data['messages'][0];

    
    $body = $data["body"];
    $file_url = checkUrl($body);
    $time = $data["time"];
    $contact = cleanId($data["id"]);
    $direction = checkMessageDirection($data["fromMe"]);
    $messageName = date("Y-m-d H:i:s",$time); // Message name for salesforce object
    $location = isLocation($body);
    $lat = "";
    $lon = "";
    $googleMaps = "https://www.google.com/maps/search/?api=1&query=";
    if($location !== ""){
        $lat = $location[0];
        $lon = $location[1];
        $googleMaps = "https://www.google.com/maps/search/?api=1&query=";
        $googleMaps .= $lat . "," . $lon;
    }

    $jsonObj->body = $body;
    $jsonObj->file_url = $file_url;
    $jsonObj->time = $time;
    $jsonObj->contact = $contact;
    $jsonObj->messageName = $messageName;
    $jsonObj->direction = $direction;
    $jsonObj->lat=$lat;
    $jsonObj->lon=$lon;
    $jsonObj->googleMaps=$googleMaps;

    $jsonDataEncoded = json_encode($jsonObj);

    // zapier webhook url
    $url = 'https://hooks.zapier.com/hooks/catch/2294096/o3t12eo/';

    //Initiate cURL.
    $ch = curl_init($url);
    //Tell cURL that we want to send a POST request.
    curl_setopt($ch, CURLOPT_POST, 1);
    //Attach our encoded JSON string to the POST fields.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
    //Set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
    //Execute the request
    $result = curl_exec($ch);
    
    // Change conversaiton phone number to south african format (remove 27 and add 0)
    function cleanId($id){
        $arr = explode("@", $id);
        $arr =  $arr[0];
        $arr = explode("_", $arr);
        $arr =  substr($arr[1],2);
        return "0".$arr;
    }

    // Salesforce message object uses a direction. Notificaions are triggered on the creation of a message "In"
    function checkMessageDirection($contact){
        if($contact){
            return "Out";
        }
        else{
            return "In";
        }
    }

    //checks if message body is a url
    function checkUrl($message){
        if(strpos($message,'https') !== false){
            return $message;
        }
        else{
            return "";
        }
    }

    //checks if message body is a location
    function isLocation($message){
        if(strpos($message, ';') !== false){
            $arr = explode(";", $message);
            return array($arr[0],$arr[1]);
        }
        else{
            return "";
        }
        
    }


    //write parsed JSON-body to the file for debugging

    /*
    file_put_contents('message_body.txt',$jsonDataEncoded.PHP_EOL,FILE_APPEND);

    ob_start();
    var_dump($data);
    $input = ob_get_contents();
    ob_end_clean();
    file_put_contents('request.txt',$input.PHP_EOL,FILE_APPEND);
    */
?>