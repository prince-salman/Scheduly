<?php

$dir = __DIR__ . '/resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    '📅' => '<i data-lucide="calendar" class="icon-sm"></i>',
    '👋' => '<i data-lucide="hand" class="icon-sm"></i>',
    '⏳' => '<i data-lucide="hourglass" class="icon-lg text-primary"></i>',
    '❌' => '<i data-lucide="x-circle" class="icon-sm"></i>',
    '✅' => '<i data-lucide="check-circle" class="icon-sm"></i>',
    '⏰' => '<i data-lucide="clock" class="icon-sm"></i>',
    '⚠️' => '<i data-lucide="alert-triangle" class="icon-sm"></i>',
    '🔔' => '<i data-lucide="bell" class="icon-sm"></i>',
    '👁️' => '<i data-lucide="eye" class="icon-sm"></i>',
    '⚙️' => '<i data-lucide="settings" class="icon-sm"></i>',
];

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($file);
        $original = $content;
        
        foreach ($replacements as $emoji => $icon) {
            $content = str_replace($emoji, $icon, $content);
        }
        
        if ($content !== $original) {
            file_put_contents($file, $content);
            echo "Updated " . $file->getBasename() . "\n";
        }
    }
}
