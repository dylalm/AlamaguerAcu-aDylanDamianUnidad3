<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Herramienta extends Model
{
    use HasFactory;

    /** Categorías disponibles: clave => etiqueta que ve el vecino. */
    public const CATEGORIAS = [
        'jardin' => 'Jardín',
        'construccion' => 'Construcción',
        'hogar' => 'Hogar',
        'cocina' => 'Cocina',
        'electricidad' => 'Electricidad',
        'otros' => 'Otros',
    ];

    protected $table = 'herramientas';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'categoria',
        'barrio',
        'latitud',
        'longitud',
        'disponible',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
            'disponible' => 'boolean',
        ];
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Busca por nombre o descripción sin distinguir mayúsculas. */
    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (blank($texto)) {
            return $query;
        }

        $patron = '%'.mb_strtolower(trim($texto)).'%';

        return $query->where(function (Builder $consulta) use ($patron) {
            $consulta->whereRaw('lower(nombre) like ?', [$patron])
                ->orWhereRaw('lower(descripcion) like ?', [$patron]);
        });
    }

    /** Distancia en kilómetros (fórmula de Haversine) hasta un punto de referencia. */
    public function distanciaKm(float $latitud, float $longitud): float
    {
        $radioTierra = 6371;
        $difLat = deg2rad($latitud - $this->latitud);
        $difLng = deg2rad($longitud - $this->longitud);

        $a = sin($difLat / 2) ** 2
            + cos(deg2rad($this->latitud)) * cos(deg2rad($latitud)) * sin($difLng / 2) ** 2;

        return round($radioTierra * 2 * atan2(sqrt($a), sqrt(1 - $a)), 1);
    }
}
