<?php
/**
 * Validator - Validation de formulaires
 * Messages en français
 */
class Validator {
    private array $errors = [];
    private array $data = [];
    
    public function __construct(array $data) {
        $this->data = $data;
    }
    
    public function required(string $field, string $label = null): self {
        $label = $label ?? $field;
        if (empty($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = "Le champ {$label} est requis.";
        }
        return $this;
    }
    
    public function email(string $field, string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Le champ {$label} doit être une adresse email valide.";
        }
        return $this;
    }
    
    public function min(string $field, int $length, string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = "Le champ {$label} doit contenir au moins {$length} caractères.";
        }
        return $this;
    }
    
    public function max(string $field, int $length, string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = "Le champ {$label} ne doit pas dépasser {$length} caractères.";
        }
        return $this;
    }
    
    public function match(string $field, string $otherField, string $label = null, string $otherLabel = null): self {
        $label = $label ?? $field;
        $otherLabel = $otherLabel ?? $otherField;
        if (($this->data[$field] ?? '') !== ($this->data[$otherField] ?? '')) {
            $this->errors[$field] = "Le champ {$label} ne correspond pas à {$otherLabel}.";
        }
        return $this;
    }
    
    public function numeric(string $field, string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = "Le champ {$label} doit être un nombre.";
        }
        return $this;
    }
    
    public function in(string $field, array $allowed, string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed)) {
            $this->errors[$field] = "La valeur sélectionnée pour {$label} est invalide.";
        }
        return $this;
    }
    
    public function date(string $field, string $format = 'Y-m-d', string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field])) {
            $d = DateTime::createFromFormat($format, $this->data[$field]);
            if (!$d || $d->format($format) !== $this->data[$field]) {
                $this->errors[$field] = "Le champ {$label} doit être une date valide.";
            }
        }
        return $this;
    }
    
    public function phone(string $field, string $label = null): self {
        $label = $label ?? $field;
        if (!empty($this->data[$field])) {
            $phone = preg_replace('/\s+/', '', $this->data[$field]);
            if (!preg_match('/^(\+212|0)[5-7][0-9]{8}$/', $phone)) {
                $this->errors[$field] = "Le champ {$label} doit être un numéro marocain valide.";
            }
        }
        return $this;
    }
    
    public function passes(): bool {
        return empty($this->errors);
    }
    
    public function fails(): bool {
        return !empty($this->errors);
    }
    
    public function errors(): array {
        return $this->errors;
    }
    
    public function firstError(): ?string {
        return $this->errors ? array_values($this->errors)[0] : null;
    }
}
