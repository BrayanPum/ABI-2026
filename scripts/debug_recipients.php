<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;

$p = Project::with(['students.user', 'professors.user'])->find(36);
if (! $p) {
    echo 'PROJECT_NOT_FOUND';
    exit(0);
}

$emails = [];
foreach ($p->students as $s) {
    if ($s->user && $s->user->email) {
        $emails[] = $s->user->email;
    }
}
foreach ($p->professors as $prof) {
    if ($prof->user && $prof->user->email) {
        $emails[] = $prof->user->email;
    }
}
echo implode(',', array_unique($emails));
