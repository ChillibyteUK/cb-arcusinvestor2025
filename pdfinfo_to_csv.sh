#!/bin/bash

# Output CSV header
echo "Filename,Title,Author,Creator"

# Loop through all PDF files in the current directory
for file in *.pdf; do
    [ -e "$file" ] || continue

    title=$(pdfinfo "$file" | awk -F': ' '/^Title:/ {print $2}' | sed 's/,/ /g' | sed 's/^[ \t]*//;s/[ \t]*$//')
    author=$(pdfinfo "$file" | awk -F': ' '/^Author:/ {print $2}' | sed 's/,/ /g' | sed 's/^[ \t]*//;s/[ \t]*$//')
    creator=$(pdfinfo "$file" | awk -F': ' '/^Creator:/ {print $2}' | sed 's/,/ /g' | sed 's/^[ \t]*//;s/[ \t]*$//')

    echo "\"$file\",\"$title\",\"$author\",\"$creator\""
done

