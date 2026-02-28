<?php

/**
 * Carrega variáveis de ambiente de um arquivo .env para o sistema.
 *
 * Lê um arquivo `.env` linha por linha, ignora comentários (`#`) e linhas vazias,
 * e define as variáveis usando `putenv()` e preenchendo a superglobal `$_ENV`.
 *
 * @param string $path Caminho completo para o arquivo .env.
 * @return bool Retorna true se o arquivo foi carregado com sucesso, false caso contrário.
 */

function load_env($path)
{
    if (!file_exists($path)) {
        return false;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (!str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);

        $name  = trim($name);
        $value = trim($value);
        $value = trim($value, "\"';");

        if (!isset($_ENV[$name])) {
            $_ENV[$name] = $value;
            putenv("$name=$value"); // Adiciona também ao ambiente do sistema
        }
    }

    return true;
}
