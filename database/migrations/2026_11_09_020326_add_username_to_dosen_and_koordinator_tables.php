<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom username ke tabel dosen (setelah nidn)
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('nidn');
        });

        // Isi username dengan nilai nidn yang sudah ada
        DB::table('dosen')->whereNotNull('nidn')->update([
            'username' => DB::raw('nidn'),
        ]);

        // Tambah kolom username ke tabel koordinator (setelah nidn)
        Schema::table('koordinator', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('nidn');
        });

        // Isi username dengan nilai nidn yang sudah ada
        DB::table('koordinator')->whereNotNull('nidn')->update([
            'username' => DB::raw('nidn'),
        ]);
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn('username');
        });

        Schema::table('koordinator', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
