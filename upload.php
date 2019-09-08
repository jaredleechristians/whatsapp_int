<?php
//upload.php
if($_FILES["file_input"]["name"] != '')
{
 $server_url = "https://webota.io/whatsapp/"; //repalce server url with root
 $test = explode('.', $_FILES["file_input"]["name"]);
 $ext = end($test);
 $name = $_FILES["file_input"]["name"];
 $location = 'upload/' . $name;  
 move_uploaded_file($_FILES["file_input"]["tmp_name"], $location);
 echo $server_url.$location; 
}
?>
