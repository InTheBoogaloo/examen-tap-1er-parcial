<?php

class PasswordGeneratorService
{
    private int $minLength = 4;
    private int $maxLength = 128;
    private int $minCount  = 1;
    private int $maxCount  = 50;

    public function generate(array $params): string
    {
        $length = (int) ($params['length'] ?? 16);

        $this->validateLength($length);

        return generate_password($length, [
            'upper'           => filter_var($params['includeUppercase'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'lower'           => filter_var($params['includeLowercase'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'digits'          => filter_var($params['includeNumbers']   ?? true,  FILTER_VALIDATE_BOOLEAN),
            'symbols'         => filter_var($params['includeSymbols']   ?? true,  FILTER_VALIDATE_BOOLEAN),
            'avoid_ambiguous' => filter_var($params['excludeAmbiguous'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'exclude'         => $params['exclude'] ?? '',
            'require_each'    => true,
        ]);
    }

    public function generateMultiple(array $params): array
    {
        $count  = (int) ($params['count']  ?? 1);
        $length = (int) ($params['length'] ?? 16);
        if ($count < $this->minCount) {
            throw new InvalidArgumentException("El número de contraseñas debe ser al menos {$this->minCount}");
        }

        if ($count > $this->maxCount) {
            throw new InvalidArgumentException("Máximo {$this->maxCount} contraseñas por petición");
        }

        $this->validateLength($length);

        return generate_passwords($count, $length, [
            'upper'           => filter_var($params['includeUppercase'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'lower'           => filter_var($params['includeLowercase'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'digits'          => filter_var($params['includeNumbers']   ?? true,  FILTER_VALIDATE_BOOLEAN),
            'symbols'         => filter_var($params['includeSymbols']   ?? true,  FILTER_VALIDATE_BOOLEAN),
            'avoid_ambiguous' => filter_var($params['excludeAmbiguous'] ?? true,  FILTER_VALIDATE_BOOLEAN),
            'exclude'         => $params['exclude'] ?? '',
            'require_each'    => true,
        ]);
    }

    private function validateLength(int $length): void
    {
        if ($length < $this->minLength || $length > $this->maxLength) {
            throw new InvalidArgumentException(
                "La longitud debe estar entre {$this->minLength} y {$this->maxLength}"
            );
        }
    }
}
