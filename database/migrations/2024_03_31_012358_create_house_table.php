<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('neighborhood'); // اسم الحي
            $table->double('area'); // بخصوص المساحة
            $table->double('width');
            $table->double('height');
            $table->string('estateType'); // نوع العقار
            $table->string('estateStreet'); // شارع العقار
            $table->enum('estateDeed', ['طابو', 'زراعي', 'شرعي'])->nullable(); // سند العقار
            $table->unsignedBigInteger('price'); // سعر العقار
            $table->json('images'); // list of image URLs or image data
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('houses');
    }
};
