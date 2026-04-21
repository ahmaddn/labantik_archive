<?php

// Small script to list first 10 siswa with NIS, NIP and class
// Run: php scripts/list_students.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$students = User::whereHas('roles', fn($q) => $q->where('code', 'siswa'))
    ->with(['employee', 'latestStudentAcademicYear.refClass', 'refClass'])
    ->take(10)
    ->get();

if ($students->isEmpty()) {
    echo "No siswa found.\n";
    exit(0);
}

echo "ID\tName\tNIS\tNIP\tClass\n";
foreach ($students as $u) {
    $id = $u->id;
    $name = $u->name;
    $nis = $u->nis ?? '-';
    $nip = $u->nip ?? '-';
    $class = $u->class_name ?? '-';
    echo "{$id}\t{$name}\t{$nis}\t{$nip}\t{$class}\n";
}

return 0;
