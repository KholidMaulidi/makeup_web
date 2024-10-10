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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mua_id');
            $table->unsignedBigInteger('type_id');
            $table->string('payment_method_name')->nullable();
            $table->string('payment_method_number')->nullable()->unique();
            $table->enum('status', ['active', 'off'])->default('active');
            $table->timestamps();

            $table->foreign('mua_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('payment_method_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
