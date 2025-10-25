<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\FacturaCompra;
use App\Models\Lote;
use App\Models\DetalleLote;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FacturaCompra::with(['proveedor', 'lotes']);

        if ($search = $request->input('search')) {
            $query->whereHas('proveedor', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $purchases = $query->orderBy('invoiceCreatedAt', 'desc')->paginate(10);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener la última factura
        $lastFactura = FacturaCompra::orderBy('id', 'desc')->first();
        $nextNumber = $lastFactura ? ((int) filter_var($lastFactura->invoiceNumber, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;

        // Generar el número formateado
        $nextInvoiceNumber = 'FC-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Cargar todos los proveedores y productos para los modales
        $proveedores = Proveedor::all();
        $productos = Producto::all();
        return view('purchases.create', compact('proveedores', 'productos', 'nextInvoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'invoiceNumber' => 'required|string',
            'invoiceCreatedAt' => 'required|date',
            'proveedor_id' => 'required|exists:tbl_proveedor,id',
            'lotes' => 'required|array|min:1',
            'lotes.*.producto_id' => 'required|exists:tbl_producto,id',
            'lotes.*.brand' => 'required|string',
            'lotes.*.cantidad' => 'required|integer|min:1',
            'lotes.*.buyPrice' => 'required|numeric|min:0',
            'lotes.*.subtotalPrice' => 'required|numeric|min:0',
            'lotes.*.detalles' => 'nullable|array',
            'lotes.*.detalles.*.serial' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Crear la factura principal
            $purchase = FacturaCompra::create([
                'invoiceNumber' => $validated['invoiceNumber'],
                'invoiceCreatedAt' => $validated['invoiceCreatedAt'],
                'proveedor_id' => $validated['proveedor_id'],
            ]);

            // Guardar cada lote asociado
            foreach ($validated['lotes'] as $loteData) {
                $lote = $purchase->lotes()->create([
                    'producto_id' => $loteData['producto_id'],
                    'brand' => $loteData['brand'],
                    'cantidad' => $loteData['cantidad'],
                    'buyPrice' => $loteData['buyPrice'],
                    'subtotalPrice' => $loteData['subtotalPrice'],
                ]);

                // Guardar detalles (seriales)
                if (!empty($loteData['detalles'])) {
                    foreach ($loteData['detalles'] as $detalle) {
                        if (!empty($detalle['serial'])) {
                            $lote->detalles()->create([
                                'serial' => $detalle['serial'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('purchases.index')->with('success', 'Factura creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al guardar la factura: ' . $e->getMessage()]);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
