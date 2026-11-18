<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$failures = [];

$requiredTables = [
    'pengumpulan_milestone',
    'milestone_mata_kuliah',
    'penyelesaian_milestone',
    'riwayat_penyelesaian_milestone',
];

foreach ($requiredTables as $table) {
    if (! Schema::hasTable($table)) {
        $failures[] = "Tabel tidak ditemukan: {$table}";
    }
}

$requiredColumns = [
    'mata_kuliah' => ['slug'],
    'pengumpulan_milestone' => ['kelompok_id', 'milestone_id', 'mata_kuliah'],
    'milestone_mata_kuliah' => ['milestone_id', 'mata_kuliah_id'],
    'penyelesaian_milestone' => [
        'kelompok_id',
        'milestone_id',
        'mata_kuliah_id',
        'status',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ],
    'riwayat_penyelesaian_milestone' => [
        'penyelesaian_milestone_id',
        'dosen_id',
        'status_lama',
        'status_baru',
        'catatan',
    ],
];

foreach ($requiredColumns as $table => $columns) {
    if (! Schema::hasTable($table)) {
        continue;
    }

    foreach ($columns as $column) {
        if (! Schema::hasColumn($table, $column)) {
            $failures[] = "Kolom tidak ditemukan: {$table}.{$column}";
        }
    }
}

$requiredIndexes = [
    'mata_kuliah' => ['mata_kuliah_slug_unique'],
    'pengumpulan_milestone' => ['pengumpulan_milestone_identitas_unique'],
    'milestone_mata_kuliah' => ['milestone_mata_kuliah_pair_unique'],
    'penyelesaian_milestone' => ['penyelesaian_milestone_identitas_unique'],
];

foreach ($requiredIndexes as $table => $indexes) {
    if (! Schema::hasTable($table)) {
        continue;
    }

    $existing = collect(Schema::getIndexes($table))
        ->pluck('name')
        ->all();

    foreach ($indexes as $index) {
        if (! in_array($index, $existing, true)) {
            $failures[] = "Index tidak ditemukan: {$table}.{$index}";
        }
    }
}

$requiredForeignKeys = [
    ['pengumpulan_milestone', ['mata_kuliah'], 'mata_kuliah', ['slug']],
    ['penyelesaian_milestone', ['kelompok_id'], 'kelompok', ['id']],
    ['penyelesaian_milestone', ['milestone_id'], 'milestone', ['id']],
    ['penyelesaian_milestone', ['mata_kuliah_id'], 'mata_kuliah', ['id']],
];

foreach ($requiredForeignKeys as [$table, $columns, $foreignTable, $foreignColumns]) {
    if (! Schema::hasTable($table)) {
        continue;
    }

    $foreignKey = collect(Schema::getForeignKeys($table))
        ->first(fn ($key) => $key['columns'] === $columns
            && $key['foreign_table'] === $foreignTable
            && $key['foreign_columns'] === $foreignColumns);

    if (! $foreignKey) {
        $failures[] = "Foreign key tidak ditemukan: {$table}(".implode(',', $columns).') -> '
            .$foreignTable.'('.implode(',', $foreignColumns).')';
    }
}

if ($failures !== []) {
    fwrite(STDERR, "DEPLOY_SCHEMA_CHECK_FAILED\n");
    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }
    exit(1);
}

echo "DEPLOY_SCHEMA_CHECK_OK\n";
