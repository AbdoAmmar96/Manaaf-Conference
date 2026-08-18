<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            // طلب الحضور من الموقع لم يعد يصدر بطاقة فورًا — يمر على الموظف أولًا
            $table->string('approval_status', 20)->default('approved')->after('guest_type')->index();
            $table->timestamp('approved_at')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable()->after('approved_by');
            $table->timestamp('card_sent_at')->nullable()->after('rejection_reason');
            $table->string('card_sent_via', 20)->nullable()->after('card_sent_at');
        });

        // كل من سُجّل قبل هذه الميزة يبقى معتمدًا كما كان
        DB::table('guests')->update(['approval_status' => 'approved', 'approved_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'approval_status', 'approved_at', 'approved_by',
                'rejection_reason', 'card_sent_at', 'card_sent_via',
            ]);
        });
    }
};
