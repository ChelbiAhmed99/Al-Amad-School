<?php
// scripts/check_counts.php
require_once __DIR__ . '/../includes/db.php';

$collections = ['users', 'teachers', 'classes', 'students', 'attendance', 'grades', 'payments', 'announcements'];

echo "Final Database Counts:\n";
foreach ($collections as $col) {
    try {
        $count = $database->getReference($col)->getSnapshot()->numChildren();
        echo "- $col: $count\n";
    } catch (Exception $e) {
        echo "- $col: Error counting children\n";
    }
}
