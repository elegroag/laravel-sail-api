<?php

namespace App\Services;

use Dotenv\Dotenv;
use Illuminate\Support\Collection;
use mysqli;

class LegacyDatabaseService
{
    protected ?mysqli $connection = null;

    public function __construct($env = 'mercurio')
    {
        // Cargar variables de entorno desde database/seeders/.env
        $dotenvPath = base_path('database/seeders');
        if (file_exists($dotenvPath.'/.env.'.$env)) {
            $dotenv = Dotenv::createImmutable($dotenvPath, '.env.'.$env);
            $dotenv->load();
        }

        $host = $_ENV['LEGACY_DB_HOST'] ?? 'localhost';
        $username = $_ENV['LEGACY_DB_USERNAME'] ?? 'root';
        $password = $_ENV['LEGACY_DB_PASSWORD'] ?? '';
        $database = $_ENV['LEGACY_DB_DATABASE'] ?? '';
        $port = isset($_ENV['LEGACY_DB_PORT']) ? (int) $_ENV['LEGACY_DB_PORT'] : 3306;

        $this->connection = new mysqli($host, $username, $password, $database, $port);

        if ($this->connection->connect_error) {
            throw new \Exception('No se pudo conectar a la base de datos legada: '.$this->connection->connect_error);
        }

        $this->connection->set_charset('utf8');
    }

    /**
     * Ejecuta una consulta SELECT y devuelve los resultados como Collection.
     * Soporta bindings básicos con real_escape_string (suficiente para seeders internos).
     */
    public function select(string $query, array $bindings = []): Collection
    {
        $query = $this->prepareQuery($query, $bindings);

        $result = $this->connection->query($query);

        if (! $result) {
            throw new \Exception('Error en consulta legada: '.$this->connection->error." - Query: {$query}");
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();

        return collect($rows);
    }

    /**
     * Ejecuta una consulta COUNT y devuelve el valor escalar.
     * La query debe devolver exactamente una fila y una columna.
     */
    public function count(string $query, array $bindings = []): int
    {
        $query = $this->prepareQuery($query, $bindings);

        $result = $this->connection->query($query);

        if (! $result) {
            throw new \Exception('Error en consulta legada (count): '.$this->connection->error." - Query: {$query}");
        }

        $row = $result->fetch_row();
        $result->free();

        return (int) ($row[0] ?? 0);
    }

    /**
     * Sustituye placeholders ? por valores escapados.
     * Suficiente para seeders internos/confiables.
     */
    private function prepareQuery(string $query, array $bindings = []): string
    {
        if (empty($bindings)) {
            return $query;
        }

        foreach ($bindings as $value) {
            $escaped = $this->connection->real_escape_string((string) $value);
            $query = preg_replace('/\?/', "'{$escaped}'", $query, 1);
        }

        return $query;
    }

    public function disconnect(): void
    {
        if ($this->connection && ! $this->connection->connect_errno) {
            $this->connection->close();
        }
        $this->connection = null; // Marcar como cerrada
    }

    public function __destruct()
    {
        $this->disconnect(); // Reutiliza la lógica segura
    }
}
