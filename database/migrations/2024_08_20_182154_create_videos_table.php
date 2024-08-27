<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            // Use string instead of bigIncrements and set it as the primary key
            $table->string('id', 11)->primary(); 
            $table->string('name');
            $table->unsignedBigInteger('userid');
            $table->unsignedBigInteger('serverid');
            $table->string('video_filepath');
            $table->string('status')->nullable()->default(null);
            $table->string('manifest_url')->nullable()->default(null);
            $table->string('thumbnail_url')->nullable();
            $table->string('subtitle_filepath')->nullable();
            $table->string('subtitle_url')->nullable();
            $table->string('logo_filepath')->nullable();
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
        Schema::dropIfExists('videos');
    }
}
