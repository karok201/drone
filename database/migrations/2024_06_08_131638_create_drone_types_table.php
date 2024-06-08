<?php

use App\Models\DroneType;
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
        Schema::create(DroneType::TABLE_NAME, function (Blueprint $table) {
            $table->id(DroneType::FIELD_ID);
            $table->string(DroneType::FIELD_NAME);
            $table->string(DroneType::FIELD_IMAGE);
            $table->integer(DroneType::FIELD_MAX_SPEED);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(DroneType::TABLE_NAME);
    }
};
