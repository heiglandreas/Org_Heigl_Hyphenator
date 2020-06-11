#!/bin/bash
set -e

mkdir -p tmp
cd tmp
curl -LO https://github.com/LibreOffice/dictionaries/archive/master.zip
unzip master.zip
find dictionaries-master -name "hyph_*.dic" -exec cp {} ../src/share/files/dictionaries/ \;
find dictionaries-master -name "README_hyph_*.txt" -exec cp {} ../src/share/files/dictionaries/ \;
cd ..
rm -rf tmp
while read line; do
	NEW_NAME=`echo $line | sed "s/_ANY//"`;
	mv $line $NEW_NAME
done < <(find src/share/files/dictionaries -name "*_ANY*")

tools/renderDicts
