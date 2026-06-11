<?php
include('d:/261/dbms project/src/assets/config/db.php');

$conn->query("ALTER TABLE employer_profiles ADD COLUMN company_type VARCHAR(100) DEFAULT NULL AFTER company_name");
$conn->query("ALTER TABLE employer_profiles ADD COLUMN trade_license VARCHAR(100) DEFAULT NULL AFTER company_type");

echo "Schema updated.";
?>
