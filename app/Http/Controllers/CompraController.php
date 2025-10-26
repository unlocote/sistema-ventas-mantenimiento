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
        // Validación principal
        $validated = $request->validate([
            'invoiceNumber' => 'required|string',
            'invoiceCreatedAt' => 'required|date',
            'proveedor_id' => 'required|exists:tbl_proveedor,id',
            'lotes' => 'required|array|min:1',
            'lotes.*.producto_id' => 'required|exists:tbl_producto,id',
            'lotes.*.brand' => 'required|string',
            'lotes.*.cantidad' => 'required|integer|min:1',
            'lotes.*.buyPrice' => 'required|numeric|min:0',
            'lotes.*.expirationDate' => 'nullable|date',
            'lotes.*.detalles' => 'nullable|array',
            'lotes.*.detalles.*.serial' => 'nullable|string',
            'lotes.*.suggestedRetailPrice' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Crear factura principal
            $purchase = FacturaCompra::create([
                'invoiceNumber' => $validated['invoiceNumber'],
                'invoiceCreatedAt' => $validated['invoiceCreatedAt'],
                'status' => FacturaCompra::ESTADO_PENDIENTE,
                'proveedor_id' => $validated['proveedor_id'],
                'empleado_id' => auth()->id() ?? null,
            ]);

            foreach ($validated['lotes'] as $loteData) {
                // Validar manualmente la fecha de expiración solo si existe
                if (!empty($loteData['expirationDate']) && $loteData['expirationDate'] < $validated['invoiceCreatedAt']) {
                    throw new \Exception(
                        "La fecha de expiración del producto {$loteData['producto_id']} no puede ser anterior a la fecha de la factura."
                    );
                }

                // Validar que suggestedRetailPrice sea mayor que buyPrice
                if ($loteData['suggestedRetailPrice'] < $loteData['buyPrice']) {
                    throw new \Exception(
                        "El precio sugerido de venta del producto {$loteData['producto_id']} no puede ser menor que el precio de compra."
                    );
                }

                // Validar seriales únicos
                if (!empty($loteData['detalles'])) {
                    $seriales = [];
                    foreach ($loteData['detalles'] as $detalle) {
                        $serial = trim($detalle['serial']);
                        if (!$serial) {
                            return back()->withInput()->withErrors([
                                'error' => "Todos los seriales del producto {$loteData['producto_id']} son obligatorios."
                            ]);
                        }
                        if (in_array($serial, $seriales)) {
                            return back()->withInput()->withErrors([
                                'error' => "Los seriales del producto {$loteData['producto_id']} no pueden repetirse."
                            ]);
                        }
                        $seriales[] = $serial;
                    }
                }

                $lote = $purchase->lotes()->create([
                    'producto_id' => $loteData['producto_id'],
                    'factura_compra_id' => $purchase->id,
                    'brand' => $loteData['brand'],
                    'initialQtty' => $loteData['cantidad'],
                    'currentQtty' => $loteData['cantidad'],
                    'buyPrice' => $loteData['buyPrice'],
                    'suggestedRetailPrice' => $loteData['suggestedRetailPrice'],
                    'expirationDate' => $loteData['expirationDate'] ?? null,
                ]);

                // Guardar detalles (seriales)
                if (!empty($loteData['detalles'])) {
                    foreach ($loteData['detalles'] as $detalle) {
                        if (!empty($detalle['serial'])) {
                            $lote->detallesLote()->create([
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
        // Traer la factura con los lotes y sus detalles
        $purchase = FacturaCompra::with('proveedor', 'lotes')->findOrFail($id);

        // Si los lotes tienen detalles como relación, se puede cargar también
        // Por simplicidad, asumimos que los detalles están guardados en cada lote como array JSON
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchase = FacturaCompra::with('lotes.detallesLote', 'proveedor')->findOrFail($id);
        $proveedores = Proveedor::all();
        $productos = Producto::all();

        return view('purchases.edit', compact('purchase', 'proveedores', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'invoiceCreatedAt' => 'required|date',
            'proveedor_id' => 'required|exists:tbl_proveedor,id',
            'lotes' => 'required|array|min:1',
            'lotes.*.producto_id' => 'required|exists:tbl_producto,id',
            'lotes.*.brand' => 'required|string',
            'lotes.*.cantidad' => 'required|integer|min:1',
            'lotes.*.buyPrice' => 'required|numeric|min:0',
            'lotes.*.suggestedRetailPrice' => 'required|numeric|min:0',
            'lotes.*.expirationDate' => 'nullable|date',
            'lotes.*.detalles' => 'nullable|array',
            'lotes.*.detalles.*.serial' => 'nullable|string',
        ]);

        $purchase = FacturaCompra::with('lotes.detallesLote')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Actualizar información de la factura
            $purchase->update([
                'invoiceCreatedAt' => $validated['invoiceCreatedAt'],
                'proveedor_id' => $validated['proveedor_id'],
            ]);

            // Eliminar lotes que ya no están en el formulario
            $idsLotesForm = array_filter(array_column($validated['lotes'], 'id') ?? []);
            foreach ($purchase->lotes as $loteExistente) {
                if (!in_array($loteExistente->id, $idsLotesForm)) {
                    $loteExistente->detallesLote()->delete();
                    $loteExistente->delete();
                }
            }

            // Crear o actualizar lotes
            foreach ($validated['lotes'] as $loteData) {
                if (!empty($loteData['expirationDate']) && $loteData['expirationDate'] < $validated['invoiceCreatedAt']) {
                    throw new \Exception("La fecha de expiración del producto {$loteData['producto_id']} no puede ser anterior a la fecha de la factura.");
                }

                if ($loteData['suggestedRetailPrice'] < $loteData['buyPrice']) {
                    throw new \Exception(
                        "El precio sugerido de venta del producto {$loteData['producto_id']} no puede ser menor que el precio de compra."
                    );
                }

                if (!empty($loteData['id'])) {
                    // Actualizar lote existente
                    $lote = Lote::findOrFail($loteData['id']);
                    $lote->update([
                        'producto_id' => $loteData['producto_id'],
                        'brand' => $loteData['brand'],
                        'initialQtty' => $loteData['cantidad'],
                        'currentQtty' => $loteData['cantidad'], 
                        'buyPrice' => $loteData['buyPrice'],
                        'suggestedRetailPrice' => $loteData['suggestedRetailPrice'],
                        'expirationDate' => $loteData['expirationDate'] ?? null,
                    ]);
                    // Eliminar detalles existentes y volver a crear
                    $lote->detallesLote()->delete();
                } else {
                    // Crear nuevo lote
                    $lote = $purchase->lotes()->create([
                        'producto_id' => $loteData['producto_id'],
                        'brand' => $loteData['brand'],
                        'initialQtty' => $loteData['cantidad'],
                        'currentQtty' => $loteData['cantidad'],
                        'buyPrice' => $loteData['buyPrice'],
                        'suggestedRetailPrice' => $loteData['suggestedRetailPrice'], 
                        'expirationDate' => $loteData['expirationDate'] ?? null,
                    ]);
                }

                // Guardar detalles (seriales)
                if (!empty($loteData['detalles'])) {
                    foreach ($loteData['detalles'] as $detalle) {
                        if (!empty($detalle['serial'])) {
                            $lote->detallesLote()->create(['serial' => $detalle['serial']]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('purchases.show', $purchase->id)->with('success', 'Factura actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al actualizar la factura: ' . $e->getMessage()]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $factura = FacturaCompra::with('lotes.detallesLote')->findOrFail($id);

        // Eliminar primero los detalles de cada lote
        foreach ($factura->lotes as $lote) {
            $lote->detallesLote()->delete();
        }

        // Luego eliminar los lotes
        $factura->lotes()->delete();

        // Finalmente eliminar la factura
        $factura->delete();

        return redirect()->route('purchases.index')->with('success', 'Factura eliminada correctamente.');
    }
}
