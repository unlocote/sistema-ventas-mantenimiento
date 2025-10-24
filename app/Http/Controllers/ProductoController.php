<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Producto::all(); // obtiene todos los productos
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:tbl_producto,name',
            'basePrice' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya se ha  registrado un producto con el mismo nombre.',
            'basePrice.required' => 'Debe ingresar un precio base.',
        ]);
        Producto::create($data);

        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Producto::find($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Producto::find($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Producto::find($id);
        $data = $request->validate([
            'name' => 'required|string|max:255|exists:tbl_producto,name',
            'basePrice' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya se ha  registrado un producto con el mismo nombre.',
            'basePrice.required' => 'Debe ingresar un precio base.',
        ]);
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Producte ' . $product->name . ' actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Producto::destroy($id);
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.' );
    }
}
