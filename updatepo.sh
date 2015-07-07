find . -iname "*.php" > /tmp/sln_file_list.txt
xgettext --from-code=utf-8 -d sln -j -f /tmp/sln_file_list.txt --keyword=__ --keyword=_e -o languages/sln-en_EN.po
xgettext --from-code=utf-8 -d sln -j -f /tmp/sln_file_list.txt --keyword=__ --keyword=_e -o languages/sln-en_US.po
xgettext --from-code=utf-8 -d sln -j -f /tmp/sln_file_list.txt --keyword=__ --keyword=_e -o languages/sln-it_IT.po
xgettext --from-code=utf-8 -d sln -j -f /tmp/sln_file_list.txt --keyword=__ --keyword=_e -o languages/sln-de_DE.po
xgettext --from-code=utf-8 -d sln -j -f /tmp/sln_file_list.txt --keyword=__ --keyword=_e -o languages/sln-fr_FR.po
xgettext --from-code=utf-8 -d sln -j -f /tmp/sln_file_list.txt --keyword=__ --keyword=_e -o languages/sln-es_ES.po
