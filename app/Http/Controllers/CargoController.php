<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use App\Models\Rol;

class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Cargo::with('roles')->get(); // obtiene todos los productos
        return view('positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Rol::all(); // Traemos todos los roles disponibles
        return view('positions.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'roles' => 'required|array|min:1', // ✅ Debe tener al menos un rol
            'roles.*' => 'exists:tbl_rol,id',
        ]);

        $position = Cargo::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $position->roles()->sync($validated['roles']); // ✅ Asignar roles al cargo

        return redirect()->route('positions.index')
                        ->with('success', 'El cargo se creó correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $position = Cargo::with('roles')->findOrFail($id); // obtiene pcargo por id
        return view('positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $position = Cargo::with('roles')->findOrFail($id); // obtiene pcargo por id
        $roles = Rol::all(); // trae todos los roles disponibles
        return view('positions.edit', compact('position', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $position = Cargo::with('roles')->findOrFail($id); // obtiene pcargo por id
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:tbl_rol,id',
        ]);

        $position->update($validated);
        $position->roles()->sync($validated['roles']);

        return redirect()->route('positions.index')
                        ->with('success', 'Cargo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $position = Cargo::with('roles')->findOrFail($id); // obtiene pcargo por id
        $position->roles()->detach();
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'El cargo se eliminó correctamente.');
    }
}
