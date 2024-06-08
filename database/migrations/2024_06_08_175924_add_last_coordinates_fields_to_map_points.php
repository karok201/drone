<?php

use App\Models\MapPoint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(MapPoint::TABLE_NAME, function (Blueprint $table) {
            $table->string(MapPoint::FIELD_LAST_LATITUDE)->nullable();
            $table->string(MapPoint::FIELD_LAST_LONGITUDE)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table(MapPoint::TABLE_NAME, function (Blueprint $table) {
            $table->dropColumn(MapPoint::FIELD_LAST_LATITUDE);
            $table->dropColumn(MapPoint::FIELD_LAST_LONGITUDE);
        });
    }
};
