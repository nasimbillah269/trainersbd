<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150);
            $t->string('slug', 180)->unique();
            $t->string('icon', 80)->nullable();          // Bootstrap Icons class, e.g. bi-code-slash
            $t->text('description')->nullable();
            $t->integer('sort')->default(0);
            $t->boolean('status')->default(true);
            $t->timestamps();
        });

        Schema::create('instructors', function (Blueprint $t) {
            $t->id();
            $t->string('name', 150);
            $t->string('title', 150)->nullable();
            $t->text('bio')->nullable();
            $t->string('photo')->nullable();
            $t->boolean('status')->default(true);
            $t->timestamps();
        });

        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained('course_categories')->nullOnDelete();
            $t->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $t->string('title', 220);
            $t->string('slug', 250)->unique();
            $t->text('subtitle')->nullable();
            $t->longText('description')->nullable();
            $t->string('thumbnail')->nullable();
            $t->string('thumbnail_alt', 200)->nullable();
            $t->enum('type', ['paid', 'free'])->default('paid');
            $t->decimal('price', 10, 2)->default(0);
            $t->decimal('old_price', 10, 2)->default(0);
            $t->string('level', 80)->nullable();
            $t->string('language', 50)->default('Bangla');
            $t->string('duration', 50)->nullable();
            $t->string('badge', 40)->nullable();
            $t->unsignedInteger('students_count')->default(0);
            $t->string('preview_video', 200)->nullable();
            $t->json('learn_points')->nullable();
            $t->json('requirements')->nullable();
            $t->boolean('featured')->default(false);
            $t->enum('status', ['active', 'draft'])->default('active');
            $t->string('meta_title', 200)->nullable();
            $t->string('meta_description', 300)->nullable();
            $t->string('meta_keywords', 300)->nullable();
            $t->integer('sort')->default(0);
            $t->timestamps();
            $t->index(['status', 'type']);
        });

        Schema::create('course_modules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $t->string('title', 200);
            $t->integer('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('course_lessons', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $t->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $t->string('title', 220);
            $t->string('video', 200)->nullable();        // YouTube URL or ID
            $t->string('duration', 20)->nullable();
            $t->boolean('is_preview')->default(false);
            $t->integer('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('course_reviews', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $t->string('name', 150);
            $t->string('email', 150)->nullable();
            $t->unsignedTinyInteger('rating');
            $t->text('review');
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->timestamps();
        });

        Schema::create('course_orders', function (Blueprint $t) {
            $t->id();
            $t->string('invoice', 30)->unique();
            $t->string('name', 150);
            $t->string('mobile', 30);
            $t->string('email', 150);
            $t->string('payment_method', 30);
            $t->string('sender_number', 60);
            $t->string('transaction_id', 80);
            $t->decimal('subtotal', 10, 2)->default(0);
            $t->decimal('discount', 10, 2)->default(0);
            $t->decimal('total', 10, 2)->default(0);
            $t->enum('status', ['pending', 'confirmed', 'hold', 'cancelled'])->default('pending');
            $t->enum('payment_status', ['unverified', 'verified', 'invalid'])->default('unverified');
            $t->text('note')->nullable();
            $t->text('admin_note')->nullable();
            $t->unsignedBigInteger('verified_by')->nullable();
            $t->timestamp('verified_at')->nullable();
            $t->string('ip', 45)->nullable();
            $t->timestamps();
            $t->index(['status', 'payment_status']);
        });

        Schema::create('course_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('course_orders')->cascadeOnDelete();
            $t->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $t->string('title', 220);
            $t->decimal('price', 10, 2)->default(0);
            $t->decimal('old_price', 10, 2)->default(0);
            $t->timestamps();
        });

        Schema::table('generals', function (Blueprint $t) {
            $t->json('payment_methods')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('generals', function (Blueprint $t) {
            $t->dropColumn('payment_methods');
        });
        Schema::dropIfExists('course_order_items');
        Schema::dropIfExists('course_orders');
        Schema::dropIfExists('course_reviews');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('course_categories');
    }
};
