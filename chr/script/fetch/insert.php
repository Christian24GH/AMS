<?php
    session_start();
    
    class Transaction {
        public $appointment_id;
        public $id;
        public $ln;
        public $fn;
        public $mn;
        public $amount;
        public $items;
        public $section_id;
        public $cashier_id;

        function __construct($appointment_id, $id, $ln, $fn, $mn, $amount, $items, $section_id, $cashier_id) {
            $this->appointment_id = $appointment_id;
            $this->id = $id;
            $this->ln = $ln;
            $this->fn = $fn;
            $this->mn = $mn;
            $this->amount = $amount;
            $this->items = json_decode($items);
            $this->section_id = $section_id;
            $this->cashier_id = $cashier_id;
        }
    }

    function getPost() {
        if (isset($_POST["items"]) && isset($_SESSION['cashier_id'])) {
            $root = $_SERVER['DOCUMENT_ROOT'];
            include "$root/ams/chr/conn.php";
            
            // Retrieve and sanitize POST data
            $transaction = new Transaction(
                $_POST["appointment_id"] ?? null,
                $_POST["studID"] ?? null,
                $_POST["studLN"] ?? '',
                $_POST["studFN"] ?? '',
                $_POST["studM"] ?? '',
                $_POST["amount"] ?? 0,
                $_POST["items"] ?? '[]',
                $_POST["section_id"] ?? 1,
                $_POST["cashier_id"] ?? 1
            );

            insert_data($transaction, $conn);
        }
    }

    function insert_data($transaction, $conn) {
        $response = array();
        header("Content-Type: application/json");
        try {
            // Insert transaction data using a stored procedure
            $stmt = $conn->prepare("CALL transaction_ins(?, ?, ?)");
            $stmt->bind_param(
                "sii",
                $transaction->appointment_id,
                $transaction->id,
                $transaction->cashier_id
            );
            $stmt->execute();

            // Insert each item associated with the transaction
            $itemStmt = $conn->prepare("INSERT INTO paid_items(stud_id, item_id, appointment_id) VALUES (?, ?, ?)");
            foreach ($transaction->items as $item) {
                $itemStmt->bind_param("iis", $transaction->id, $item, $transaction->appointment_id);
                $itemStmt->execute();
            }
            $itemStmt->close();
            $response['result'] = "OK";
            echo json_encode($response);
        } catch (Exception $e) {
            $conn->rollback();
            $response['result'] = "Failed to complete transaction: " . $e->getMessage();
            echo json_encode($response);
        }
    }
    getPost();
?>
