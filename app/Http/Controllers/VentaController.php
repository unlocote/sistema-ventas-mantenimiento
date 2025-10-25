<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FacturaVenta;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Lote;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = FacturaVenta::with(['cliente', 'vendedor']);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->cliente . '%');
            });
        }

        $facturas = $query->orderBy('invoiceCreatedAt', 'desc')->paginate(10);

        return view('sales.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener la última factura
        $lastFactura = FacturaVenta::orderBy('id', 'desc')->first();
        $nextNumber = $lastFactura ? ((int) filter_var($lastFactura->invoiceNumber, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;

        // Generar el número formateado
        $nextInvoiceNumber = 'FC-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $clientes = Cliente::all();
        $vendedor = Auth::user()->empleado; // empleado asociado al usuario actual
        // Todos los lotes con cantidad > 0
        $lotes = Lote::where('currentQtty', '>', 0)->get();
        $productos = Producto::all();

        return view('sales.create', compact('clientes', 'vendedor', 'productos', 'lotes', 'nextInvoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoiceNumber' => 'required|string|max:50|unique:tbl_factura_venta,invoiceNumber',
            'invoiceCreatedAt' => 'required|date',
            'cliente_id' => 'required|exists:tbl_clientes,id',
            'ventas' => 'required|array|min:1',
            'sales.*.lote_id' => 'required|exists:tbl_lote,id',
            'sales.*.producto_id' => 'required|exists:tbl_producto,id',
            'sales.*.quantity' => 'required|numeric|min:1',
            'sales.*.sellPrice' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 1️⃣ Crear la factura
            $factura = FacturaVenta::create([
                'invoiceNumber' => $validated['invoiceNumber'],
                'invoiceCreatedAt' => $validated['invoiceCreatedAt'],
                'cliente_id' => $validated['cliente_id'],
                'vendedor_id' => Auth::user()->empleado->id,
                'status' => FacturaVenta::ESTADO_PENDIENTE,
            ]);

            // 2️⃣ Registrar los ítems de venta
            foreach ($validated['ventas'] as $ventaData) {
                $lote = Lote::find($ventaData['lote_id']);

                if ($lote->cantidad < $ventaData['quantity']) {
                    throw new \Exception("El lote {$lote->id} no tiene suficiente stock.");
                }

                Venta::create([
                    'factura_venta_id' => $factura->id,
                    'producto_id' => $ventaData['producto_id'],
                    'lote_id' => $ventaData['lote_id'],
                    'sellTime' => now(),
                    'quantity' => $ventaData['quantity'],
                    'sellPrice' => $ventaData['sellPrice'],
                ]);

                // 3️⃣ Actualizar inventario
                $lote->decrement('cantidad', $ventaData['quantity']);
            }

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Factura de venta registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $factura = FacturaVenta::with(['cliente', 'vendedor', 'pagos', 'sales.producto', 'sales.lote'])
            ->findOrFail($id);

        return view('sales.show', compact('factura'));
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
