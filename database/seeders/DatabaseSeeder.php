<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Este bloco é tudo o que precisa estar aqui.
        // Ele chama os seeders na ordem correta.
        $this->call([
            RoleSeeder::class,
            SecretariatSeeder::class,
            UserSeeder::class,
            FuelTypeSeeder::class,
            VehicleCategorySeeder::class,
            VehicleStatusSeeder::class,
            PrefixSeeder::class,
            VehicleSeeder::class,
            UserPhotoSeeder::class,
            AuditLogSeeder::class,
            GasStationSeeder::class,
            ChecklistItemSeeder::class,
            RunSeeder::class,
            ChecklistSeeder::class,
            PdfTemplateSeeder::class,
            ChatSeeder::class,
            FuelingSeeder::class,
            DefectCategorySeeder::class,
            DefectReportItemSeeder::class,
            DefectReportSeeder::class,
            DigitalSignatureSeeder::class,
            RunSignatureSeeder::class,
            InventoryCategorySeeder::class,
            InventorySeeder::class,
            FineSeeder::class,
            VehicleTransferSeeder::class,
        ]);
    }
}
