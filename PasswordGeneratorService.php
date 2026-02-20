class PasswordGeneratorService
{
    private int $minLength = 4;
    private int $maxLength = 128;
    private int $maxCount = 50;

    public function generate(array $params): string
    {
        $length = $params['length'] ?? 16;

        $this->validateLength($length);

        return generate_password($length, [
            'upper' => $params['includeUppercase'] ?? true,
            'lower' => $params['includeLowercase'] ?? true,
            'digits' => $params['includeNumbers'] ?? true,
            'symbols' => $params['includeSymbols'] ?? true,
            'avoid_ambiguous' => $params['excludeAmbiguous'] ?? true,
            'exclude' => $params['exclude'] ?? '',
            'require_each' => true
        ]);
    }

    public function generateMultiple(array $params): array
    {
        $count = $params['count'] ?? 1;
        $length = $params['length'] ?? 16;

        if ($count > $this->maxCount) {
            throw new InvalidArgumentException("Máximo {$this->maxCount} contraseñas por petición");
        }

        $this->validateLength($length);

        return generate_passwords($count, $length, [
            'upper' => $params['includeUppercase'] ?? true,
            'lower' => $params['includeLowercase'] ?? true,
            'digits' => $params['includeNumbers'] ?? true,
            'symbols' => $params['includeSymbols'] ?? true,
            'avoid_ambiguous' => $params['excludeAmbiguous'] ?? true,
            'exclude' => $params['exclude'] ?? '',
            'require_each' => true
        ]);
    }

    private function validateLength(int $length): void
    {
        if ($length < $this->minLength || $length > $this->maxLength) {
            throw new InvalidArgumentException("La longitud debe estar entre {$this->minLength} y {$this->maxLength}");
        }
    }
}
