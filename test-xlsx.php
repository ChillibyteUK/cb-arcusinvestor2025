<?php
/**
 * Simple test script for XLSX functionality
 */

// Create a simple XLSX file for testing
function create_test_xlsx($filename) {
    $zip = new ZipArchive();
    if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
        return false;
    }
    
    // Create minimal XLSX structure
    $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
<Default Extension="xml" ContentType="application/xml"/>
<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>');
    
    $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');
    
    $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
<sheets>
<sheet name="Sheet1" sheetId="1" r:id="rId1"/>
</sheets>
</workbook>');
    
    $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>');
    
    $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
<sheetData>
<row r="1">
<c r="A1" t="inlineStr"><is><t>Test Data</t></is></c>
</row>
</sheetData>
</worksheet>');
    
    $zip->close();
    return true;
}

// Test the functionality
$test_file = '/tmp/test_file.xlsx';
if (create_test_xlsx($test_file)) {
    echo "Test XLSX file created successfully at: $test_file\n";
    
    // Check if file exists and is valid
    if (file_exists($test_file)) {
        echo "File size: " . filesize($test_file) . " bytes\n";
        
        // Test opening with ZipArchive
        $zip = new ZipArchive();
        if ($zip->open($test_file) === TRUE) {
            echo "ZipArchive can open the file successfully\n";
            echo "Number of files in archive: " . $zip->numFiles . "\n";
            $zip->close();
        } else {
            echo "ERROR: ZipArchive cannot open the file\n";
        }
        
        // Clean up
        unlink($test_file);
        echo "Test file cleaned up\n";
    } else {
        echo "ERROR: Test file was not created\n";
    }
} else {
    echo "ERROR: Failed to create test XLSX file\n";
}

echo "XLSX functionality test completed\n";
?>
