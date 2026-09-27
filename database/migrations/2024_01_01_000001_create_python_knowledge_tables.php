<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nickname', 20)->nullable();
            $table->string('name', 50)->nullable();
            $table->string('avatar', 500)->nullable();
            $table->text('bio')->nullable();
            $table->string('github_id')->nullable()->unique();
            $table->string('github_username')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', ['active', 'disabled', 'deleted'])->default('active');
            $table->timestamps();

            $table->index('email');
            $table->index('github_id');
            $table->index('status');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->primary(['user_id', 'role_id']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('version', 10);
            $table->string('category');
            $table->json('tags')->nullable();
            $table->string('author')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_system')->default(true);
            $table->integer('order')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('published');
            $table->timestamps();

            $table->index('version');
            $table->index('category');
            $table->index('is_system');
            $table->index('status');
        });

        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->enum('share_permission', ['view_only', 'allow_comment', 'allow_favorite'])->default('view_only');
            $table->char('share_id', 32)->nullable()->unique();
            $table->timestamp('share_expires_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->enum('status', ['draft', 'published', 'archived', 'deleted'])->default('draft');
            $table->timestamps();

            $table->index('user_id');
            $table->index('share_id');
            $table->index('is_shared');
            $table->index('status');
        });

        Schema::create('knowledge_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('knowledge_base_categories')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('knowledge_base_id');
            $table->index('knowledge_id');
            $table->index('category_id');
        });

        Schema::create('knowledge_base_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('knowledge_base_categories')->onDelete('set null');
            $table->string('name', 50);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index('knowledge_base_id');
            $table->index('parent_id');
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('knowledge_base_id')->nullable()->constrained('knowledge_bases')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'knowledge_id'], 'user_knowledge_unique');
            $table->unique(['user_id', 'knowledge_base_id'], 'user_knowledge_base_unique');
            $table->index('user_id');
            $table->index('knowledge_id');
            $table->index('knowledge_base_id');
        });

        Schema::create('share_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_base_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->char('share_id', 32)->unique();
            $table->enum('permission', ['view_only', 'allow_comment', 'allow_favorite'])->default('view_only');
            $table->timestamp('expires_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->timestamps();

            $table->index('share_id');
            $table->index('knowledge_base_id');
            $table->index('user_id');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['system', 'announcement', 'warning', 'info'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_read');
            $table->index('type');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });

        Schema::create('search_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->integer('result_count')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('keyword');
            $table->index('created_at');
        });

        Schema::create('system_config', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index('key');
        });

        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('size');
            $table->enum('type', ['full', 'incremental'])->default('full');
            $table->boolean('is_restorable')->default(true);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });

        DB::statement("ALTER TABLE users CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE knowledge CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE knowledge_bases CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        DB::statement("ALTER TABLE knowledge_entries CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    public function down()
    {
        Schema::dropIfExists('backups');
        Schema::dropIfExists('system_config');
        Schema::dropIfExists('search_history');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('share_records');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('knowledge_entries');
        Schema::dropIfExists('knowledge_base_categories');
        Schema::dropIfExists('knowledge_bases');
        Schema::dropIfExists('knowledge');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
