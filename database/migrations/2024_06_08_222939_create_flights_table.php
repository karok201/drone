<?php

use App\Models\Flight;
use App\Models\MapPoint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Flight::TABLE_NAME, function (Blueprint $table) {
            $table->id(Flight::FIELD_ID);
            $table->string(Flight::FIELD_PATH);
            $table->date(Flight::FIELD_TIME_START);
            $table->foreignIdFor(MapPoint::class, Flight::FIELD_MAP_POINT_ID);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Flight::TABLE_NAME);
    }
};
