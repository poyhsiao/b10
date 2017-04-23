<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImageCachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_caches', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('primary key');
            $table->string('image_id')->comment('image id of image');
            $table->unsignedInteger('width')->comment('image width');
            $table->unsignedInteger('height')->comment('image height');
            $table->string('extension')->comment('extension name of the file');
            $table->string('filename')->comment('file name');
            $table->string('url')->comment('the url of the file');

            $table->index('image_id', 'idx_img_id');
            $table->index(['image_id', 'extension'], 'idx_img_filename');
            $table->index('width', 'idx_img_width');
            $table->index('height', 'idx_img_height');

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
        Schema::dropIfExists('image_caches');
    }
}
