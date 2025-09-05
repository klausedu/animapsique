<?php
/**
 * Script de Conexão com o Banco de Dados usando PDO.
 *
 * Este arquivo utiliza as constantes definidas em config.php para
 * estabelecer uma conexão segura com o banco de dados.
 */

// Inclui o arquivo de configuração apenas uma vez
require_once __DIR__ . '/../config.php';

// Variável global para a conexão, se preferir
$pdo = null;

/**
 * Função para conectar ao banco de dados.
 *
 * @return PDO|null Retorna um objeto PDO em caso de sucesso ou null em caso de falha.
 */
function conectar(): ?PDO
{
    global $pdo;

    // Se já estiver conectado, retorna a conexão existente
    if ($pdo) {
        return $pdo;
    }

    // Define o DSN (Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    // Define as opções do PDO
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Retorna arrays associativos
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa prepared statements nativos
    ];

    try {
        // Tenta criar a instância do PDO
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (\PDOException $e) {
        // Em caso de erro, exibe uma mensagem genérica em produção
        // e loga o erro real para o desenvolvedor.
        // Em desenvolvimento, você pode descomentar a linha abaixo para ver o erro completo.
        // throw new \PDOException($e->getMessage(), (int)$e->getCode());
        error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
        die("Erro ao conectar com o servidor. Por favor, tente mais tarde.");
    }
}

// Exemplo de como chamar a função
// $conexao = conectar();
?>
