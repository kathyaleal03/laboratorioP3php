<?php
require __DIR__ . '/../vendor/autoload.php';

echo "Checking dompdf availability...\n";

echo "class_exists(Barryvdh\\DomPDF\\Facade\\Pdf): ";
echo class_exists('Barryvdh\\DomPDF\\Facade\\Pdf') ? "1\n" : "0\n";

// Bootstrap the framework so providers are registered
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "app->bound('dompdf.wrapper'): ";
echo $app->bound('dompdf.wrapper') ? "1\n" : "0\n";

echo "Done.\n";