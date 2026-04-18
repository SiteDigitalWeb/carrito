<?php

namespace Sitedigitalweb\Carrito\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Sitedigitalweb\Carrito\Tenant\Cms_producto;
use Sitedigitalweb\Carrito\Cms_departamento;
use Sitedigitalweb\Carrito\Cms_order;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
     protected $tenantName = null;


public function __construct()
{
if(!session()->has('cart')) session()->has('cart', array());
$hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
        if ($hostname){
            $fqdn = $hostname->fqdn;
            $this->tenantName = explode(".", $fqdn)[0];
        }

}

    
    public function show()
    {
        $cart = Session::get('cart', []);
        $departamento = Cms_departamento::all();
        
        // Calcular totales
        $subtotal = 0;
        $total = 0;
        $iva = 0;
        $descuento = 0;
        
        foreach($cart as $item) {
            // Usar el precio correcto
            $price = isset($item->precioinivafin) && $item->precioinivafin > 0 
                ? $item->precioinivafin 
                : $item->precioivafin;
            
            $subtotal += $price * $item->quantity;
            $total += $price * $item->quantity;
        }
        
        return view('templates.cart', compact('cart', 'departamento', 'subtotal', 'total', 'iva', 'descuento'));
    }
    
    public function add($slug)
    {
        $producto = $this->getProductoBySlug($slug);
        
        if (!$producto) {
            return redirect()->back()->with('error', 'Producto no encontrado');
        }
        
        // Verificar stock
        if ($producto->stock <= 0) {
            return redirect()->back()->with('error', 'Producto sin stock disponible');
        }
        
        $cart = Session::get('cart', []);
        $currentQuantity = isset($cart[$producto->slug]) ? $cart[$producto->slug]->quantity : 0;
        
        // Verificar stock disponible
        if ($currentQuantity + 1 > $producto->stock) {
            return redirect()->back()->with('error', 'No hay suficiente stock disponible');
        }
        
        if (isset($cart[$producto->slug])) {
            $cart[$producto->slug]->quantity++;
            $message = 'Cantidad actualizada: ' . $producto->name;
        } else {
            // Crear objeto con todas las propiedades necesarias
            $cartItem = new \stdClass();
            $cartItem->id = $producto->id;
            $cartItem->slug = $producto->slug;
            $cartItem->name = $producto->name;
            $cartItem->description = $producto->description ?? '';
            $cartItem->contenido = $producto->contenido ?? '';
            $cartItem->image = $producto->image;
            $cartItem->imagea = $producto->imagea ?? '';
            $cartItem->imageb = $producto->imageb ?? '';
            $cartItem->imagec = $producto->imagec ?? '';
            $cartItem->imaged = $producto->imaged ?? '';
            $cartItem->imagee = $producto->imagee ?? '';
            $cartItem->stock = $producto->stock ?? 0;
            $cartItem->precio = $producto->precio ?? 0;
            $cartItem->preciodesc = $producto->preciodesc ?? 0;
            $cartItem->preciodescfin = $producto->preciodescfin ?? 0;
            $cartItem->precioiniva = $producto->precioiniva ?? 0;
            $cartItem->precioiva = $producto->precioiva ?? 0;
            $cartItem->precioivafin = $producto->precioivafin ?? 0;
            $cartItem->precioinivafin = $producto->precioinivafin ?? 0;
            $cartItem->descuento = $producto->descuento ?? 0;
            $cartItem->iva = $producto->iva ?? 0;
            $cartItem->visible = $producto->visible ?? 1;
            $cartItem->referencia = $producto->referencia ?? '';
            $cartItem->cms_categoria_id = $producto->cms_categoria_id ?? 0;
            $cartItem->cms_subcategoria_id = $producto->cms_subcategoria_id ?? 0;
            $cartItem->quantity = 1;
            
            $cart[$producto->slug] = $cartItem;
            $message = 'Producto agregado: ' . $producto->name;
        }
        
        Session::put('cart', $cart);
        
        return redirect('/cart/show')->with('success', $message);
    }
    
    public function update($slug, $quantity)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$slug])) {
            $producto = $this->getProductoBySlug($slug);
            
            // Validar stock
            if ($producto && $quantity > $producto->stock) {
                return redirect()->back()->with('error', 'No hay suficiente stock disponible');
            }
            
            $cart[$slug]->quantity = (int)$quantity;
            Session::put('cart', $cart);
        }
        
        return redirect('/cart/show')->with('success', 'Carrito actualizado');
    }
    
    public function delete($slug)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$slug])) {
            $productName = $cart[$slug]->name;
            unset($cart[$slug]);
            Session::put('cart', $cart);
            
            return redirect('/cart/show')->with('success', 'Producto eliminado: ' . $productName);
        }
        
        return redirect('/cart/show')->with('error', 'Producto no encontrado en el carrito');
    }
    
    public function trash()
    {
        Session::forget('cart');
        return redirect('/cart/show')->with('success', 'Carrito vaciado correctamente');
    }
    
    // Método AJAX para actualizar cantidad
    public function updateAjax($slug, $quantity)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$slug])) {
            $producto = $this->getProductoBySlug($slug);
            
            // Validar stock
            if ($producto && (int)$quantity > $producto->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficiente stock disponible. Stock máximo: ' . $producto->stock
                ]);
            }
            
            $cart[$slug]->quantity = (int)$quantity;
            Session::put('cart', $cart);
            
            // Calcular nuevo subtotal
            $price = isset($cart[$slug]->precioinivafin) && $cart[$slug]->precioinivafin > 0 
                ? $cart[$slug]->precioinivafin 
                : $cart[$slug]->precioivafin;
            
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
            $price = isset($item->precioinivafin) && $item->precioinivafin > 0 
                ? $item->precioinivafin 
                : $item->precioivafin;
            
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
            ? '\\Sitedigitalweb\\Carrito\\Tenant\\Cms_producto'
            : '\\Sitedigitalweb\\Carrito\\Tenant\\Cms_producto';
        
        return $modelClass::where('slug', $slug)->first();
    }

     public function getMunicipios($departamento_id)
    {
        $municipios = DB::table('cms_municipios')
            ->where('departamento_id', $departamento_id)
            ->where('estado', 1)
            ->select('id', 'municipio', 'p_municipio')
            ->get();
        
        return response()->json($municipios);
    }
    
    // Calcular envío
    public function calcularEnvio(Request $request)
    {
        $municipio_id = $request->input('municipio_id');
        $costo_envio = $request->input('costo_envio');
        $municipio_nombre = $request->input('municipio_nombre');
        $departamento_id = $request->input('departamento_id');
        
        // Guardar en sesión
        Session::put('preciomunicipio', $costo_envio);
        Session::put('nombremunicipio', $municipio_nombre);
        Session::put('departamento_id', $departamento_id);
        Session::put('municipio_id', $municipio_id);
        
        return response()->json([
            'success' => true,
            'costo_envio' => $costo_envio,
            'municipio_nombre' => $municipio_nombre
        ]);
    }
    
    // Validar cupón
    public function validarCupon(Request $request)
    {
        $codigo = $request->input('codigo');
        
        // Aquí tu lógica de validación de cupón
        $cupon = DB::table('cupones')->where('codigo', $codigo)->first();
        
        if($cupon) {
            Session::put('porcentaje', $cupon->descuento);
            Session::put('codigo', $codigo);
            
            return response()->json([
                'success' => true,
                'porcentaje' => $cupon->descuento
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Cupón inválido'
        ]);
    }


    public function orderDetail(){
 
if(!$this->tenantName){
$departamento = Cms_departamento::all();

$price = Cms_order::max('id');
$suma = $price + 1;
$whatsapp = Whatsapp::all();
$configuracion = Configuracion::find(1);
$meta = Page::where('slug','=','1')->get();
$plantilla = \DigitalsiteSaaS\Pagina\Template::all();
foreach ($plantilla as $plantillas) {
 $templateweb = $plantillas->template;
}
$menufoot = Page::orderBy('posta', 'asc')->get();
$plantillaes = \DigitalsiteSaaS\Pagina\Template::all();
$meta = \DigitalsiteSaaS\Pagina\Tenant\Page::where('slug','=','1')->get();
$menu = \DigitalsiteSaaS\Pagina\Page::whereNull('page_id')->orderBy('posta', 'asc')->get();
$cart = session()->get('cart');

$subtotal = $this->subtotal();
$iva = $this->iva();
$precioenvio = $this->precioenvio();
/*$datos = User::join('departamentos', 'departamentos.id', '=', 'users.ciudad')
             ->leftjoin('municipios', 'municipios.id', '=', 'users.region')
             ->where('users.id', '=' , Auth::user()->id)->get();
*/
$costoenvio = $this->costoenvio();
$preciomunicipio = $this->preciomunicipio();
$nombremunicipio = $this->nombremunicipio();
$descuento = $this->descuento();
/*$orderold  = Order::where('user_id', '=', Auth::user()->id)->get();*/
$categories = Pais::all();
/*$ordenes = Order::where('user_id', '=' ,Auth::user()->id)->where('estado', '=', 'PENDING')->get();*/
}else{
$departamento = \Sitedigitalweb\Carrito\Tenant\Cms_departamento::all();
$price = \Sitedigitalweb\Carrito\Tenant\Cms_order::max('id');
$suma = $price + 1;
$configuracion = \Sitedigitalweb\Carrito\Tenant\Cms_configuracion::where('id','=',1)->get();

$cart = session()->get('cart');






$orderold  = \Sitedigitalweb\Carrito\Tenant\Cms_order::where('user_id', '=', '1')->get();
$ordenes = \Sitedigitalweb\Carrito\Tenant\Cms_order::where('user_id', '=' ,'1')->where('estado', '=', 'PENDING')->get();


}

 


return view('Templates.order', compact('cart', 'configuracion','price','suma', 'departamento'));

}

}