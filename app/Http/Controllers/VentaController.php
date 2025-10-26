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
    public function index(Request $request)
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
        $clientes = Cliente::all();
        $productos = Producto::all();
        $lotes = Lote::with('producto')->get(); // ✅ importante: incluye la relación
        $lastInvoice = FacturaVenta::orderBy('id', 'desc')->first();
        $nextInvoiceNumber = $lastInvoice ? $lastInvoice->invoiceNumber + 1 : 1;

        return view('sales.create', compact('clientes', 'productos', 'lotes', 'nextInvoiceNumber'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoiceNumber' => 'required|unique:tbl_factura_venta,invoiceNumber',
            'invoiceCreatedAt' => 'required|date',
            'cliente_id' => 'required|exists:clientes,id',
            'ventas' => 'required|array|min:1',
            'ventas.*.producto_id' => 'required|exists:productos,id',
            'ventas.*.lote_id' => 'required|exists:tbl_lote,id',
            'ventas.*.quantity' => 'required|integer|min:1',
            'ventas.*.sellPrice' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Crear factura
            $factura = FacturaVenta::create([
                'invoiceNumber' => $request->invoiceNumber,
                'invoiceCreatedAt' => $request->invoiceCreatedAt,
                'cliente_id' => $request->cliente_id,
                'vendedor_id' => auth()->id(),
                'status' => FacturaVenta::ESTADO_PENDIENTE,
            ]);

            // Crear items
            foreach ($request->ventas as $v) {
                $venta = new Venta([
                    'producto_id' => $v['producto_id'],
                    'lote_id' => $v['lote_id'],
                    'quantity' => $v['quantity'],
                    'sellPrice' => $v['sellPrice'],
                ]);
                $venta->facturaVenta()->associate($factura);
                $venta->save();

                // Reducir stock del lote
                $lote = Lote::find($v['lote_id']);
                if ($lote->currentQtty < $v['quantity']) {
                    DB::rollBack();
                    return back()->withErrors(['ventas' => "El lote {$lote->brand} no tiene suficiente cantidad."]);
                }
                $lote->currentQtty -= $v['quantity'];
                $lote->save();
            }

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Factura de venta creada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
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
