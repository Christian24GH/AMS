<?php
    function fetchItems(){
        $root = $_SERVER['DOCUMENT_ROOT'];
        include $root."/ams/client/global/components/conn.php";

        $getItem = "SELECT item_name, item_price FROM items";
        $result = $conn->query($getItem);
        while($row = $result->fetch_assoc()){
            echo "<tr>
                    <td>{$row['item_name']}</td>
                    <td>{$row['item_price']}</td>
                </tr>"; 
        }
    }
?>