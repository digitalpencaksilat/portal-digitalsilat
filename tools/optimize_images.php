<?php
/**
 * One-time image migration helper.
 * Run from the project root with the XAMPP PHP binary:
 * php tools/optimize_images.php --dry-run
 * php tools/optimize_images.php
 */
if (PHP_SAPI !== 'cli') {
    exit("CLI only.\n");
}

define('BASEPATH', dirname(__DIR__) . '/system/');
require dirname(__DIR__) . '/application/libraries/Image_optimizer.php';

$root = dirname(__DIR__);
$targets = [
    ['path' => $root . '/assets/carousel', 'max_dimension' => 1920, 'quality' => 84],
    ['path' => $root . '/assets/uploads/posters', 'max_dimension' => 1400, 'quality' => 84],
];
$dry_run = in_array('--dry-run', $argv, TRUE);
$optimizer = new Image_optimizer();
$processed = 0;
$skipped = 0;
$saved = 0;

foreach ($targets as $target) {
    $files = glob($target['path'] . '/*.{jpg,jpeg,png}', GLOB_BRACE) ?: [];
    foreach ($files as $source) {
        $before = filesize($source);
        if ($dry_run) {
            printf("would optimize %s (%s)\n", str_replace($root . '/', '', $source), format_bytes($before));
            continue;
        }

        $staging = $target['path'] . '/.optimized';
        $result = $optimizer->process($source, $staging, [
            'max_dimension' => $target['max_dimension'],
            'quality' => $target['quality'],
        ]);
        if (!$result['status'] || empty($result['webp'])) {
            $skipped++;
            printf("skip %s: %s\n", basename($source), $result['error']);
            continue;
        }

        $destination = $target['path'] . '/' . pathinfo($source, PATHINFO_FILENAME) . '.webp';
        if (!@rename($staging . '/' . $result['webp'], $destination)) {
            $skipped++;
            printf("skip %s: cannot move result\n", basename($source));
            continue;
        }
        @unlink($staging . '/' . $result['jpeg']);
        $after = filesize($destination);
        $processed++;
        $saved += max(0, $before - $after);
        printf("optimized %s -> %s (%s -> %s)\n", basename($source), basename($destination), format_bytes($before), format_bytes($after));
    }
}

if (!$dry_run) {
    foreach ($targets as $target) {
        @rmdir($target['path'] . '/.optimized');
    }
    printf("completed: %d optimized, %d skipped, %s saved\n", $processed, $skipped, format_bytes($saved));
}

function format_bytes($bytes)
{
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 2) . ' MB';
}
