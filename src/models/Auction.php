<?php

class Auction
{
    public ?int $id;
    public ?int $nro;
    public ?int $noches;
    public ?string $description;
    public ?string $fechainicio;
    public ?string $fechafin;
    public ?string $fechacarga;
    public ?int $status;
    public ?float $comision;
    public ?int $iva;
    public ?int $cerrada;
    public ?string $linkcatalogo3;
    public ?int $subasta_online;
    public ?int $show_home;
    public ?float $incremental;

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