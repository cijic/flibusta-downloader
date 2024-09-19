#!/bin/bash

script_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
site_file="$script_dir/flibusta.txt"

if [ -f "$site_file" ]; then
    echo "Файл flibusta.txt найден в директории скрипта."

    while IFS= read -r link; do
        if [[ $link == *"/b/"* ]]; then
            echo "Ссылка содержит /b/. Выполняем wget для: $link"
            wget --random-wait --recursive --no-clobber --content-disposition --restrict-file-names=windows --user-agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3" "$link"/download
        else
            echo "Ссылка НЕ содержит /b/. Пропускаем: $link"
        fi
    done < "$site_file"

else
    echo "Файл flibusta.txt не найден в директории скрипта."
    # Ваш код для обработки отсутствия файла
fi
