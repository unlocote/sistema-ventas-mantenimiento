<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\TipoId;


class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Cliente::all(); // obtiene todos los productos
        $clients = Cliente::with('tipoId')->get();
        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tiposId = TipoId::all();
        return view('clients.create', compact('tiposId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_id' => 'required|exists:tbl_tipo_id,id',
            'identification' => 'required|string|max:50|unique:tbl_cliente,identification',
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

        Cliente::create($data);

        return redirect()->route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $client = Cliente::find($id);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $client = Cliente::find($id);
        $tiposId = TipoId::all();
        return view('clients.edit', compact('client', 'tiposId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $client = Cliente::find($id);
        $data = $request->validate([
            'tipo_id' => 'required|exists:tbl_tipo_id,id',
            'identification' => 'required|string|max:50|unique:tbl_cliente,identification,' . $client->id,
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

        $client->update($data);

        return redirect()->route('clients.index')->with('success', 'Cliente ' . $client->name . ' actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Cliente::destroy($id);
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado correctamente.' );
    }
}
