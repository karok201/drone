<?php

use App\Models\DroneType;
use App\Models\MapPoint;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(MapPoint::TABLE_NAME, function (Blueprint $table) {
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(DroneType::class);
        });
    }

    public function down(): void
    {
        Schema::table(MapPoint::TABLE_NAME, function (Blueprint $table) {
            $table->dropForeignIdFor(User::class);
            $table->dropForeignIdFor(DroneType::class);
        });
    }
};
