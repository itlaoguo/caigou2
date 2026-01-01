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
        if (Schema::hasTable('country')) { return; }

        Schema::create('country', function (Blueprint $table) {
            $table->id()->comment('主键、自增');
            $table->string('code')->unique()->comment('国家代码');
            $table->string('address_format')->nullable()->comment('国家地址格式');
            $table->integer('sort_order')->nullable()->comment('排序');
            $table->creatorId();
            $table->createdAt();
            $table->updatedAt();
            $table->deletedAt();

            $table->engine='InnoDB';
            $table->comment('国家表');
        });

        if (Schema::hasTable('country_description')) { return; }

        Schema::create('country_description', function (Blueprint $table) {
            $table->integer('country_id')->comment('国家id');
            $table->integer('language_id')->comment('语言id');
            $table->string('name')->comment('国家名称');
            $table->primary(['country_id', 'language_id']);

            $table->engine='InnoDB';
            $table->comment('国家描述');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country');
        Schema::dropIfExists('country_description');
    }
};
