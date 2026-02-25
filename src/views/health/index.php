<?php

use App\Config;
use Dotenv\Dotenv;
use Doctrine\DBAL\DriverManager;

require_once __DIR__ . "/../../vendor/autoload.php";

$dotenv = Dotenv::createImmutable(dirname(__DIR__, levels: 3));
$dotenv->load();

header("Content-Type: application/json");

$status = "ok";
$httpCode = 200;
$checks = [];

// Database check
$params = new Config(env: $_ENV);
try {
    $conn = DriverManager::getConnection(params: $params->db ?? []);
    $conn->executeQuery("SELECT 1");
    $checks["database"] = ["status" => "ok"];
} catch (Exception $e) {
    $checks["database"] = [
        "status" => "error",
        // gate message on app environment ("production" suppresses it)
        "message" => ($_ENV["APP_ENV"] ?? "production") !== "production"
            ? $e->getMessage() : "database unavailable"
    ];
    $status = "degraded";
    $httpCode = 503;
} finally {
    // nullsafe op handles undefined conn, if getConnection() throws
    $conn?->close();
}

// Disk space check
$freeBytes = disk_free_space('/');
$totalBytes = disk_total_space('/');
$freePercent = round(($freeBytes / $totalBytes) * 100, 1);

if ($freePercent < 10) {
    $checks["disk"] = [
        "status" => "warning",
        "free_percent" => $freePercent
    ];
    $status = "degraded";
} else {
    $checks["disk"] = [
        "status" => "ok",
        "free_percent" => $freePercent
    ];
}

http_response_code($httpCode);

echo json_encode(
    [
        "status"    => $status,
        "timestamp" => date("c"),
        "version" => $_ENV["APP_VERSION"] ?? "0.1.0",
        "checks"    => $checks,
    ],
    JSON_PRETTY_PRINT
);
