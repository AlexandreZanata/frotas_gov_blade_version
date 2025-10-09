<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking database tables...\n";

// Check if oil_changes table exists
if (Schema::hasTable('oil_changes')) {
    echo "✓ Table 'oil_changes' already exists\n";
} else {
    echo "✗ Table 'oil_changes' does not exist - Creating now...\n";

    DB::statement("
        CREATE TABLE oil_changes (
            id CHAR(36) PRIMARY KEY,
            vehicle_id CHAR(36) NOT NULL,
            user_id CHAR(36) NOT NULL,
            inventory_item_id CHAR(36) NULL,
            km_at_change INT UNSIGNED NOT NULL,
            change_date DATE NOT NULL,
            liters_used DECIMAL(8,2) NULL,
            cost DECIMAL(10,2) NULL,
            next_change_km INT UNSIGNED NOT NULL,
            next_change_date DATE NOT NULL,
            notes TEXT NULL,
            service_provider VARCHAR(255) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id),
            FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✓ Table 'oil_changes' created successfully!\n";
}

// Check if oil_change_settings table exists
if (Schema::hasTable('oil_change_settings')) {
    echo "✓ Table 'oil_change_settings' already exists\n";
} else {
    echo "✗ Table 'oil_change_settings' does not exist - Creating now...\n";

    DB::statement("
        CREATE TABLE oil_change_settings (
            id CHAR(36) PRIMARY KEY,
            vehicle_category_id CHAR(36) NULL,
            km_interval INT UNSIGNED NOT NULL DEFAULT 10000,
            days_interval INT UNSIGNED NOT NULL DEFAULT 180,
            default_liters DECIMAL(8,2) NULL,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL,
            FOREIGN KEY (vehicle_category_id) REFERENCES vehicle_categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✓ Table 'oil_change_settings' created successfully!\n";
}

echo "\nDone! All tables are ready.\n";

