<?php
    function fetchItems(){
        $root = $_SERVER['DOCUMENT_ROOT'];
        include $root."/ams/client/global/components/conn.php";

        $getItem = "SELECT item_id, item_name, item_price FROM items";
        $result = $conn->query($getItem);
        while($row = $result->fetch_assoc()){
            echo "  
                    <div class='input-group w-100 mb-2' style=''>
                        <div class='input-group-text'>
                            <input class='checkboxItem form-check-input mt-0 item_checkbox' type='checkbox' value='{$row['item_id']}'/>
                        </div>
                        <label type='text' class='form-control d-flex flex-wrap' style='font-size: 0.9em;'>{$row['item_name']}</label>
                    </div>
            "; 
        }

    }
?>