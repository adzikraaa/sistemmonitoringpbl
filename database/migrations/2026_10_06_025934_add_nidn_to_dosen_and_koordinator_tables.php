<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('dosen', function (Blueprint $table) {
        $table->string('nidn')->unique()->after('nip');
    });

    Schema::table('koordinator', function (Blueprint $table) {
        $table->string('nidn')->unique()->after('nip');
    });
}

public function down()
{
    Schema::table('dosen', function (Blueprint $table) {
        $table->dropColumn('nidn');
    });

    Schema::table('koordinator', function (Blueprint $table) {
        $table->dropColumn('nidn');
    });
}
};
