<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('koordinator', function (Blueprint $table) {
            $table->dropColumn('nip');
            $table->foreignId('dosen_id')->nullable()->unique()->after('nidn')
                ->constrained('dosen')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('koordinator', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->dropForeign(['dosen_id']);
            $table->dropColumn('dosen_id');
        });
    }
};
