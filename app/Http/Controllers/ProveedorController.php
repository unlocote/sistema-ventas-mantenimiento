<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\TipoId;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Proveedor::all(); // obtiene todos los productos
        $providers = Proveedor::with('tipoId')->get();
        return view('providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposId = TipoId::all();
        return view('providers.create', compact('tiposId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_id' => 'required|exists:tbl_tipo_id,id',
            'identification' => 'required|string|max:50|unique:tbl_proveedor,identification',
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'tipo_id.required' => 'Debe seleccionar un tipo de identificación.',
            'tipo_id.exists' => 'El tipo de identificación seleccionado no es válido.',
            'identification.required' => 'Debe ingresar una identificación.',
            'identification.unique' => 'Esta identificación ya está registrada.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
        ]);

        Proveedor::create($data);

        return redirect()->route('providers.index')->with('success', 'Proveedor creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $provider = Proveedor::find($id);
        return view('providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $provider = Proveedor::find($id);
        $tiposId = TipoId::all();
        return view('providers.edit', compact('provider', 'tiposId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $provider = Proveedor::find($id);
        $data = $request->validate([
            'tipo_id' => 'required|exists:tbl_tipo_id,id',
            'identification' => 'required|string|max:50|unique:tbl_proveedor,identification,' . $provider->id,
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'tipo_id.required' => 'Debe seleccionar un tipo de identificación.',
            'tipo_id.exists' => 'El tipo de identificación seleccionado no es válido.',
            'identification.required' => 'Debe ingresar una identificación.',
            'identification.unique' => 'Esta identificación ya está registrada.',
            'email.email' => 'Debe ingresar un correo electrónico válido.',
        ]);

        $provider->update($data);

        return redirect()->route('providers.index')->with('success', 'Proveedor ' . $provider->name . ' actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Proveedor::destroy($id);
        return redirect()->route('providers.index')->with('success', 'Proveedor eliminado correctamente.' );
    }
}
