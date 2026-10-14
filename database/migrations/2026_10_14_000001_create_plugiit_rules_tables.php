<?php

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
        Schema::create('plugiit_rules_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('content');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('plugiit_rules_versions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('version', 20);
            $table->text('summary')->nullable();
            $table->boolean('requires_acceptance')->default(true);
            $table->json('sections');
            $table->unsignedInteger('author_id')->nullable();
            $table->timestamp('published_at');
            $table->timestamps();

            $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('plugiit_rules_acceptances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('version_id');
            $table->string('ip_hash', 64)->nullable();
            $table->string('source', 20)->default('modal');
            $table->timestamps();

            $table->unique(['user_id', 'version_id']);
            $table->index('version_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('version_id')->references('id')->on('plugiit_rules_versions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugiit_rules_acceptances');
        Schema::dropIfExists('plugiit_rules_versions');
        Schema::dropIfExists('plugiit_rules_sections');
    }
};
