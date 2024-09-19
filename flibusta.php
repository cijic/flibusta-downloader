<?php

$script_dir = dirname(__FILE__);
$site_file  = $script_dir . '/flibusta.txt';

if (file_exists($site_file)) {
    echo "Файл flibusta.txt найден в директории скрипта.\n";

    $links = file($site_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($links as $link) {
        $link = trim($link);

        if (strpos($link, "/b/") !== false) {
            echo "Скачиваем $link" . PHP_EOL;
            $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
            $options   = [
                'http' => [
                    'header' => 'User-Agent: ' . $userAgent . "\r\n" .
                    'Cookie: ' . 'SESS717db4750c98b34dc0a0cf14a0c49e88=ql0bhjftqq78pr468ma7866gf1;' . "\r\n",
                ],
            ];
            $context            = stream_context_create($options);
            $file               = file_get_contents($link . '/download', false, $context);
            $pattern            = '/Content-Disposition: attachment; filename="(.+)"/';
            $contentDisposition = preg_grep($pattern, $http_response_header);

            if (count($contentDisposition) != 1) {
                continue;
            }

            preg_match($pattern, reset($contentDisposition), $matches);
            $filename = $matches[1];

            if (file_exists($filename)) {
                echo 'Файл уже скачан.' . PHP_EOL;
                continue;
            }

            file_put_contents($filename, $file);
            sleep(random_int(1, 3));
        } else {
            echo "Ссылка НЕ содержит /b/. Пропускаем: $link\n";
        }
    }

} else {
    echo "Файл flibusta.txt не найден в папке скрипта.\n";
    // Ваш код для обработки отсутствия файла
}
