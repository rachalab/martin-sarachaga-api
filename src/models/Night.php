<?php
class Night {
    public ?int $idSubasta;
    public ?int $noche;
    public ?string $dia;
    public ?string $horario;
    public ?string $descripcion;
    public ?int $mostrar;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}