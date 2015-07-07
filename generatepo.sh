find . -iname "*.php" > /tmp/sln_file_list.txt
xgettext --from-code=utf-8 -d sln  -f /tmp/sln_file_list.txt --keyword=__ -o languages/sln-en_EN.po
xgettext --from-code=utf-8 -d sln  -f /tmp/sln_file_list.txt --keyword=__ -o languages/sln-en_US.po
xgettext --from-code=utf-8 -d sln  -f /tmp/sln_file_list.txt --keyword=__ -o languages/sln-it_IT.po
xgettext --from-code=utf-8 -d sln  -f /tmp/sln_file_list.txt --keyword=__ -o languages/sln-de_DE.po
xgettext --from-code=utf-8 -d sln  -f /tmp/sln_file_list.txt --keyword=__ -o languages/sln-es_ES.po
xgettext --from-code=utf-8 -d sln  -f /tmp/sln_file_list.txt --keyword=__ -o languages/sln-fr_FR.po
sed --in-place languages/* --expression=s/CHARSET/UTF-8/
