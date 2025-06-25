<?php

// Path to the views directory
$viewsDirectory = __DIR__ . '/../resources/views';

// Get all blade files
$bladeFiles = getAllFiles($viewsDirectory, 'blade.php');

// Templates to look for and remove
$patterns = [
    // Success message pattern
    '/@if\s*\(\s*session\s*\(\s*[\'"]success[\'"]\s*\)\s*\)\s*\n\s*<div[^>]*>\s*\n\s*{{\s*session\s*\(\s*[\'"]success[\'"]\s*\)\s*}}\s*\n\s*<\/div>\s*\n\s*@endif/m',
    
    // Error message pattern
    '/@if\s*\(\s*session\s*\(\s*[\'"]error[\'"]\s*\)\s*\)\s*\n\s*<div[^>]*>\s*\n\s*{{\s*session\s*\(\s*[\'"]error[\'"]\s*\)\s*}}\s*\n\s*<\/div>\s*\n\s*@endif/m',
    
    // Warning message pattern
    '/@if\s*\(\s*session\s*\(\s*[\'"]warning[\'"]\s*\)\s*\)\s*\n\s*<div[^>]*>\s*\n\s*{{\s*session\s*\(\s*[\'"]warning[\'"]\s*\)\s*}}\s*\n\s*<\/div>\s*\n\s*@endif/m',
];

// Skip main layout files
$skipFiles = [
    $viewsDirectory . '/layouts/app.blade.php',
    $viewsDirectory . '/layouts/admin.blade.php',
    $viewsDirectory . '/layouts/student.blade.php',
    $viewsDirectory . '/layouts/teacher.blade.php',
    $viewsDirectory . '/layouts/moderator.blade.php',
];

// Counter for modified files
$modifiedCount = 0;

// Process each blade file
foreach ($bladeFiles as $bladeFile) {
    // Skip layout files
    if (in_array($bladeFile, $skipFiles)) {
        echo "Skipping layout file: " . basename($bladeFile) . "\n";
        continue;
    }
    
    // Get the file content
    $content = file_get_contents($bladeFile);
    $originalContent = $content;
    
    // Apply each pattern
    foreach ($patterns as $pattern) {
        $content = preg_replace($pattern, '', $content);
    }
    
    // Check if content was modified
    if ($content != $originalContent) {
        // Write the modified content back to the file
        file_put_contents($bladeFile, $content);
        echo "Removed duplicate messages from: " . basename($bladeFile) . "\n";
        $modifiedCount++;
    }
}

echo "Finished processing. Modified {$modifiedCount} files.\n";

/**
 * Recursively get all files with a specific extension from a directory
 * 
 * @param string $dir Directory to search
 * @param string $extension File extension to filter
 * @return array Array of file paths
 */
function getAllFiles($dir, $extension) {
    $files = [];
    $directories = new RecursiveDirectoryIterator($dir);
    $iterator = new RecursiveIteratorIterator($directories);
    
    foreach ($iterator as $file) {
        if ($file->isFile() && substr($file->getFilename(), -strlen($extension)) === $extension) {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
} 