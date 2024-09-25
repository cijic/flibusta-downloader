<?php

# TODO:
# 1/ Extract      file_put_contents('flibusta.txt', join("\n", array_slice($links, $linkKey + 1))); to function
# 2/ Replace flibusta.txt with value from .env-file.
# 3/ Move getBookInfo before downloading.

function sanitizeFilename(string $filename, string $sanitizer = ' ')
{
    $unsupportedSymbols = ['\\', '/', ':', '*', '?', '"', '<', '>', '|', "\t", "\r", "\n", "\v"];
    $filename           = str_replace($unsupportedSymbols, $sanitizer, $filename);

    return $filename;
}

function getDotEnv(): array | false
{
    return parse_ini_file('.env');
}

function getStreamContext()
{
    $env       = getDotEnv();
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3';
    $options   = [
        'http' => [
            'header'  => 'User-Agent: ' . $userAgent . "\r\n" .
            'Cookie: ' . $env['COOKIE_KEY'] . '=' . $env['COOKIE_VAL'] . ';' . "\r\n",
            'timeout' => 60,
        ],
    ];
    $context = stream_context_create($options);
    return $context;
}

function getBookInfo(string $link): ?array
{
    $page = file_get_contents($link, false, getStreamContext());
    $dom  = new DOMDocument();
    $dom->loadHTML($page);
    $xpath         = new DOMXPath($dom);
    $titleElements = $xpath->query('//*[@id="main"]/h1');

    if (!empty($titleElements->length)) {
        $title            = $titleElements->item(0)->textContent;
        $titlePattern     = '/(.+)\s*\(.+\)/i';
        $titleReplacement = '$1';
        $title            = trim(preg_replace($titlePattern, $titleReplacement, $title));

        $authorsElements = $xpath->query('//*[@id="main"]/a[1]');
        $authors         = !empty($authorsElements->length) ? trim($authorsElements->item(0)->textContent) : '';
        $yearElements    = $xpath->query('//*[@id="main"]/text()[6]');
        $year            = '';

        if (!empty($yearElements->length)) {
            $yearInfo = trim($yearElements->item(0)->textContent);
            preg_match('/(\d{4})/', $yearInfo, $yearMatches);
            $year = reset($yearMatches) ?? $year;
        }

        return [
            'authors' => $authors,
            'title'   => $title,
            'year'    => $year,
        ];
    }

    echo 'Item not found.';
    return null;
}

function main(): void
{
    $urlsFile   = dirname(__FILE__) . '/flibusta.txt';
    $cliOptions = getopt('', [
        'useRemoteFilename',
    ]);

    if (!file_exists($urlsFile)) {
        echo "File flibusta.txt not found." . PHP_EOL;
        return;
    }

    $links = file($urlsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($links as $linkKey => $link) {
        $link = trim($link);

        if (strpos($link, "/b/") == false) {
            echo "Link does NOT contain /b/. Skipping: $link\n";
            return;
        }

        echo "Downloading $link" . PHP_EOL;
        $downloadLink = $link . '/download';
        $file         = file_get_contents($downloadLink, false, getStreamContext());

        if ($file === false) {
            echo 'Not downloaded.' . PHP_EOL;
            continue;
        }

        $pattern            = '/Content-Disposition: attachment; filename="(.+)\.(.+)"/';
        $contentDisposition = preg_grep($pattern, $http_response_header); // As example: https://www.phpliveregex.com/p/Mr8

        if (count($contentDisposition) != 1) {
            die('Need to change cookie ¯\_(ツ)_/¯');
        }

        preg_match($pattern, reset($contentDisposition), $matches);
        $remoteFilename = $matches[1];
        $extension      = $matches[2] ?? pathinfo($remoteFilename, PATHINFO_EXTENSION);

        if (isset($cliOptions['useRemoteFilename'])) {
            $localFilename = $remoteFilename . '.' . $extension;
        } else {
            $bookInfo = getBookInfo($link);

            if (empty($bookInfo)) {
                die('BookInfo Fatality.' . PHP_EOL);
            }

            $filenameData = [
                $bookInfo['authors'],
                sanitizeFilename($bookInfo['title']),
                $bookInfo['year'],
            ];
            $filenameData  = array_filter($filenameData);
            $localFilename = join(DIRECTORY_SEPARATOR, [
                getDotEnv()['SAVE_FOLDER'],
                join(' — ', $filenameData)
                . '.' . $extension,
            ]);
        }

        if (file_exists($localFilename)) {
            echo 'File already downloaded.' . PHP_EOL;
            file_put_contents('flibusta.txt', join("\n", array_slice($links, $linkKey + 1)));
            continue;
        }

        if (!file_put_contents($localFilename, $file) === false) {
            echo 'Downloaded. Name: ' . $localFilename . PHP_EOL;
        }

        file_put_contents('flibusta.txt', join("\n", array_slice($links, $linkKey + 1)));

        sleep(random_int(3, 7));
    }
}

main();
