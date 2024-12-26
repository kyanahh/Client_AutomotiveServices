<?php
require("../server/connection.php");

header('Content-Type: application/json'); // Set the content type

if (isset($_POST['detailId'])) {
    $detailId = $_POST['detailId'];

    // First, fetch the transid for the given detailid
    $stmt = $connection->prepare("SELECT transid, amount FROM trans_details WHERE detailid = ?");
    $stmt->bind_param("i", $detailId);
    $stmt->execute();
    $stmt->bind_result($transid, $amount);
    $stmt->fetch();
    $stmt->close();

    if ($transid) {
        // Delete the transaction detail
        $sql = "DELETE FROM trans_details WHERE detailid = ?";
        $stmtDelete = $connection->prepare($sql);
        $stmtDelete->bind_param("i", $detailId);
        
        if ($stmtDelete->execute()) {
            // Now recalculate the total_amount for the transaction
            $stmtTotal = $connection->prepare("SELECT SUM(amount) AS total FROM trans_details WHERE transid = ?");
            $stmtTotal->bind_param("i", $transid);
            $stmtTotal->execute();
            $stmtTotal->bind_result($totalAmount);
            $stmtTotal->fetch();
            $stmtTotal->close();

            // Update the total_amount in trans table
            $stmtUpdateTrans = $connection->prepare("UPDATE trans SET total_amount = ? WHERE transid = ?");
            $stmtUpdateTrans->bind_param("ii", $totalAmount, $transid);

            if ($stmtUpdateTrans->execute()) {
                echo json_encode(['success' => 'Transaction detail deleted and total amount updated successfully']);
            } else {
                echo json_encode(['error' => 'Error updating total amount: ' . $connection->error]);
            }

            $stmtUpdateTrans->close();
        } else {
            echo json_encode(['error' => 'Error deleting transaction detail: ' . $stmtDelete->error]);
        }

        $stmtDelete->close();
    } else {
        echo json_encode(['error' => 'Transaction detail not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();
?>
