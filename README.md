# reveerse_shell_on_php8

Реверс-шелл — это тип шелла, который устанавливает соединение обратно к атакующему, позволяя атакующему управлять системой через сеть. 

Обратите внимание на следующие моменты:

    - Этот код требует, чтобы на целевой машине был настроен сервер, который слушает указанный IP-адрес и порт (в этом примере 127.0.0.1 и 1234) и ожидает входящих соединений. В реальных условиях атакующий настраивает подобный сервер на своей машине.
    - Для работы реверс-шелла необходимо, чтобы на целевой системе был разрешен исходящий трафик на указанный IP-адрес и порт.
    - Команда /bin/sh -i в переменной $shell инициирует интерактивный шелл. Вы можете заменить её на другую команду, если необходимо.
    - Использование данного кода может быть обнаружено антивирусами и средствами обнаружения вторжений.

Используйте этот код только в легальных целях, для обучения или тестирования собственных систем на проникновение с явным разрешением владельцев системы.
