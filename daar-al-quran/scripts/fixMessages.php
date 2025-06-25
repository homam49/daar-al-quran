<?php

/**
 * Script to remove duplicate flash messages from Blade templates
 * 
 * This script will scan all Blade templates and remove duplicate success/error alerts
 * that cause double display of flash messages.
 */

// Configuration
$viewsPath = __DIR__ . '/../resources/views';
$excludeDirs = ['layouts'];

// Statistics
$fileCount = 0;
$modifiedCount = 0;

// List of files to manually check and update
$commonPatterns = [
    // Success message blocks
    '@if (session(\'success\'))
    <div class="alert alert-success">
        {{ session(\'success\') }}
    </div>
@endif',
    '@if(session(\'success\'))
    <div class="alert alert-success">
        {{ session(\'success\') }}
    </div>
@endif',
    '@if(session(\'success\'))
<div class="alert alert-success">
    {{ session(\'success\') }}
</div>
@endif',
    '@if (session(\'success\'))
                    <div class="alert alert-success">
                        {{ session(\'success\') }}
                    </div>
                @endif',
    
    // Error message blocks
    '@if (session(\'error\'))
    <div class="alert alert-danger">
        {{ session(\'error\') }}
    </div>
@endif',
    '@if(session(\'error\'))
    <div class="alert alert-danger">
        {{ session(\'error\') }}
    </div>
@endif',
    '@if(session(\'error\'))
<div class="alert alert-danger">
    {{ session(\'error\') }}
</div>
@endif',
    '@if (session(\'error\'))
                    <div class="alert alert-danger">
                        {{ session(\'error\') }}
                    </div>
                @endif',
                
    // Warning message blocks
    '@if (session(\'warning\'))
    <div class="alert alert-warning">
        {{ session(\'warning\') }}
    </div>
@endif',
    '@if(session(\'warning\'))
    <div class="alert alert-warning">
        {{ session(\'warning\') }}
    </div>
@endif',
    '@if(session(\'warning\'))
<div class="alert alert-warning">
    {{ session(\'warning\') }}
</div>
@endif',
    '@if (session(\'warning\'))
                    <div class="alert alert-warning">
                        {{ session(\'warning\') }}
                    </div>
                @endif'
];

// Main processing function
function processDirectory($dirPath, $excludeDirs, $patterns) {
    global $fileCount, $modifiedCount;
    
    $items = scandir($dirPath);
    
    foreach ($items as $item) {
        // Skip dots and excluded directories
        if ($item == '.' || $item == '..' || in_array($item, $excludeDirs)) {
            continue;
        }
        
        $path = $dirPath . '/' . $item;
        
        if (is_dir($path)) {
            // Recursively process subdirectories
            processDirectory($path, $excludeDirs, $patterns);
        } else if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) == 'php') {
            // Process blade template files
            $fileCount++;
            $content = file_get_contents($path);
            $originalContent = $content;
            
            // Apply string replacements
            foreach ($patterns as $pattern) {
                $content = str_replace($pattern, '', $content);
            }
            
            // If content changed, save the file
            if ($content != $originalContent) {
                file_put_contents($path, $content);
                $modifiedCount++;
                echo "Modified: " . str_replace(__DIR__ . '/../', '', $path) . "\n";
            }
        }
    }
}

// Start processing
echo "Starting to process Blade templates...\n";
processDirectory($viewsPath, $excludeDirs, $commonPatterns);
echo "Completed!\n";
echo "Processed $fileCount files, modified $modifiedCount files.\n"; 