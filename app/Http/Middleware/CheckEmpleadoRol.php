<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class CheckEmpleadoRol
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$rolesRequeridos
     */
    public function handle(Request $request, Closure $next, ...$rolesRequeridos): Response
    {
        $empleado = auth('empleado')->user();

        if (!$empleado) {
            abort(403, 'No autenticado.');
        }

        $hoy = Carbon::today();

        // Filtramos solo contratos activos
        $contratosActivos = $empleado->contratos->filter(function ($contrato) use ($hoy) {
            return is_null($contrato->end_date) || Carbon::parse($contrato->end_date)->greaterThanOrEqualTo($hoy);
        });

        // Obtenemos los roles de los contratos activos
        $rolesEmpleado = $contratosActivos
            ->flatMap(function ($contrato) {
                return optional($contrato->cargo)->roles->pluck('name') ?? collect();
            })
            ->unique()
            ->toArray();

        // Verificamos si el empleado tiene alguno de los roles requeridos
        $tienePermiso = collect($rolesRequeridos)->some(function ($rol) use ($rolesEmpleado) {
            return in_array($rol, $rolesEmpleado);
        });

        if (!$tienePermiso) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
