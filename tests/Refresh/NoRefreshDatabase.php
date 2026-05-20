<?php

namespace Tests\Refresh;

/**
 * Trait que evita el refresh de la base de datos.
 *
 * En este proyecto las tablas legacy (gener02, mercurio01, etc.)
 * no existen en el entorno de test y las migraciones no se pueden
 * ejecutar contra la BD de test. Este trait reemplaza a RefreshDatabase
 * para que los tests no intenten interactuar con la BD.
 *
 * NOTA: los tests que realmente necesiten base de datos (Mercurio, etc.)
 * deben usar DatabaseMigrations en su lugar.
 */
trait NoRefreshDatabase
{
    protected function defineDatabaseMigrations()
    {
        // No ejecuta migraciones
    }

    protected function refreshDatabase()
    {
        // No hace nada - la BD no está disponible para tests
    }

    protected function beginDatabaseTransaction()
    {
        // No hace nada
    }

    protected function runDatabaseMigrations()
    {
        // No hace nada
    }
}