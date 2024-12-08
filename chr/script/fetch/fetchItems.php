<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root."/ams/client/global/components/conn.php";
    header("Content-Type: application/json");

    $response = [];
    $getItem = "SELECT item_id, item_name, item_price FROM items";
    $result = $conn->query($getItem);
    while($row = $result->fetch_assoc()){
        $response[] = [
            "item_id" => "{$row['item_id']}",
            "item_name"=>"{$row['item_name']}",
            "item_price"=>"{$row['item_price']}"
        ];
    }
    echo json_encode($response);
?>