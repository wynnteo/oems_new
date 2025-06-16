<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('wallet_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('balance_after', 10, 2)->nullable()->after('amount');
            $table->string('currency', 3)->default('USD')->after('description');
            $table->index(['user_id', 'type']);
            $table->index(['wallet_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
            $table->dropForeign(['exam_id']);
            $table->dropColumn(['wallet_id', 'exam_id', 'type', 'balance_after', 'currency']);
        });
    }
};
