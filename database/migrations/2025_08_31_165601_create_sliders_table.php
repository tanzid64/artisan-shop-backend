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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('title')->index();
            $table->string('banner');
            $table->decimal('starting_price', 10, 2);
            $table->string('btn_url');
            $table->integer('serial')->default(0);
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['type', 'title', 'starting_price',]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
