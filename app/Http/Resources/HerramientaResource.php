<?php

namespace App\Http\Resources;

use App\Models\Herramienta;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Formato JSON de una herramienta en la API. */
class HerramientaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'categoria_etiqueta' => Herramienta::CATEGORIAS[$this->categoria] ?? 'Otros',
            'barrio' => $this->barrio,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            'disponible' => $this->disponible,
            'propietario' => $this->whenLoaded('propietario', fn () => $this->propietario->name),
            'distancia_km' => $this->when($this->distancia_km !== null, $this->distancia_km),
        ];
    }
}
