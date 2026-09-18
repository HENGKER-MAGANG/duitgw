<?php

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label): self
    {
        if (empty(trim((string) ($this->data[$field] ?? '')))) {
            $this->errors[$field] = "{$label} wajib diisi.";
        }
        return $this;
    }

    public function email(string $field, string $label): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "{$label} tidak valid.";
        }
        return $this;
    }

    public function min(string $field, int $length, string $label): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = "{$label} minimal {$length} karakter.";
        }
        return $this;
    }

    public function numeric(string $field, string $label): self
    {
        if (isset($this->data[$field]) && $this->data[$field] !== '' && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "{$label} harus berupa angka.";
        }
        return $this;
    }

    public function min_value(string $field, float $min, string $label): self
    {
        if (isset($this->data[$field]) && is_numeric($this->data[$field]) && (float) $this->data[$field] <= $min) {
            $this->errors[$field] = "{$label} harus lebih besar dari " . $min . ".";
        }
        return $this;
    }

    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
