<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->string('slug', 120)->nullable()->after('nama_matkul');
        });

        $usedSlugs = [];

        foreach (DB::table('mata_kuliah')->orderBy('id')->get() as $mataKuliah) {
            $base = Str::slug($mataKuliah->nama_matkul) ?: 'mata-kuliah-'.$mataKuliah->id;
            $slug = $base;
            $counter = 2;

            while (in_array($slug, $usedSlugs, true)) {
                $slug = $base.'-'.$counter;
                $counter++;
            }

            DB::table('mata_kuliah')
                ->where('id', $mataKuliah->id)
                ->update(['slug' => $slug]);

            $usedSlugs[] = $slug;
        }

        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->string('slug', 120)->nullable(false)->change();
            $table->unique('slug', 'mata_kuliah_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropUnique('mata_kuliah_slug_unique');
            $table->dropColumn('slug');
        });
    }
};
