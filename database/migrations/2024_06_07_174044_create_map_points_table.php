<?php

use App\Models\MapPoint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(MapPoint::TABLE_NAME, function (Blueprint $table) {
            $table->id(MapPoint::FIELD_ID);
            $table->json(MapPoint::FIELD_PATH);
            $table->string(MapPoint::FIELD_LONGITUDE);
            $table->string(MapPoint::FIELD_LATITUDE);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(MapPoint::TABLE_NAME);
    }
};
