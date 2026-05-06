<?php
require_once 'database.php';
$result = mysqli_query($conn, "SHOW TABLES LIKE 'messages'");
if (!$result) {
    echo 'ERROR: ' . mysqli_error($conn);
    exit(1);
}
if (mysqli_num_rows($result) > 0) {
    echo 'EXISTS';
} else {
    echo 'MISSING';
}
?>