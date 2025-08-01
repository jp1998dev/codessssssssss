<?php

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
        Schema::create('cashier_collections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cashier_id');
            $table->decimal('system_collection', 8, 2);
            $table->decimal('actual_collection', 8, 2);
            $table->decimal('variance',8,2);
            $table->string('note',255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashier_collection');
    }
};
