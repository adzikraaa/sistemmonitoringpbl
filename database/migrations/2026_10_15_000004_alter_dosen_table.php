<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn('nip');
            $table->boolean('is_aktif')->default(true)->after('koordinator_id');
            $table->timestamp('last_login_at')->nullable()->after('is_aktif');
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('email');
            $table->dropColumn(['is_aktif', 'last_login_at']);
        });
    }
};
