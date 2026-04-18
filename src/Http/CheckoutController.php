<?php

namespace Sitedigitalweb\Carrito\Http;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\CmsOrder;
use App\Models\CmsOrderItem;
use App\Models\CmsConfiguracionOnline;
use App\Models\CmsProductoOnline;

class CheckoutController extends Controller
{
    protected $tenantName;
    protected $configuracion;
    
    public function __construct()
    {
        // Definir tenantName (ajusta según tu lógica)
        $this->tenantName = config('tenant.name', null);
        
        try {
            $this->configuracion = CmsConfiguracionOnline::first();
        } catch (\Exception $e) {
            $this->configuracion = null;
        }
    }
    
    public function index()
    {
        $departamento = \App\Models\CmsDepartamento::all();
        $plantilla = \App\Models\CmsPlantilla::all();
        
        return view('Templates.checkout', compact('departamento', 'plantilla'));
    }
    
    public function saveStep1(Request $request)
    {
        try {
            Session::put('nombres', $request->nombres);
            Session::put('apellidos', $request->apellidos);
            Session::put('tipo_documento', $request->tipo_documento);
            Session::put('numero_documento', $request->numero_documento);
            Session::put('direccion_envio', $request->direccion_envio);
            Session::put('tipo_inmueble', $request->tipo_inmueble);
            Session::put('info_inmueble', $request->info_inmueble);
            Session::put('telefono', $request->telefono);
            Session::put('email', $request->email);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function saveStep2(Request $request)
    {
        try {
            Session::put('tipo_facturacion', $request->tipo_facturacion);
            
            if ($request->tipo_facturacion === 'persona') {
                Session::put('fact_nombres', $request->fact_nombres);
                Session::put('fact_apellidos', $request->fact_apellidos);
                Session::put('fact_tipo_documento', $request->fact_tipo_documento);
                Session::put('fact_numero_documento', $request->fact_numero_documento);
                Session::put('fact_direccion', $request->fact_direccion);
            } else {
                Session::put('fact_razon_social', $request->fact_razon_social);
                Session::put('fact_nit', $request->fact_nit);
                Session::put('fact_contacto', $request->fact_contacto);
                Session::put('fact_telefono', $request->fact_telefono);
                Session::put('fact_direccion_empresa', $request->fact_direccion_empresa);
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function createOrder(Request $request)
{
    try {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Carrito vacío']);
        }
        
        $productModel = CmsProductoOnline::class;
        
        // Calcular totales
        $subtotal = 0; // Subtotal SIN descuento (precios originales)
        $totalDescuento = 0; // Total de descuentos aplicados
        $items = [];
        
        foreach($cart as $slug => $item) {
            $productoReal = $productModel::where('slug', $slug)->first();
            
            if (!$productoReal) {
                \Log::error('Producto no encontrado en BD por slug:', ['slug' => $slug]);
                continue;
            }
            
            // Precio base del producto (sin IVA)
            $precioBase = isset($item->precio) && $item->precio > 0 
                ? $item->precio 
                : ($productoReal->precio ?? 0);
            $cantidad = isset($item->quantity) ? $item->quantity : 1;
            
            // Calcular descuento del producto
            $descuentoProducto = 0;
            if(isset($item->descuento) && $item->descuento > 0) {
                $descuentoProducto = ($precioBase * $item->descuento / 100) * $cantidad;
                $totalDescuento += $descuentoProducto;
            }
            
            $subtotal += $precioBase * $cantidad;
            
            $items[] = [
                'product_id' => $productoReal->id,
                'price' => $precioBase,
                'quantity' => $cantidad,
                'descuento' => $item->descuento ?? 0,
                'name' => $productoReal->name,
                'slug' => $slug
            ];
        }
        
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No hay productos válidos en el carrito']);
        }
        
        // Cálculos CORRECTOS
        $subtotalConDescuento = $subtotal - $totalDescuento; // 110,000
        $iva = $subtotalConDescuento * 0.19; // 20,900
        $costoEnvio = Session::get('preciomunicipio', 5000); // 5,000
        $totalFinal = $subtotalConDescuento + $iva + $costoEnvio; // 135,900
        
        // Log para depuración
        \Log::info('Cálculos createOrder:', [
            'subtotal_original' => $subtotal,
            'total_descuento' => $totalDescuento,
            'subtotal_con_descuento' => $subtotalConDescuento,
            'iva' => $iva,
            'costo_envio' => $costoEnvio,
            'total_final' => $totalFinal
        ]);
        
        // Generar códigos únicos
        $codigo = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        $codigoApr = 'APR-' . rand(100000, 999999);
        
        // Crear la orden
        $order = CmsOrder::create([
            'descripcion' => 'Compra de productos',
            'cantidad' => count($cart),
            'subtotal' => $subtotalConDescuento, // Guardar subtotal CON descuento
            'shipping' => $costoEnvio,
            'iva_ord' => $iva,
            'preciodescuento' => $totalDescuento,
            'cos_envio' => $costoEnvio,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'codigo' => $codigo,
            'codigo_apr' => $codigoApr,
            'medio' => 'epayco',
            'estado' => 'pendiente',
            'user_id' => auth()->id() ?? 1,
            'nombre' => Session::get('nombres'),
            'apellido' => Session::get('apellidos'),
            'documento' => Session::get('numero_documento'),
            'direccion' => Session::get('direccion_envio'),
            'inmueble' => Session::get('tipo_inmueble'),
            'informacion' => Session::get('info_inmueble'),
            'telefono' => Session::get('telefono'),
            'email' => Session::get('email'),
            'departamento' => Session::get('nombredepartamento'),
            'ciudad' => Session::get('nombremunicipio'),
            'tipo_facturacion' => Session::get('tipo_facturacion'),
            'fact_nombres' => Session::get('fact_nombres'),
            'fact_apellidos' => Session::get('fact_apellidos'),
            'fact_tipo_documento' => Session::get('fact_tipo_documento'),
            'fact_numero_documento' => Session::get('fact_numero_documento'),
            'fact_direccion' => Session::get('fact_direccion'),
        ]);
        
        // Guardar los items de la orden
        foreach($items as $item) {
            CmsOrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'user_id' => auth()->id() ?? 1
            ]);
        }
        
        \Log::info('Orden creada con items', [
            'order_id' => $order->id,
            'items_count' => count($items),
            'total_final' => $totalFinal
        ]);
        
        // Guardar en sesión
        Session::put('order_id', $order->id);
        Session::put('order_codigo', $order->codigo);
        Session::put('order_codigo_apr', $order->codigo_apr);
        
        return response()->json([
            'success' => true,
            'order' => [
                'id' => (int)$order->id,
                'codigo' => (string)$order->codigo,
                'total_final' => (int)$totalFinal, // 135900
                'subtotal' => (int)$subtotalConDescuento, // 110000
                'iva' => (int)$iva, // 20900
                'costo_envio' => (int)$costoEnvio, // 5000
                'nombre' => (string)Session::get('nombres'),
                'apellido' => (string)Session::get('apellidos'),
                'email' => (string)Session::get('email'),
                'telefono' => (string)Session::get('telefono'),
                'direccion' => (string)Session::get('direccion_envio'),
            ]
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error al crear orden: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Error al crear la orden: ' . $e->getMessage()
        ]);
    }
}
    
    public function epaycoConfig()
    {
        try {
            $config = CmsConfiguracionOnline::first();
            
            $public_key = '88ea6c24a94ca752cf5dcedfdaf0e657';
            
            if ($config && $config->public_key) {
                $public_key = $config->public_key;
            }
            
            $test_mode = ($public_key == 'test' || $public_key == '88ea6c24a94ca752cf5dcedfdaf0e657');
            
            return response()->json([
                'public_key' => $public_key,
                'url_response' => url('/epayco/response'),
                'url_confirmation' => url('/epayco/confirmation'),
                'test_mode' => $test_mode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'public_key' => '88ea6c24a94ca752cf5dcedfdaf0e657',
                'url_response' => url('/epayco/response'),
                'url_confirmation' => url('/epayco/confirmation'),
                'test_mode' => true
            ]);
        }
    }
    
    public function epaycoResponse(Request $request)
    {
        \Log::info('Respuesta ePayco (Response):', $request->all());
        
        $x_ref_payco = $request->input('x_ref_payco') ?? $request->query('x_ref_payco');
        $x_transaction_state = $request->input('x_transaction_state') ?? $request->query('x_transaction_state');
        $x_extra1 = $request->input('x_extra1') ?? $request->query('x_extra1');
        
        if ($x_transaction_state == 'Aceptada') {
            Session::put('transaction_state', 'completado');
            Session::put('ref_payco', $x_ref_payco);
            
            if ($x_extra1) {
                $order = CmsOrder::find($x_extra1);
                if ($order && $order->estado != 'completado') {
                    $order->estado = 'completado';
                    $order->ref_payco = $x_ref_payco;
                    $order->save();
                }
            }
            
            Session::forget('cart');
            
            return redirect()->route('order.show')->with('success', '¡Pago exitoso! Tu pedido ha sido confirmado.');
        } elseif ($x_transaction_state == 'Rechazada') {
            return redirect()->route('checkout')->with('error', 'El pago fue rechazado. Por favor intenta nuevamente.');
        } elseif ($x_transaction_state == 'Pendiente') {
            return redirect()->route('order.show')->with('warning', 'El pago está pendiente de confirmación.');
        } else {
            return redirect()->route('checkout')->with('error', 'Error al procesar el pago.');
        }
    }
    
    public function epaycoConfirmation(Request $request)
    {
        \Log::info('Confirmación ePayco (Webhook):', $request->all());
        
        $x_ref_payco = $request->input('x_ref_payco');
        $x_transaction_state = $request->input('x_transaction_state');
        $x_amount = $request->input('x_amount');
        $x_extra1 = $request->input('x_extra1');
        $x_signature = $request->input('x_signature');
        
        $p_cust_id_cliente = '1578383';
        $p_key = 'bb28dcee8e250983f5471bf58362d6fd8cc79b7c';
        
        $signature = hash('sha256', $p_cust_id_cliente . '^' . $p_key . '^' . $x_ref_payco . '^' . $x_extra1 . '^' . $x_amount . '^' . 'COP');
        
        if ($signature == $x_signature) {
            if ($x_transaction_state == 'Aceptada') {
                $order = CmsOrder::find($x_extra1);
                if ($order) {
                    $order->estado = 'completado';
                    $order->ref_payco = $x_ref_payco;
                    $order->save();
                    \Log::info('Orden #' . $order->id . ' actualizada a completado');
                }
            }
            return response()->json(['success' => true]);
        } else {
            \Log::error('Firma inválida en confirmación ePayco');
            return response()->json(['success' => false], 400);
        }
    }
}