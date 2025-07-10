<?php
/**
 * Detailed PDF structure analysis
 */

require_once '/var/www/investor/wp-config.php';

$file_path = '/var/www/investor/wp-content/uploads/2025/06/Zensen-Annual-Report-2023.pdf';

echo "=== Detailed PDF Structure Analysis ===\n";

$content = file_get_contents($file_path);
if ($content === false) {
    echo "Could not read file\n";
    exit;
}

echo "PDF Content Length: " . number_format(strlen($content)) . " bytes\n\n";

// Look for Info object reference
if (preg_match('/\/Info\s+(\d+)\s+(\d+)\s+R/', $content, $matches)) {
    echo "Found Info object reference: /Info {$matches[1]} {$matches[2]} R\n";
    $info_obj_id = $matches[1];
    $info_gen_id = $matches[2];
    
    // Look for the actual object definition
    $obj_patterns = array(
        "/{$info_obj_id}\s+{$info_gen_id}\s+obj\s*<<([^>]*)>>/s",
        "/{$info_obj_id}\s+{$info_gen_id}\s+obj\s*<<(.*?)>>/s",
        "/{$info_obj_id}\s+\d+\s+obj\s*<<([^>]*)>>/s",
    );
    
    foreach ($obj_patterns as $pattern) {
        if (preg_match($pattern, $content, $obj_matches)) {
            echo "Found Info object definition:\n";
            echo "Pattern: $pattern\n";
            echo "Content: " . trim($obj_matches[1]) . "\n";
            break;
        }
    }
    
    if (!isset($obj_matches)) {
        echo "Could not find Info object definition for object ID $info_obj_id\n";
        
        // Let's search for any object with this ID
        if (preg_match("/{$info_obj_id}\s+\d+\s+obj(.*?)endobj/s", $content, $debug_matches)) {
            echo "Found object $info_obj_id (raw):\n";
            echo substr($debug_matches[1], 0, 500) . "...\n";
        }
    }
} else {
    echo "No Info object reference found\n";
}

// Look for any metadata-like patterns
echo "\n=== Searching for Metadata Patterns ===\n";
$patterns = array(
    'Title' => '/\/Title\s*\(([^)]*)\)/',
    'Author' => '/\/Author\s*\(([^)]*)\)/',
    'Creator' => '/\/Creator\s*\(([^)]*)\)/',
    'Producer' => '/\/Producer\s*\(([^)]*)\)/',
    'Subject' => '/\/Subject\s*\(([^)]*)\)/',
    'Keywords' => '/\/Keywords\s*\(([^)]*)\)/',
);

foreach ($patterns as $name => $pattern) {
    if (preg_match($pattern, $content, $matches)) {
        echo "$name: " . $matches[1] . "\n";
    } else {
        echo "$name: Not found\n";
    }
}

// Look for dictionary structures
echo "\n=== Searching for Dictionary Structures ===\n";
if (preg_match_all('/<<[^>]*>>/s', $content, $dict_matches)) {
    echo "Found " . count($dict_matches[0]) . " dictionary structures\n";
    
    // Show first few that might contain metadata
    $count = 0;
    foreach ($dict_matches[0] as $dict) {
        if (strpos($dict, '/Title') !== false || 
            strpos($dict, '/Author') !== false || 
            strpos($dict, '/Creator') !== false ||
            strpos($dict, '/Producer') !== false) {
            echo "Dictionary $count: " . substr(trim($dict), 0, 200) . "...\n";
            $count++;
            if ($count >= 3) break;
        }
    }
}

echo "\n=== Analysis Complete ===\n";
