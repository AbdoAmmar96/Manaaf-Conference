<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->string('position')->nullable();
            $table->string('mobile', 30)->index();
            $table->string('email')->nullable();
            $table->string('guest_type', 20)->default('guest')->index();
            $table->string('invite_token', 40)->unique();
            $table->string('qr_token', 40)->unique();
            $table->string('rsvp_status', 20)->default('pending')->index();
            $table->string('attendance_status', 20)->default('awaited')->index();
            $table->timestamp('rsvp_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('registered_via', 20)->default('staff');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
