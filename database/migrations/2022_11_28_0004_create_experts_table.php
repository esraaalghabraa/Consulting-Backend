<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('experts', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('phone_number',30)->unique();
            $table->string('password',100);
            $table->string('address',100)->nullable();
            $table->string('photo',100)->nullable();
            $table->double('rating')->default(0.0);
            $table->integer('rating_number')->default(0);
            $table->string('start_work',10)->nullable();
            $table->string('end_work',10)->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('money')->default(50000);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('experts');
    }
};
