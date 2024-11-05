<?php
    function fetchItems(){
        $root = $_SERVER['DOCUMENT_ROOT'];
        include "$root/ams/client/conn.php";

        $getItem = "SELECT item_id, item_name FROM items";
        $result = $conn->query($getItem);
        while($row = $result->fetch_assoc()){
            echo "<option value='{$row['item_id']}'>{$row['item_name']}</option>";
        }

    }
?>