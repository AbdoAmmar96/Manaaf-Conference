<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * مجالات الاهتمام كانت enum ثابتًا في الكود فلا يمكن إضافة مجال
         * دون نشر إصدار جديد. صارت جدولًا، و slug يطابق قيم الـenum القديمة
         * حرفيًا حتى تبقى بيانات leads.interests المخزّنة صالحة كما هي.
         */
        Schema::create('interests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 60)->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('area')->nullable();          // المساحة كنص حر: "12,000 م²"
            $table->string('units')->nullable();          // عدد الوحدات/المواقع
            $table->string('status', 30)->default('available')->index();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('interest_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interest_id')->constrained('interests')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->unique(['interest_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interest_project');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('interests');
    }
};
