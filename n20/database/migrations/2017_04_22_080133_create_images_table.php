<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->string('image_id', 32)
                ->comment('image id');

            $table->string('storage', 50)
                ->default('filesystem')
                ->comment('storage engine');

            $table->string('image_name', 50)
                ->nullable()
                ->commen('image name');

            $table->string('ident', 200)
                ->comment('ident');

            $table->string('url', 200)
                ->comment('url');

            $table->string('l_ident', 200)
                ->nullable()
                ->comment('large image id');

            $table->string('l_url', 200)
                ->nullable()
                ->comment('large image url');

            $table->string('m_ident', 200)
                ->nullable()
                ->comment('middle image id');

            $table->string('m_url', 200)
                ->nullable()
                ->comment('middle image url');

            $table->string('s_ident', 200)
                ->nullable()
                ->comment('small image id');

            $table->string('s_url', 200)
                ->nullable()
                ->comment('small image url');

            $table->unsignedMediumInteger('width')
                ->nullable()
                ->comment('width');

            $table->unsignedMediumInteger('height')
                ->nullable()
                ->comment('height');

            $table->enum('watermark', ['true', 'false'])
                ->default('false')
                ->comment('if having water mark');

            $table->unsignedInteger('last_modified')
                ->default(0)
                ->comment('update time');

            $table->timestamps();

            $table->primary('image_id');

            $table->index(['s_url'], 'idx_full_surl');
            $table->index(['m_url'], 'idx_full_murl');
            $table->index(['l_url'], 'idx_full_lurl');
            $table->index(['last_modified'], 'idx_full_last_modified');
            $table->index(['width'], 'idx_full_width');
            $table->index(['height'], 'idx_full_height');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
