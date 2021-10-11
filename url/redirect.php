<?php
require_once('mysql.php');
$key = json_encode(htmlspecialchars($_GET['key']));

if(empty($_GET['key'])){}
else{
    $select = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM `short` WHERE `short_key` = '$key'"));

    if($select){
        $result = json_decode($select['url']);
    
        
        #$result = json_decode($result, true);
        header('location: '.$result);
    }
}

?>