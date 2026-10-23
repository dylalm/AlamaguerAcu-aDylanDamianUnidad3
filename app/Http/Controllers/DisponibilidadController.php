<?php

namespace App\Http\Controllers;

use App\Models\Herramienta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DisponibilidadController extends Controller
{
    /** Alterna una herramienta entre «disponible» y «prestada». Solo su dueño puede hacerlo. */
    public function __invoke(Request $request, Herramienta $herramienta): RedirectResponse
    {
        abort_unless($herramienta->user_id === $request->user()->id, 403);

        $herramienta->update(['disponible' => ! $herramienta->disponible]);

        return back();
    }
}
