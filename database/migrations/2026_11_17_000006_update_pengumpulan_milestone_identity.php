<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $invalid = DB::table('pengumpulan_milestone')
            ->where(function ($query) {
                $query->whereNull('mata_kuliah')
                    ->orWhereRaw('TRIM(mata_kuliah) = ?', ['']);
            })
            ->count();

        if ($invalid > 0) {
            throw new RuntimeException(
                'Migration dibatalkan: ada '.$invalid.' submission dengan mata_kuliah NULL/kosong. '
                .'Lengkapi data tersebut sebelum menjalankan migration ini.'
            );
        }

        $tooLong = DB::table('pengumpulan_milestone')
            ->whereRaw('LENGTH(mata_kuliah) > 120')
            ->count();

        if ($tooLong > 0) {
            throw new RuntimeException(
                'Migration dibatalkan: ada '.$tooLong.' submission dengan mata_kuliah lebih dari 120 karakter.'
            );
        }

        $unknownSlugs = DB::table('pengumpulan_milestone')
            ->leftJoin('mata_kuliah', 'mata_kuliah.slug', '=', 'pengumpulan_milestone.mata_kuliah')
            ->whereNull('mata_kuliah.id')
            ->distinct()
            ->pluck('pengumpulan_milestone.mata_kuliah');

        if ($unknownSlugs->isNotEmpty()) {
            throw new RuntimeException(
                'Migration dibatalkan: slug mata_kuliah tidak dikenal: '.$unknownSlugs->implode(', ')
            );
        }

        Schema::table('pengumpulan_milestone', function (Blueprint $table) {
            $table->string('mata_kuliah', 120)->nullable(false)->change();
        });

        Schema::table('pengumpulan_milestone', function (Blueprint $table) {
            $table->dropUnique('pengumpulan_milestone_kelompok_id_milestone_id_unique');
            $table->index('mata_kuliah', 'pengumpulan_milestone_mata_kuliah_index');
            $table->foreign('mata_kuliah')
                ->references('slug')
                ->on('mata_kuliah')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unique(
                ['kelompok_id', 'milestone_id', 'mata_kuliah'],
                'pengumpulan_milestone_identitas_unique'
            );
        });
    }

    public function down(): void
    {
        $duplicates = DB::table('pengumpulan_milestone')
            ->select('kelompok_id', 'milestone_id')
            ->groupBy('kelompok_id', 'milestone_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $contoh = $duplicates->take(5)
                ->map(fn ($row) => 'kelompok '.$row->kelompok_id.' / milestone '.$row->milestone_id)
                ->implode('; ');

            throw new RuntimeException(
                'Rollback dibatalkan: ada '.$duplicates->count().' pasangan kelompok-milestone '
                .'yang memiliki submission lintas mata kuliah ('.$contoh.'). '
                .'Hapus atau pisahkan data tersebut secara manual sebelum rollback; '
                .'migration tidak akan menghapus atau menggabungkan data.'
            );
        }

        Schema::table('pengumpulan_milestone', function (Blueprint $table) {
            $table->dropUnique('pengumpulan_milestone_identitas_unique');
            $table->dropForeign(['mata_kuliah']);
            $table->dropIndex('pengumpulan_milestone_mata_kuliah_index');
        });

        Schema::table('pengumpulan_milestone', function (Blueprint $table) {
            $table->string('mata_kuliah', 120)->nullable()->change();
        });

        Schema::table('pengumpulan_milestone', function (Blueprint $table) {
            $table->unique(
                ['kelompok_id', 'milestone_id'],
                'pengumpulan_milestone_kelompok_id_milestone_id_unique'
            );
        });
    }
};
