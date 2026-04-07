<?php

namespace Sitedigitalweb\Carrito\Http;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use DigitalsiteSaaS\Carrito\Product;
use DigitalsiteSaaS\Carrito\Autor;
use DigitalsiteSaaS\Carrito\Area;
use DigitalsiteSaaS\Carrito\Parametro;
use DigitalsiteSaaS\Carrito\Category;
use Input;
use Illuminate\Support\Str;
use DB;
use Session;
use Hyn\Tenancy\Models\Hostname;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;

class ProductoController extends Controller{

         protected $tenantName = null;

  public function __construct(){

  $hostname = app(\Hyn\Tenancy\Environment::class)->hostname();
        if ($hostname){
            $fqdn = $hostname->fqdn;
            $this->tenantName = explode(".", $fqdn)[0];
        }
    }
    
	public function index()
    {
    $products = Product::orderBy('id', 'desc')->paginate(5);
    dd($products);
    }    
    
   public function digitales($id)
{
    $subcategoria = $this->findSubcategoria($id);
    
    if (!$subcategoria) {
        abort(404, 'Subcategoría no encontrada');
    }
    
    $productos = $subcategoria->Products;
    
    return view('carrito::productos.index', compact('productos'));
}

private function findSubcategoria($id)
{
    $modelClass = $this->tenantName 
        ? \Sitedigitalweb\Carrito\Tenant\Cms_subcategoria::class 
        : Cms_subcategoria::class;
    
    return $modelClass::find($id);
}


  public function programacion($id){
  if(!$this->tenantName){   
  $productos = Programacion::find($id)->Products;
    }else{
    $productos = \DigitalsiteSaaS\Carrito\Tenant\Programacion::where('product_id','=',$id)->get();
    }
  return view('carrito::productos.rutas', compact('productos'));
  }

    public function crearprogramacion($id){
 
  return view('carrito::productos.crearprogramacion');
  }


    public function imagenes($id){
    if(!$this->tenantName){
        $paginas = \DigitalsiteSaaS\Pagina\Page::all();
        $autores = Autor::all();
        $categoria = Category::where('id', '=', $id )->get();
    
        $parametros = Parametro::all();
        }else{
        $paginas = \DigitalsiteSaaS\Pagina\Tenant\Page::all();
        $autores = \DigitalsiteSaaS\Carrito\Tenant\Autor::all();
        $categoria = \DigitalsiteSaaS\Carrito\Tenant\Category::where('id', '=', $id )->get();
        
        $parametros = \DigitalsiteSaaS\Carrito\Tenant\Parametro::all();
        }
    
    return view('carrito::productos.imagenes')->with('paginas', $paginas)->with('autores', $autores)->with('parametros', $parametros)->with('categoria', $categoria);
    }

	public function editarproducto($id){
    if(!$this->tenantName){   
    $autores = Autor::all();

    $parametros = Parametro::all();
    $pages = \DigitalsiteSaaS\Pagina\Page::all();
    if(\DigitalsiteSaaS\Pagina\Venta::where('id', '1')->value('comunidad') == 1)
    $productos = Product::join('autor', 'autor.id', '=', 'products.autor_id')
    ->leftJoin('parametro', 'parametro.id', '=', 'products.parametro_id')
    ->where('products.id', '=' ,$id)->get();
    else
    $productos = Product::join('autor', 'autor.id', '=', 'products.autor_id')
    ->where('products.id', '=' ,$id)->get();

    //$paginas =DB::table('pages')
      //      ->join('products', 'products.page_id', '=', 'pages.id')
        //    ->where('products.id', '=' ,$id)->get();
    }else{

     $autores = \DigitalsiteSaaS\Carrito\Tenant\Autor::all();

    $parametros = \DigitalsiteSaaS\Carrito\Tenant\Parametro::all();
    $pages = \DigitalsiteSaaS\Pagina\Tenant\Page::all();
    if(\DigitalsiteSaaS\Pagina\Tenant\Venta::where('id', '1')->value('comunidad') == 1)
    $productos = \DigitalsiteSaaS\Carrito\Tenant\Product::join('autor', 'autor.id', '=', 'products.autor_id')
    ->leftJoin('parametro', 'parametro.id', '=', 'products.parametro_id')
    ->where('products.id', '=' ,$id)->get();
    else
    $productos = Product::join('autor', 'autor.id', '=', 'products.autor_id')
    ->where('products.id', '=' ,$id)->get();

    //$paginas =DB::table('pages')
      //      ->join('products', 'products.page_id', '=', 'pages.id')
        //    ->where('products.id', '=' ,$id)->get();

    }     

     return view('carrito::productos.editar', compact('productos','pages','autores','pages','parametros'))->with('status', 'ok_update');

    }

     public function crear($id)
    {
        if(!$this->tenantName){
        $paginas = \Sitedigitalweb\Pagina\Page::all();
        $autores = Autor::all();
        $categoria = Category::where('id', '=', $id )->get();
    
        $parametros = Parametro::all();
        }else{
        $paginas = \Sitedigitalweb\Pagina\Tenant\Page::all();
        $autores = \Sitedigitalweb\Carrito\Tenant\Cms_autor::all();
        $categoria = \Sitedigitalweb\Carrito\Tenant\Cms_subcategoria::where('id', '=', $id )->get();
        $parametros = \Sitedigitalweb\Carrito\Tenant\Cms_parametro::all();
        }
        return view('carrito::productos.crear')->with('paginas', $paginas)->with('autores', $autores)->with('parametros', $parametros)->with('categoria', $categoria);
    }


    public function show()
{
    $categoria = $this->createProductoInstance();
    
    // Asignar valores básicos
    $categoria->name = Input::get('nombre');
    $categoria->slug = Str::slug($categoria->name);
    $categoria->description = Input::get('descripcion');
    $categoria->contenido = Input::get('contenido');
    $categoria->precio = Input::get('precio');
    $categoria->descuento = Input::get('descuento');
    $categoria->iva = Input::get('iva');
    
    // Calcular precios
    $this->calculatePrices($categoria);
    
    // Asignar imágenes
    $this->assignImages($categoria);
    
    // Asignar el resto de campos
    $categoria->visible = Input::get('nivel');
    $categoria->cms_categoria_id = Input::get('peca');
    $categoria->position = Input::get('posicion');
    $categoria->stock = Input::get('stock');
    $categoria->responsive = Input::get('responsive');
    $categoria->facebook = Input::get('facebook');
    $categoria->pinterest = Input::get('pinterest');
    $categoria->youtube = Input::get('youtube');
    $categoria->instagram = Input::get('instagram');
    $categoria->animacion = Input::get('animacion');
    $categoria->ano = Input::get('ano');
    $categoria->referencia = Input::get('referencia');
    $categoria->parametro_id = Input::get('parametro');
    $categoria->autor_id = Input::get('autor');
    $categoria->cms_subcategoria_id = Input::get('categoriapro');
    
    $categoria->save();
    
    return redirect('/sd/productos/' . $categoria->cms_categoria_id)
        ->with('status', 'ok_create');
}

private function createProductoInstance()
{
    $modelClass = $this->tenantName 
        ? \Sitedigitalweb\Carrito\Tenant\Cms_producto::class 
        : Cms_producto::class;
    
    return new $modelClass;
}

private function calculatePrices($product)
{
    $product->preciodesc = $product->precio * $product->descuento / 100;
    $product->preciodescfin = $product->precio - $product->preciodesc;
    
    $product->precioiniva = $product->precio * $product->iva / 100;
    $product->precioiva = $product->preciodescfin * $product->iva / 100;
    $product->precioivafin = $product->precio + $product->precioiniva;
    $product->precioinivafin = $product->preciodescfin + $product->precioiva;
}

private function assignImages($product)
{
    $product->image = Input::get('FilePath');
    $product->imagea = Input::get('FilePatha');
    $product->imageb = Input::get('FilePathb');
    $product->imagec = Input::get('FilePathc');
    $product->imaged = Input::get('FilePathd');
    $product->imagee = Input::get('FilePathe');
}

        public function showruta(){
    if(!$this->tenantName){
    $categoria = new Programacion;
    }else{
    $categoria = new \DigitalsiteSaaS\Carrito\Tenant\Programacion;   
    }
    $categoria->fecha = Input::get('fecha');
    $categoria->cupos = Input::get('cupos');
    $categoria->product_id = Input::get('peca');
    $categoria->save();
       return Redirect('/gestion/productos/programacion/'.$categoria->product_id)->with('status', 'ok_create');
    }
 
    public function actualizar($id){
    $input = Input::all();
    if(!$this->tenantName){
    $categoria = Product::find($id);
    }else{
    $categoria = \DigitalsiteSaaS\Carrito\Tenant\Product::find($id);   
    }
    $categoria->name = Input::get('nombre');
    $categoria->slug = Str::slug($categoria->name);
    $categoria->description = Input::get('descripcion');
    $categoria->contenido = Input::get('contenido');
    $categoria->precio = Input::get('precio');
    $categoria->descuento = Input::get('descuento');
    $categoria->preciodesc = $categoria->precio*$categoria->descuento/100;
    $categoria->preciodescfin = $categoria->precio-$categoria->preciodesc; 
    $categoria->iva = Input::get('iva');
    $categoria->precioiniva = $categoria->precio*$categoria->iva/100;
    $categoria->precioiva = $categoria->preciodescfin*$categoria->iva/100;
    $categoria->precioivafin = $categoria->precio+$categoria->precioiniva;
    $categoria->precioinivafin = $categoria->preciodescfin+$categoria->precioiva;
    $categoria->image = Input::get('FilePath');
    $categoria->imagea = Input::get('FilePatha');
    $categoria->imageb = Input::get('FilePathb');
    $categoria->imagec = Input::get('FilePathc');
    $categoria->imaged = Input::get('FilePathd');
    $categoria->imagee = Input::get('FilePathe');
    $categoria->visible = Input::get('nivel');
    $categoria->position = Input::get('posicion');
    $categoria->stock = Input::get('stock');
    $categoria->responsive = Input::get('responsive');
    $categoria->facebook = Input::get('facebook');
    $categoria->pinterest = Input::get('pinterest');
    $categoria->youtube = Input::get('youtube');
    $categoria->instagram = Input::get('instagram');
    $categoria->animacion = Input::get('animacion');

    $categoria->ano = Input::get('ano');
    $categoria->referencia = Input::get('referencia');
    $categoria->parametro_id = Input::get('parametro');
    $categoria->autor_id = Input::get('autor');
    $categoria->categoriapro_id = Input::get('categoriapro');
    $categoria->save();
    return Redirect('sd/productos/'.$categoria->category_id)->with('status', 'ok_update');
    }


   public function eliminar($id){
    if(!$this->tenantName){
    $categoria = Product::find($id);
    }else{
    $categoria = \DigitalsiteSaaS\Carrito\Tenant\Product::find($id);    
    }
    $categoria->delete();
        
    return Redirect('/gestion/productos/digitales/'.$categoria->category_id)->with('status', 'ok_delete');
    }



}

