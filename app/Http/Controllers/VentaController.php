<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\FacturaVenta;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Lote;
use App\Models\TipoServicio;
use App\Models\DetalleLote;

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
        $lotes = Lote::with('producto')->get(); // importante: incluye la relación
        $tiposServicio = TipoServicio::all();


        $lastFactura = FacturaVenta::orderBy('id', 'desc')->first();
        $nextNumber = $lastFactura ? ((int) filter_var($lastFactura->invoiceNumber, FILTER_SANITIZE_NUMBER_INT) + 1) : 1;

        do {
            $nextInvoiceNumber = 'FC-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            $exists = FacturaVenta::where('invoiceNumber', $nextInvoiceNumber)->exists();
            if ($exists) {
                $nextNumber++; // Incrementamos y volvemos a probar
            }
        } while ($exists);


        // DetalleLotes no asociados a ninguna venta
        $detalleLotes = DetalleLote::whereDoesntHave('detalleVenta')->get();

        return view('sales.create', compact(
            'clientes',
            'productos',
            'lotes',
            'tiposServicio',
            'nextInvoiceNumber',
            'detalleLotes'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoiceNumber' => 'required|unique:tbl_factura_venta,invoiceNumber',
            'invoiceCreatedAt' => 'required|date',
            'cliente_id' => 'required|exists:tbl_cliente,id',
            'ventas' => 'required|string',
        ]);

        $ventasArray = json_decode($request->ventas, true);
        if (!$ventasArray || count($ventasArray) === 0) {
            return back()->withErrors(['ventas' => 'Debe agregar al menos un item a la factura.']);
        }

        DB::beginTransaction();

        try {
            // 1. Crear la factura
            $factura = new FacturaVenta([
                'invoiceNumber' => $request->invoiceNumber,
                'invoiceCreatedAt' => $request->invoiceCreatedAt,
                'cliente_id' => $request->cliente_id,
                'vendedor_id' => auth()->id(),
                'status' => FacturaVenta::ESTADO_PENDIENTE,
            ]);
            $factura->save();

            $tipoServicioSoporte = TipoServicio::where('name', 'Soporte')->firstOrFail();
            foreach ($ventasArray as $v) {
                if (isset($v['tipo']) && $v['tipo'] === "Servicio") {
                    // Si es servicio
                    $tipoServicioId = $v['servicio_id'];

                    $servicio = new Servicio([
                       'tipo_servicio_id' => $tipoServicioId,
                       'cliente_id' =>  $request->cliente_id,
                       'estado' => Servicio::ESTADO_CREADO,
                    ]);
                    $servicio->save();

                    $venta = new Venta([
                        'sellPrice' => $v['precio'],
                        'quantity' => 1,
                        'factura_venta_id' => $factura->id,
                        'servicio_id' => $servicio->id
                    ]);
                    $venta->save();
                    continue;
                }

                // Si es producto con lote
                if (isset($v['lote_id'])) {
                    $lote = Lote::findOrFail($v['lote_id']);

                    if ($lote->currentQtty < $v['cantidad']) {
                        DB::rollBack();
                        return back()->withErrors(['ventas' => "El lote {$lote->brand} no tiene suficiente cantidad."]);
                    }

                    // Crear Venta
                    $venta = new Venta([
                        'lote_id' => $v['lote_id'],
                        'factura_venta_id' => $factura->id,
                        'quantity' => $v['cantidad'],
                        'sellPrice' => $v['precio']
                    ]);
                    $venta->save();

                    // Reducir stock
                    $lote->currentQtty -= $v['cantidad'];
                    $lote->save();


                    // Crear DetalleVenta para cada detalleLote seleccionado
                    if (!empty($v['detalleLotes'])) {
                        foreach ($v['detalleLotes'] as $detalleLoteId) {

                            $servicio = new Servicio([
                                'tipo_servicio_id' => $tipoServicioSoporte->id,
                                'cliente_id' =>  $request->cliente_id,
                                'estado' => Servicio::ESTADO_CREADO,
                            ]);
                            $servicio->save();

                            $detalleLote = DetalleLote::findOrFail($detalleLoteId);
                            $detalleVenta = new DetalleVenta([
                                'venta_id' => $venta->id,
                                'detalle_lote_id' => $detalleLote->id,
                                'servicio_id' => $servicio->id
                            ]);
                            $detalleVenta->save();

                        }
                    }

                }
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
        // Traer la factura con cliente, vendedor, ventas y relaciones
        $factura = FacturaVenta::with([
            'cliente',
            'vendedor',
            'pagos',
            'ventas.lote.producto',
            'ventas.servicio.tipoServicio',
            'ventas.detallesVenta.detalleLote.lote'
        ])->findOrFail($id);

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
        DB::beginTransaction();

        try {
            $factura = FacturaVenta::with([
                'ventas.lote',
                'ventas.servicio',
                'ventas.detallesVenta.detalleLote',
                'ventas.detallesVenta.servicio'
            ])->findOrFail($id);

            // ✅ Revertir cantidades de los lotes y eliminar detalles asociados
            foreach ($factura->ventas as $venta) {

                // Si tiene lote → devolver stock
                if ($venta->lote) {
                    $venta->lote->currentQtty += $venta->quantity;
                    $venta->lote->save();
                }

                // Eliminar detalles de venta
                foreach ($venta->detallesVenta as $detalle) {
                    // Si hay servicio asociado al detalle, se elimina también
                    if ($detalle->servicio) {
                        $detalle->servicio->delete();
                    }

                    $detalle->delete();
                }

                // Si la venta tiene un servicio asociado (caso de servicios)
                if ($venta->servicio) {
                    $venta->servicio->delete();
                }

                // Eliminar la venta
                $venta->delete();
            }

            // Finalmente, eliminar la factura
            $factura->delete();

            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Factura eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la factura: ' . $e->getMessage()]);
        }
    }

}
