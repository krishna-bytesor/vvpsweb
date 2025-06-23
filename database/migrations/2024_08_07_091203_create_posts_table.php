<?php

use App\Models\Category;
use App\Models\PostType;
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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable();
            $table->foreignIdFor(PostType::class)->constrained();
            $table->foreignIdFor(Category::class)->constrained();
            $table->date('date')->nullable();
            $table->string('author')->nullable();

            $table->string('country')->nullable();
            $table->string('city')->nullable();

            $table->string('data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
