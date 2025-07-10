<?php
/**
 * Debug the specific PDF file mentioned
 */

// Set up WordPress environment
require_once '/var/www/investor/wp-config.php';
require_once '/var/www/investor/wp-content/themes/cb-arcusinvestor2025/inc/cb-docrepo-admin.php';

$file_path = '/var/www/investor/wp-content/uploads/2025/06/Zensen-Annual-Report-2023.pdf';
$filename = basename($file_path);

echo "=== Debugging: $filename ===\n";

if (!file_exists($file_path)) {
    echo "File not found: $file_path\n";
    exit;
}

echo "File exists: Yes\n";
echo "File size: " . number_format(filesize($file_path)) . " bytes\n";

// Test compatibility functions
echo "\n=== Compatibility Tests ===\n";
$watermark_compatible = cb_test_pdf_watermark_compatibility($file_path);
echo "Watermark compatible: " . ($watermark_compatible ? "Yes" : "No") . "\n";

$metadata_compatible = cb_test_pdf_metadata_compatibility($file_path);
echo "Metadata compatible: " . ($metadata_compatible ? "Yes" : "No") . "\n";

$direct_metadata_compatible = cb_test_direct_metadata_modification($file_path);
echo "Direct metadata compatible: " . ($direct_metadata_compatible ? "Yes" : "No") . "\n";

// Test actual metadata addition
echo "\n=== Testing Actual Metadata Addition ===\n";
$test_watermark = "Test watermark: " . date('Y-m-d H:i:s');
$temp_output = sys_get_temp_dir() . '/debug_metadata_' . time() . '.pdf';

echo "Testing cb_add_metadata_only_pdf...\n";
$metadata_success = cb_add_metadata_only_pdf($file_path, $temp_output, $test_watermark);
echo "Metadata addition successful: " . ($metadata_success ? "Yes" : "No") . "\n";

if ($metadata_success && file_exists($temp_output)) {
    echo "Output file created: Yes\n";
    echo "Output file size: " . number_format(filesize($temp_output)) . " bytes\n";
    
    // Extract metadata from the output file
    $metadata = cb_extract_pdf_metadata($temp_output);
    echo "Extracted keywords: " . $metadata['keywords'] . "\n";
    
    // Check if our test watermark is in the keywords
    if (strpos($metadata['keywords'], $test_watermark) !== false) {
        echo "Test watermark found in metadata: Yes\n";
    } else {
        echo "Test watermark found in metadata: No\n";
    }
} else {
    echo "Output file created: No\n";
}

// Test direct metadata modification specifically
echo "\n=== Testing Direct Metadata Modification ===\n";
$temp_output2 = sys_get_temp_dir() . '/debug_direct_' . time() . '.pdf';
$direct_success = cb_add_direct_metadata_modification($file_path, $temp_output2, $test_watermark);
echo "Direct metadata modification successful: " . ($direct_success ? "Yes" : "No") . "\n";

if ($direct_success && file_exists($temp_output2)) {
    echo "Direct output file created: Yes\n";
    echo "Direct output file size: " . number_format(filesize($temp_output2)) . " bytes\n";
    
    // Extract metadata from the direct output file
    $metadata2 = cb_extract_pdf_metadata($temp_output2);
    echo "Direct extracted keywords: " . $metadata2['keywords'] . "\n";
    
    // Check if our test watermark is in the keywords
    if (strpos($metadata2['keywords'], $test_watermark) !== false) {
        echo "Test watermark found in direct metadata: Yes\n";
    } else {
        echo "Test watermark found in direct metadata: No\n";
    }
} else {
    echo "Direct output file created: No\n";
}

// Examine the PDF content structure
echo "\n=== PDF Content Analysis ===\n";
$content = file_get_contents($file_path);
if ($content !== false) {
    echo "PDF content length: " . number_format(strlen($content)) . " bytes\n";
    
    // Look for metadata patterns
    if (preg_match('/\/Keywords\s*\(([^)]*)\)/', $content, $matches)) {
        echo "Found existing Keywords: " . $matches[1] . "\n";
    } else {
        echo "No existing Keywords found\n";
    }
    
    if (preg_match('/\/Info\s+\d+\s+\d+\s+R/', $content)) {
        echo "Found Info object reference: Yes\n";
    } else {
        echo "Found Info object reference: No\n";
    }
    
    if (preg_match('/<<\s*\/Title\s*\(([^)]*)\)/', $content, $matches)) {
        echo "Found Title: " . $matches[1] . "\n";
    } else {
        echo "No Title found\n";
    }
    
    if (preg_match('/<<\s*\/Author\s*\(([^)]*)\)/', $content, $matches)) {
        echo "Found Author: " . $matches[1] . "\n";
    } else {
        echo "No Author found\n";
    }
    
    if (preg_match('/<<\s*\/Creator\s*\(([^)]*)\)/', $content, $matches)) {
        echo "Found Creator: " . $matches[1] . "\n";
    } else {
        echo "No Creator found\n";
    }
} else {
    echo "Could not read PDF content\n";
}

// Clean up
if (file_exists($temp_output)) {
    unlink($temp_output);
}
if (file_exists($temp_output2)) {
    unlink($temp_output2);
}

echo "\n=== Debug Complete ===\n";
