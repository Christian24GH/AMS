<?php
    // Set the response header to JSON
    header('Content-Type: application/json');
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root . "/ams/client/global/components/conn.php";
    try {
        // Get the input data from the POST body
        $input = json_decode(file_get_contents("php://input"), true);
        $appt_id = $input['appt_id'];

        if (!$appt_id) {
            echo json_encode(['error' => 'No appointment ID provided.']);
            exit();
        }

        $stmt = $conn->prepare("SELECT * FROM queueinformation WHERE appointment_id = ?");
        $stmt->bind_param("i", $appt_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($result->num_rows > 0) {
            // Adjusted to handle space-separated values
            $string_list = explode(" ", $data['items']);
            $placeholder = implode(",", array_fill(0, count($string_list), "?"));

            $sql = "SELECT item_name FROM items WHERE item_id IN ($placeholder)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat("i", count($string_list)), ...$string_list);
            $stmt->execute();

            $fetch_data = $stmt->get_result();
            $item_names = [];
            while ($row = $fetch_data->fetch_assoc()) {
                $item_names[] = $row['item_name'];
            }

            // Combine initial data with item names
            $array = array_merge($data, ['item_names' => $item_names]);
            echo json_encode($array);
        } else {
            echo json_encode(['error' => 'No appointment found with the given ID.']);
        }
        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error retrieving data: ' . $e->getMessage()]);
    }

?>
