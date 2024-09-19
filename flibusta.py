import os
import subprocess

script_dir = os.path.dirname(os.path.realpath(__file__))
site_file = os.path.join(script_dir, 'flibusta.txt')

if os.path.isfile(site_file):
    print("Файл flibusta.txt найден в директории скрипта.")

    with open(site_file, 'r') as file:
        for link in file:
            link = link.strip()
            if "/b/" in link:
                print(f"Ссылка содержит /b/. Выполняем wget для: {link}")
                subprocess.run(['wget', '--recursive', '--no-clobber', '--content-disposition', '--restrict-file-names=windows', '--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3', link + '/download'])
            else:
                print(f"Ссылка НЕ содержит /b/. Пропускаем: {link}")

else:
    print("Файл flibusta.txt не найден в директории скрипта.")
    # Ваш код для обработки отсутствия файла
