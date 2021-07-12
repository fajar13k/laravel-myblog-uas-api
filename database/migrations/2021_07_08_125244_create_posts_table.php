<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->string("post_uid");

            $table->string("title");
            $table->string("tags")->nullable();
            $table->text("content");

            $table->text("warning_description")->comment("Warning to be placed before post eg. spoiler warning.")->nullable();

            $table->boolean("is_archived")->default("0");

            $table->foreignId("created_by")
                ->constrained('users')->onDelete('cascade')->onUpdate('cascade');            
                
            $table->foreignId("updated_by")
                ->nullable()
                ->constrained('users')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('posts');
    }
}
