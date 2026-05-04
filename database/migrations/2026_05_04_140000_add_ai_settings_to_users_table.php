<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Workaround untuk data legacy tanggal '0000-00-00 00:00:00' saat ALTER TABLE.
        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(@@sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ai_token_balance')) {
                $table->bigInteger('ai_token_balance')->default(100000)->after('password');
            }
            if (!Schema::hasColumn('users', 'ai_allowed_models')) {
                $table->text('ai_allowed_models')->nullable()->after('ai_token_balance');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("SET SESSION sql_mode = REPLACE(REPLACE(@@sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', '')");

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ai_allowed_models')) {
                $table->dropColumn('ai_allowed_models');
            }
            if (Schema::hasColumn('users', 'ai_token_balance')) {
                $table->dropColumn('ai_token_balance');
            }
        });
    }
};
