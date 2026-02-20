<?php

class PasswordValidator
{
    public function validate(string $password, array $requirements): array
    {
        $result = [
            'length'    => strlen($password) >= ($requirements['minLength'] ?? 8),
            // ✅ FIXED: paréntesis correctos para controlar la precedencia del operador !
            'uppercase' => !($requirements['requireUppercase'] ?? false) || (bool) preg_match('/[A-Z]/', $password),
            'numbers'   => !($requirements['requireNumbers']   ?? false) || (bool) preg_match('/[0-9]/', $password),
            'symbols'   => !($requirements['requireSymbols']   ?? false) || (bool) preg_match('/[^a-zA-Z0-9]/', $password),
        ];

        $score = count(array_filter($result));

        return [
            'valid'   => !in_array(false, $result, true),
            'score'   => $score,
            'details' => $result
        ];
    }
}
