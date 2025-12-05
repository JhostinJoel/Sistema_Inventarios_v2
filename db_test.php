<?php
// db_test.php
$combinations = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'port' => 3306],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'port' => 3306],
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'port' => 3307],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'port' => 3307],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root', 'port' => 3306],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root', 'port' => 3306],
];

echo "Iniciando diagnóstico de conexión...\n";

foreach ($combinations as $config) {
    echo "Probando: Host={$config['host']}, Port={$config['port']}, User={$config['user']}, Pass='" . ($config['pass'] ? '***' : '') . "' ... ";
    try {
        $dsn = "mysql:host={$config['host']};port={$config['port']}";
        $pdo = new PDO($dsn, $config['user'], $config['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ ¡ÉXITO!\n";
        echo "---------------------------------------------------\n";
        echo "Configuración correcta encontrada:\n";
        echo "Host: " . $config['host'] . "\n";
        echo "Port: " . $config['port'] . "\n";
        echo "User: " . $config['user'] . "\n";
        echo "Pass: " . ($config['pass'] ? $config['pass'] : '(vacío)') . "\n";
        echo "---------------------------------------------------\n";
        exit(0); // Terminar al encontrar la correcta
    } catch (PDOException $e) {
        echo "❌ Falló (" . $e->getCode() . ")\n";
    }
}

echo "No se pudo conectar con ninguna combinación común.\n";
