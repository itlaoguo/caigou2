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
        if (Schema::hasTable('language')) { return; }

        Schema::create('language', function (Blueprint $table) {
            $table->id()->comment('主键、自增');
            $table->string('name')->unique()->comment('语言名称');
            $table->string('code')->unique()->comment('语言代码');
            $table->integer('sort_order')->nullable()->comment('排序');
            $table->creatorId();
            $table->createdAt();
            $table->updatedAt();
            $table->deletedAt();

            $table->engine='InnoDB';
            $table->comment('语言表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('language');
    }
};
