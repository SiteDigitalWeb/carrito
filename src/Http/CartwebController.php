<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Cms_producto;
use App\Models\Cms_departamentos;

class CartwebController extends Controller
{
    protected $tenantName;
    
    public function __construct()
    {
        $this->tenantName = config('tenant.name', null);
    }
    
    public function show()
    {
        $cart = Session::get('cart', []);
        $departamento = Cms_departamentos::all();
        
        // Calcular totales
        $subtotal = 0;
        $total = 0;
        $iva = 0;
        $descuento = 0;
        
        foreach($cart as $item) {
            $price = isset($item->precioinivafin) ? $item->precioinivafin : $item->precioivafin;
            $subtotal += $price * $item->quantity;
            $total += $price * $item->quantity;
        }
        
        return view('carrito::admin.carrito', compact('cart', 'departamento', 'subtotal', 'total', 'iva', 'descuento'));
    }
    
    // Método AJAX para actualizar cantidad
    public function updateAjax($slug, $quantity)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$slug])) {
            $producto = $this->getProductoBySlug($slug);
            
            // Validar stock
            if ($producto && $quantity > $producto->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible. Stock máximo: ' . $producto->stock
                ]);
            }
            
            $cart[$slug]->quantity = $quantity;
            Session::put('cart', $cart);
            
            // Calcular nuevo subtotal
            $price = isset($cart[$slug]->precioinivafin) ? $cart[$slug]->precioinivafin : $cart[$slug]->precioivafin;
            $subtotal = $price * $quantity;
            
            return response()->json([
                'success' => true,
                'subtotal' => $subtotal,
                'cart' => $this->getCartSummary()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado en el carrito'
        ]);
    }
    
    // Método AJAX para eliminar producto
    public function deleteAjax($slug)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$slug])) {
            unset($cart[$slug]);
            Session::put('cart', $cart);
            
            return response()->json([
                'success' => true,
                'cart' => $this->getCartSummary()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado'
        ]);
    }
    
    // Método AJAX para vaciar carrito
    public function trashAjax()
    {
        Session::forget('cart');
        
        return response()->json([
            'success' => true,
            'cart' => $this->getCartSummary()
        ]);
    }
    
    // Obtener resumen del carrito
    private function getCartSummary()
    {
        $cart = Session::get('cart', []);
        $subtotal = 0;
        $total = 0;
        
        foreach($cart as $item) {
            $price = isset($item->precioinivafin) ? $item->precioinivafin : $item->precioivafin;
            $subtotal += $price * $item->quantity;
        }
        
        $total = $subtotal;
        
        return [
            'subtotal' => $subtotal,
            'total' => $total,
            'items_count' => count($cart),
            'items_quantity' => array_sum(array_column($cart, 'quantity'))
        ];
    }
    
    private function getProductoBySlug($slug)
    {
        $modelClass = $this->tenantName 
            ? \Sitedigitalweb\Carrito\Tenant\Cms_producto::class 
            : Cms_producto::class;
        
        return $modelClass::where('slug', $slug)->first();
    }
}