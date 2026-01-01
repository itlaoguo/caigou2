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
        if (Schema::hasTable('platform')) { return; }

        Schema::create('platform', function (Blueprint $table) {
            $table->id()->comment('主键、自增');
            $table->string('name')->unique()->comment('平台名称、渠道名称');
            $table->string('url')->nullable()->comment('平台链接');
            $table->text('note')->nullable()->comment('备注');
            $table->creatorId();
            $table->createdAt();
            $table->updatedAt();
            $table->deletedAt();

            $table->engine='InnoDB';
            $table->comment('销售渠道、销售平台');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platform');
    }
};
