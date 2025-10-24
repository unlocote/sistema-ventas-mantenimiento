<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Empleado;
use App\Models\Contrato;
use App\Models\Cargo;
use App\Models\TipoId;


class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Empleado::with(['tipoId'])->withCount('contratos')->get(); // obtiene todos los empleados con el tipoId y los contratos
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposId = TipoId::all();
        $positions = Cargo::all(); // para contrato inicial (opcional)
        return view('employees.create', compact('tiposId', 'positions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Empleado::find($id);
        $tiposId = TipoId::all();
        $positions = Cargo::all();
        return view('employees.edit', compact('employee', 'tiposId', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) { 
        $data = $request->validate([ 
            'tipo_id' => 'required|exists:tbl_tipo_id,id', 
            'identification' => 'required|string|max:50|unique:tbl_empleado,identification', 
            'name' => 'required|string|max:255', 
            'phoneNumber' => 'required|numeric|min:0', 
            'address' => 'required|string|max:255', 
            'email' => 'required|email|max:255', 
            'username' => 'required|string|max:100|unique:tbl_empleado,username', 
            'password' => 'required|string|min:6', 
            // Validación de contratos (opcional pero recomendable) 
            'contratos' => 'nullable|array', 
            'contratos.*.cargo_id' => 
            'required_with:contratos|exists:tbl_cargo,id', 
            'contratos.*.start_date' => 'required_with:contratos|date', 
            'contratos.*.end_date' => 'nullable|date|after_or_equal:contratos.*.start_date', 
            'contratos.*.document' => 'file|mimes:pdf,jpg,jpeg,png|max:40960', 
        ], 
        [ 
            'name.required' => 'El nombre es obligatorio.', 
            'tipo_id.required' => 'Debe seleccionar un tipo de identificación.', 
            'tipo_id.exists' => 'El tipo de identificación seleccionado no es válido.', 
            'identification.required' => 'Debe ingresar una identificación.', 
            'identification.unique' => 'Esta identificación ya está registrada.', 
            'email.email' => 'Debe ingresar un correo electrónico válido.', 
            'username.required' => 'Debe ingresar un nombre de usuario.', 
            'username.unique' => 'Este nombre de usuario ya está registrado.', 
            'password.required' => 'Debe ingresar una contraseña.', 
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.', 
            'contratos.*.cargo_id.required_with' => 'Debe seleccionar un cargo para cada contrato.', 
            'contratos.*.start_date.required_with' => 'Debe indicar la fecha de inicio.', 
            'contratos.*.document.max' => 'El archivo del contrato no debe superar los 40 MB.', 
        ]); 
        try { \DB::beginTransaction(); // Normalizar algunos datos 
            $data['email'] = strtolower(trim($data['email'])); 
            $data['passwordHash'] = Hash::make($data['password']); 
            unset($data['password']); // Crear empleado 
            $empleado = Empleado::create($data); // Guardar contratos (si existen) 
            if ($request->has('contratos')) { 
                foreach ($request->contratos as $i => $contratoData) { 
                    $documentPath = null; 
                    if ($request->hasFile("contratos.$i.document")) { // Leer el archivo como binario 
                    $file = $request->file("contratos.$i.document"); // Guarda el archivo en storage/app/public/contratos 
                    $documentPath = $file->store('contratos', 'public'); } 
                    $empleado->contratos()->create([ 
                        'cargo_id' => $contratoData['cargo_id'], 
                        'empleado_id' => $empleado->id, 
                        'start_date' => $contratoData['start_date'], 
                        'end_date' => $contratoData['end_date'] ?? null, 
                        'creation_date' => $contratoData['creation_date'] ?? now()->format('Y-m-d'), 
                        'document' => $documentPath, // Ruta del archivo
                    ]); 
                } 
            } 
            \DB::commit(); 
            return redirect()->route('employees.index')
            ->with('success', 'Empleado y contratos creados correctamente.');
         } catch (\Throwable $e) { 
            \DB::rollBack(); 
            return back()
            ->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()])->withInput(); 
        } 
    }

    public function update(Request $request, string $id)
    {
        $empleado = Empleado::findOrFail($id);

        $data = $request->validate([
            'tipo_id' => 'required|exists:tbl_tipo_id,id',
            'identification' => "required|string|max:50|unique:tbl_empleado,identification,{$empleado->id}",
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'username' => "required|string|max:100|unique:tbl_empleado,username,{$empleado->id}",
        ]);

        try {
            \DB::beginTransaction();

            // Normalizar datos
            $data['email'] = strtolower(trim($data['email']));

            // Actualizar empleado
            $empleado->update($data);

            /**
             * 1️⃣ Eliminar contratos marcados desde el formulario
             */
            if ($request->filled('eliminar_contratos')) {
                $empleado->contratos()
                    ->whereIn('id', $request->input('eliminar_contratos'))
                    ->delete();
            }

            /**
             * 2️⃣ Actualizar contratos existentes (que no fueron eliminados)
             *    Se actualizan a partir de las filas visibles en la tabla.
             */
            foreach ($empleado->contratos as $contrato) {
                // Buscar si hay datos en el request que coincidan con este contrato
                $cargoId = $request->input("contratos_existentes.{$contrato->id}.cargo_id");
                $startDate = $request->input("contratos_existentes.{$contrato->id}.start_date");
                $endDate = $request->input("contratos_existentes.{$contrato->id}.end_date");
                $fileInput = "contratos_files.{$contrato->id}";

                if ($cargoId && $startDate) {
                    $documentPath = $contrato->document;

                    // Si se cargó un nuevo archivo
                    if ($request->hasFile($fileInput)) {
                        $file = $request->file($fileInput);
                        $documentPath = $file->store('contratos', 'public');
                    }

                    $contrato->update([
                        'cargo_id' => $cargoId,
                        'start_date' => $startDate,
                        'end_date' => $endDate ?: null,
                        'document' => $documentPath,
                    ]);
                }
            }

            /**
             * 3️⃣ Crear nuevos contratos (enviados como contratos_nuevos[])
             */
            if ($request->has('contratos_nuevos')) {
                foreach ($request->input('contratos_nuevos') as $key => $contratoData) {
                    if (empty($contratoData['cargo_id']) || empty($contratoData['start_date'])) {
                        continue;
                    }

                    $documentPath = null;
                    if ($request->hasFile("contratos_files.$key")) {
                        $file = $request->file("contratos_files.$key");
                        $documentPath = $file->store('contratos', 'public');
                    }

                    $empleado->contratos()->create([
                        'cargo_id'      => $contratoData['cargo_id'],
                        'empleado_id'   => $empleado->id,
                        'start_date'    => $contratoData['start_date'],
                        'end_date'      => $contratoData['end_date'] ?? null,
                        'creation_date' => now()->format('Y-m-d'),
                        'document'      => $documentPath,
                    ]);
                }
            }

            \DB::commit();
            return redirect()->route('employees.index')->with('success', 'Empleado y contratos actualizados correctamente.');

        } catch (\Throwable $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Empleado::find($id);
        $employee->load('tipoId', 'contratos.cargo');
        return view('employees.show', compact('employee'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Empleado::find($id);
        if ($employee->contratos()->exists()) {
            return back()->with('error', 'No puedes eliminar un empleado con contratos.');
        }

        Empleado::destroy($id);
        return redirect()->route('employees.index')->with('success', 'Empleado eliminado correctamente.' );
    }

    public function download(string $id) 
    {
        $contrato = Contrato::findOrFail($id);

        if (!$contrato->document || !Storage::disk('public')->exists($contrato->document)) {
            abort(Response::HTTP_NOT_FOUND, 'El archivo no existe o fue eliminado.');
        }

        // Descarga directa desde el disco "public"
        return Storage::disk('public')->download($contrato->document, basename($contrato->document));
    }
}
