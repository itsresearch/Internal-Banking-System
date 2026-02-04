<?php

try {
    $conn = new PDO('mysql:host=127.0.0.1;dbname=bank', 'root', '');

    $stmt = $conn->prepare('INSERT IGNORE INTO migrations (migration, batch) VALUES (?, ?)');
    $stmt->execute(['2026_02_04_000000_create_transactions_table', 1]);

    echo "Migration record inserted successfully.";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
