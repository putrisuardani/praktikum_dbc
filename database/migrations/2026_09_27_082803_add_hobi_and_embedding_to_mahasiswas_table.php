<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('mahasiswas', 'hobi')) {
        Schema::table('mahasiswas', function (Blueprint $table) {
                $table->text('hobi')->nullable();
            });
        }

        if (!Schema::hasColumn('mahasiswas', 'embedding')) {
            DB::statement(
                'ALTER TABLE mahasiswas
                ADD COLUMN embedding extensions.vector(384)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('mahasiswas', 'embedding')) {
            DB::statement(
                'ALTER TABLE mahasiswas
                DROP COLUMN embedding'
            );
        }

        if (Schema::hasColumn('mahasiswas', 'hobi')) {
            Schema::table('mahasiswas', function (Blueprint $table) {
                $table->dropColumn('hobi');
            });
        }
    }
};
