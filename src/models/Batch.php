<?php
class Batch
{
    public ?int $id;
    public ?int $lote;
    public ?int $bis;
    public ?string $titulo;
    public ?string $descripcion;
    public ?string $medidas;
    public ?string $tipo;
    public ?int $nronoche;
    public ?float $preciominimo;
    public ?float $preciomaximo;
    public ?float $preciofijo;
    public ?float $precioestimativo;
    public ?string $precioestimativomaximo;
    public ?int $subasta;
    public ?string $moneda;
    public ?int $categoria;
    public ?string $autor = "Anonimo";
    public ?string $escuela;
    public ?string $tecnica;
    public ?int $home;
    public ?int $status;
    public ?int $ordenhome;
    public ?int $mandante;
    public ?int $barra;
    public ?string $hora_fin;
    public ?float $precioaumento;

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