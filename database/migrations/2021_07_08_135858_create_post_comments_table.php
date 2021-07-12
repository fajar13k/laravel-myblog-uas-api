<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId("id_post")
                ->constrained('posts')->onDelete('cascade')->onUpdate('cascade');

            $table->foreignId("id_parent_comment")
                ->nullable()
                ->constrained('post_comments')->onDelete('restrict')->onUpdate('cascade');

            $table->boolean("is_pulled")->default(0)
                ->comment("Determines the comment has been flagged as deleted.");

            $table->text("content")
                ->comment("Comment contents.");

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
        Schema::dropIfExists('post_comments');
    }
}
