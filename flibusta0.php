<?php

$script_dir = dirname(__FILE__);
$site_file  = $script_dir . '/flibusta.txt';

if (file_exists($site_file)) {
    echo "Файл flibusta.txt найден в директории скрипта.\n";

    $links = file($site_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($links as $link) {
        $link = trim($link);
        $link = $link . '/download';

        if (strpos($link, "/b/") !== false) {
            echo "Ссылка содержит /b/. Выполняем wget для: $link\n";
            system("wget --recursive -w 2 --no-clobber --content-disposition --restrict-file-names=windows --user-agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36' $link");
        } else {
            echo "Ссылка НЕ содержит /b/. Пропускаем: $link\n";
        }
    }

} else {
    echo "Файл flibusta.txt не найден в директории скрипта.\n";
    // Ваш код для обработки отсутствия файла
}
