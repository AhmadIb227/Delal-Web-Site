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
        Schema::create('accepted_houses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('displayType');
            $table->decimal('price')->nullable()->change();
            $table->text('note');
            $table->timestamps();
            $table->string('neighborhood'); // اسم الحي
            $table->double('area'); // بخصوص المساحة
            $table->double('width');
            $table->double('height');
            $table->string('estateType'); // نوع العقار
            $table->string('estateStreet'); // شارع العقار
            $table->enum('estateDeed', ['طابو', 'زراعي', 'شرعي'])->nullable(); // سند العقار
            $table->unsignedBigInteger('price'); // سعر العقار
            $table->json('images'); // list of image URLs or image data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accepted_houses_tabel');
    }
};
