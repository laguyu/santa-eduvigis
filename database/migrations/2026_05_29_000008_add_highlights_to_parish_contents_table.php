<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parish_contents', function (Blueprint $table) {
            $table->text('highlights')->nullable()->after('body');
        });

        DB::table('parish_contents')
            ->whereIn('key', ['community', 'comunidad'])
            ->update([
                'highlights' => "Pastoral juvenil\nCatequesis\nMinisterio de musica\nVoluntariado social",
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parish_contents', function (Blueprint $table) {
            $table->dropColumn('highlights');
        });
    }
};
