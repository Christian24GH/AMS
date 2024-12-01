<?php
    function fetchItems(){
        $root = $_SERVER['DOCUMENT_ROOT'];
        include $root."/ams/client/global/components/conn.php";

        $userID = $_SESSION['stud_id'];
        $getItem = "SELECT
                        i.item_id,
                        i.item_name,
                        i.item_price,
                        pd.stud_id
                    FROM items i
                    LEFT JOIN paid_items pd
                    ON pd.item_id = i.item_id AND pd.stud_id = {$userID}
                    WHERE stud_id IS NULL
                    ";
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