<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\balance\BalanceGasStationSupplierSeeder;
use Database\Seeders\checklist\ChecklistItemSeeder;
use Database\Seeders\checklist\ChecklistSeeder;
use Database\Seeders\defect\DefectCategorySeeder;
use Database\Seeders\defect\DefectReportItemSeeder;
use Database\Seeders\defect\DefectReportSeeder;
use Database\Seeders\fuel\FuelingSeeder;
use Database\Seeders\fuel\FuelPriceSeeder;
use Database\Seeders\fuel\FuelTypeSeeder;
use Database\Seeders\fuel\GasStationCurrentSeeder;
use Database\Seeders\fuel\GasStationSeeder;
use Database\Seeders\fuel\ScheduledGasStationSeeder;
use Database\Seeders\fuel\ScheduledPriceMovementSeeder;
use Database\Seeders\fuel\ScheduledPriceSeeder;
use Database\Seeders\fuel\ServiceOrderSeeder;
use Database\Seeders\garbage\GarbageMaintenanceTareVehicleSeeder;
use Database\Seeders\garbage\GarbageNeighborhoodsSeeder;
use Database\Seeders\garbage\GarbageRunDestinationsSeeder;
use Database\Seeders\garbage\GarbageRunsSeeder;
use Database\Seeders\garbage\GarbageTypesSeeder;
use Database\Seeders\garbage\GarbageUsersSeeder;
use Database\Seeders\garbage\GarbageVehiclesSeeder;
use Database\Seeders\garbage\GarbageWeighbridgeOperatorSeeder;
use Database\Seeders\garbage\GarbageWeighingSeeder;
use Database\Seeders\garbage\GarbageWeighingSignatureSeeder;
use Database\Seeders\maintence\InventoryCategorySeeder;
use Database\Seeders\maintence\InventorySeeder;
use Database\Seeders\maintence\OilChangeSettingSeeder;
use Database\Seeders\run\RunSeeder;
use Database\Seeders\run\RunSignatureSeeder;
use Database\Seeders\user\DigitalSignatureSeeder;
use Database\Seeders\user\GeneralManagerSeeder;
use Database\Seeders\user\ManagerStatusSeeder;
use Database\Seeders\user\RoleSeeder;
use Database\Seeders\user\SecretariatSectorManagerSeeder;
use Database\Seeders\user\SecretariatSeeder;
use Database\Seeders\user\UserPhotoSeeder;
use Database\Seeders\user\UserSeeder;
use Database\Seeders\vehicle\PrefixSeeder;
use Database\Seeders\vehicle\VehicleBrandSeeder;
use Database\Seeders\vehicle\VehicleCategorySeeder;
use Database\Seeders\vehicle\VehicleHeritageSeeder;
use Database\Seeders\vehicle\VehiclePriceCurrentSeeder;
use Database\Seeders\vehicle\VehiclePriceHistorySeeder;
use Database\Seeders\vehicle\VehiclePriceOriginSeeder;
use Database\Seeders\vehicle\VehicleSeeder;
use Database\Seeders\vehicle\VehicleStatusSeeder;
use Database\Seeders\vehicle\VehicleTireLayoutSeeder;
use Database\Seeders\vehicle\VehicleTransferSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SecretariatSeeder::class,
            UserSeeder::class,
            GarbageManagerSeeder::class,
            GarbageUsersSeeder::class,
            FuelTypeSeeder::class,
            VehicleCategorySeeder::class,
            VehicleStatusSeeder::class,
            PrefixSeeder::class,
            VehicleHeritageSeeder::class,
            VehicleBrandSeeder::class,
            VehicleSeeder::class,
            AcquisitionTypeSeeder::class,
            VehiclePriceOriginSeeder::class,
            VehiclePriceCurrentSeeder::class,
            VehiclePriceHistorySeeder::class,
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
            ServiceOrderSeeder::class,
            VehicleTireLayoutSeeder::class,
            TireInventorySeeder::class,
            TireSeeder::class,
            OilChangeSettingSeeder::class,
            ScheduledPriceSeeder::class,
            FuelPriceSeeder::class,
            ScheduledPriceMovementSeeder::class,
            GasStationCurrentSeeder::class,
            ScheduledGasStationSeeder::class,
            BalanceGasStationSupplierSeeder::class,
            ManagerStatusSeeder::class,
            SecretariatSectorManagerSeeder::class,
            GeneralManagerSeeder::class,
            GarbageVehiclesSeeder::class,
            GarbageUsersSeeder::class,
            GarbageNeighborhoodsSeeder::class,
            GarbageTypesSeeder::class,
            GarbageWeighingSeeder::class,
            GarbageRunsSeeder::class,
            GarbageUsersSeeder::class,
            GarbageWeighbridgeOperatorSeeder::class,
            GarbageMaintenanceTareVehicleSeeder::class,
            GarbageWeighingSeeder::class,
            GarbageWeighingSignatureSeeder::class,
            GarbageRunsSeeder::class,
            GarbageRunDestinationsSeeder::class,
            FuelingVehicleExpenseSeeder::class,
            DigitalSignatureSeeder::class,
            FuelingSeeder::class,
            FuelingSignatureSeeder::class,
        ]);
    }
}
