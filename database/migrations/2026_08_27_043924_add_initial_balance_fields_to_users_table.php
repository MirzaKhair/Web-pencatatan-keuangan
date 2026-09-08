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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('initial_balance', 15, 2)->nullable()->after('password');

            $table->date('initial_balance_date')->nullable()
                ->after('initial_balance');

            $table->timestamp('initial_balance_setup_at')->nullable()
                ->after('initial_balance_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'initial_balance',
                'initial_balance_date',
                'initial_balance_setup_at',
            ]);
        });
    }
};
