@extends ('adminsite.layout')

@section('ContenidoSite-01')

<div class="content-header">
     <ul class="nav-horizontal text-center">
      <li>
      <a href="/gestion/carrito/dashboard"><i class="fa fa-keyboard-o"></i> Dashboard</a>
      </li>
      <li>
       <a href="/gestion/carrito"><i class="gi gi-parents"></i> Usuarios</a>
      </li>
      <li>
       <a href="/gestion/carrito/categorias"><i class="fa fa-th-list"></i> Categorias</a>
      </li>
      <li class="active">
       <a href="/gestion/carrito/epayco"><i class="fa fa-pencil-square-o"></i>Ordenes</a>
      </li>
      <li>
       <a href="/gestion/carrito/autores"><i class="fa fa-child"></i>Autores</a>
      </li>
      
      <li>
       <a href="/gestion/carrito/crearconfiguracion"><i class="fa fa-clipboard"></i>Configurar</a>
      </li>
      <li>
       <a href="/gestion/carrito/terminos"><i class="fa fa-clipboard"></i>Terminos y condiciones</a>
      </li>
     </ul>
    </div>

<div class="container">
  <?php $status=Session::get('status'); ?>
  @if($status=='ok_create')
   <div class="alert alert-success">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Usuario registrado con éxito</strong>
   </div>
  @endif

  @if($status=='ok_delete')
   <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Usuario eliminado con éxito</strong>
   </div>
  @endif

  @if($status=='ok_update')
   <div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Usuario actualizado con éxito</strong>
   </div>
  @endif
</div>

@if($configuracion && $configuracion->tienda == 'Cotizador')

@foreach($datos as $dato)
<div class="container">
  <div class="row text-center">
    <div class="col-sm-12 col-lg-6">
     <div class="widget">
      <div class="widget-extra themed-background-success">
       <h4 class="widget-content-light"><strong>ORD.{{$dato->id}}</strong></h4>
      </div>
     <div class="widget-extra-full"><span class="h4 text-success animation-expandOpen">{{$dato->fecha}}</span></div>
    </div>
  </div>

  @if($dato->estado == "Aceptada" OR $dato->estado == "APPROVED")
    <div class="col-sm-6 col-lg-6">
      <div class="widget">
       <div class="widget-extra themed-background-success">
        <h4 class="widget-content-light"><i class="fa fa-paypal"></i> <strong>Estado</strong></h4>
       </div>
      <div class="widget-extra-full"><span class="h4 text-success animation-expandOpen"> Aceptada</span></div>
     </div>
    </div>
    @elseif($dato->estado == "Pendiente" OR $dato->estado == "PENDING")
    <div class="col-sm-6 col-lg-6">
     <div class="widget">
      <div class="widget-extra themed-background-warning">
       <h4 class="widget-content-light"><i class="fa fa-paypal"></i> <strong>Estado</strong></h4>
      </div>
      <div class="widget-extra-full"><span class="h4 text-warning animation-expandOpen"> Pendiente</span></div>
     </div>
    </div>
    @elseif($dato->estado == "Rechazada" OR $dato->estado == "REJECTED")
    <div class="col-sm-6 col-lg-6">
     <div class="widget">
      <div class="widget-extra themed-background-danger">
       <h4 class="widget-content-light"><i class="fa fa-paypal"></i> <strong>Estado</strong></h4>
      </div>
     <div class="widget-extra-full"><span class="h4 text-danger animation-expandOpen"> Rechazada</span></div>
    </div>
    </div>
    @endif
  </div>
</div>
@endforeach

<div class="container">
 <div class="block">
  <div class="block-title">
   <h2><i class="fa fa-shopping-cart"></i> <strong>Detalle de la cotización</strong></h2>
  </div>

  <div class="table-responsive">
   <table class="table table-bordered table-vcenter">
    <thead>
     <tr>
      <th class="text-center" style="width: 100px;">ID</th>
      <th class="text-center">Imagen</th>
      <th class="text-center">Producto</th>
      <th class="text-center">Referencia</th>
      <th class="text-center">Cantidad</th>
      </tr>
    </thead>
    <tbody>
     @foreach($productos as $producto)
      <tr>
       <td class="text-center"><strong>IDT.{{$producto->id}}</strong></td>
       <td class="text-center" width="10%"><strong><img src="{{$producto->image}}" class="img-responsive"></strong></td>
       <td>{{$producto->name}} </td>
       <td class="text-center text-primary"><b>{{$producto->referencia}}</b></td>
       <td class="text-center"><strong>{{$producto->cantidad}}</strong></td>
       </tr>
     @endforeach
    </tbody>
   </table>
  </div>
 </div>
</div>

<!-- Información del Cliente -->
<div class="container">
  <div class="row">
    <div class="col-sm-12">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-user"></i> <strong>Información del</strong> Cliente</h2>
        </div>
        @if($informacion)
        <h4><strong>{{$informacion->nombre ?? 'N/A'}} {{$informacion->apellido ?? ''}}</strong></h4>
        <address>
            <b>{{$informacion->ciudad ?? 'N/A'}}</b><br>
            {{$informacion->departamento ?? 'N/A'}}<br>
            <br>
            {{$informacion->direccion ?? 'N/A'}}<br>
            @if(isset($informacion->inmueble))
              @if($informacion->inmueble == 1)
                <strong>Casa</strong>
              @elseif($informacion->inmueble == 2)
                <strong>Apartamento</strong>
              @elseif($informacion->inmueble == 3)
                <strong>Oficina</strong>
              @endif
            @endif
            <br>
            <br>                                
            <i class="fa fa-phone"></i> {{$informacion->telefono ?? 'N/A'}}<br>
            <i class="fa fa-envelope-o"></i> <a href="javascript:void(0)">{{$informacion->email ?? 'N/A'}}</a>
        </address>
        @else
        <p>No hay información de cliente disponible</p>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="/adminsite/js/pages/tablesDatatables.js"></script>
<script>$(function(){ TablesDatatables.init(); });</script>

@else

@foreach($datos as $dato)
<div class="container">
  <div class="row text-center">
    <div class="col-sm-6 col-lg-3">
     <div class="widget">
      <div class="widget-extra themed-background-success">
       <h4 class="widget-content-light"><strong>ORD.{{$dato->id}}</strong></h4>
      </div>
     <div class="widget-extra-full"><span class="h4 text-success animation-expandOpen">{{$dato->fecha}}</span></div>
    </div>
  </div>

  @if($dato->estado == "Aceptada" OR $dato->estado == "APPROVED")
    <div class="col-sm-6 col-lg-3">
      <div class="widget">
       <div class="widget-extra themed-background-success">
        <h4 class="widget-content-light"><i class="fa fa-paypal"></i> <strong>Estado</strong></h4>
       </div>
      <div class="widget-extra-full"><span class="h4 text-success animation-expandOpen"> Aceptada</span></div>
     </div>
    </div>
    @elseif($dato->estado == "Pendiente" OR $dato->estado == "PENDING")
    <div class="col-sm-6 col-lg-3">
     <div class="widget">
      <div class="widget-extra themed-background-warning">
       <h4 class="widget-content-light"><i class="fa fa-paypal"></i> <strong>Estado</strong></h4>
      </div>
      <div class="widget-extra-full"><span class="h4 text-warning animation-expandOpen"> Pendiente</span></div>
     </div>
    </div>
    @elseif($dato->estado == "Rechazada" OR $dato->estado == "REJECTED")
    <div class="col-sm-6 col-lg-3">
     <div class="widget">
      <div class="widget-extra themed-background-danger">
       <h4 class="widget-content-light"><i class="fa fa-paypal"></i> <strong>Estado</strong></h4>
      </div>
     <div class="widget-extra-full"><span class="h4 text-danger animation-expandOpen"> Rechazada</span></div>
    </div>
    </div>
    @endif
                        
    <div class="col-sm-6 col-lg-3">
     <div class="widget">
      <div class="widget-extra themed-background-warning">
       <h4 class="widget-content-light"><i class="fa fa-archive"></i> <strong># Aprobación</strong></h4>
      </div>
     <div class="widget-extra-full"><span class="h4 text-warning">{{$dato->codigo_apr}}</span></div>
    </div>
    </div>

    <div class="col-sm-6 col-lg-3">
     <div class="widget">
      <div class="widget-extra themed-background-muted">
       <h4 class="widget-content-light"><i class="fa fa-truck"></i> <strong>Medio de Pago</strong></h4>
      </div>
      @if($dato->medio == 'AM')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Amex</span></div>
      @elseif($dato->medio == 'BA')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Baloto</span></div>
      @elseif($dato->medio == 'CR')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Credencial</span></div>
      @elseif($dato->medio == 'DC' OR $dato->medio == "Diners")
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Diners Club</span></div>
      @elseif($dato->medio == 'EF')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Efecty</span></div>
      @elseif($dato->medio == 'GA')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Gana</span></div>
      @elseif($dato->medio == 'PR')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Punto Red</span></div>
      @elseif($dato->medio == 'RS')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Red Servi</span></div>
      @elseif($dato->medio == 'MC' OR $dato->medio == "MasterCard")
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Master Card</span></div>
      @elseif($dato->medio == 'PSE')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">PSE</span></div>
      @elseif($dato->medio == 'SP')
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">SafetyPay</span></div>
      @elseif($dato->medio == 'VS' OR $dato->medio == "Visa")
      <div class="widget-extra-full"><span class="h4 text-muted animation-pulse">Visa</span></div>
      @endif
    </div>
    </div>
  </div>
</div>
@endforeach

<div class="container">
 <div class="block">
  <div class="block-title">
   <h2><i class="fa fa-shopping-cart"></i> <strong>Detalle de la orden</strong></h2>
  </div>

  <div class="table-responsive">
   <table class="table table-bordered table-vcenter">
    <thead>
      <tr>
       <th class="text-center" style="width: 100px;">ID</th>
       <th>Producto</th>
       <th class="text-center">Referencia</th>
       <th class="text-center">Cantidad</th>
       <th class="text-center">Vr.Unitario</th>
       <th class="text-center">% Descuento</th>
       <th class="text-center">Vr. Iva</th>
       <th class="text-right" style="width: 10%;">Vr.Total</th>
      </tr>
    </thead>
    <tbody>
     <?php 
     $subtotalSinDescuento = 0;
     $subtotalConDescuento = 0;
     $ivaGeneral = 0;
     ?>
     
     @foreach($productos as $producto)
       <?php
       // Subtotal sin descuento (precio unitario * cantidad)
       $subtotalProducto = $producto->precio * $producto->quantity;
       
       // Valor del descuento en pesos
       $descuentoProducto = $subtotalProducto * ($producto->descuento / 100);
       
       // Subtotal con descuento aplicado
       $subtotalConDescuentoProducto = $subtotalProducto - $descuentoProducto;
       
       // IVA del producto (calculado sobre el subtotal CON descuento)
       $ivaProducto = $subtotalConDescuentoProducto * ($producto->iva / 100);
       
       // Total del producto (subtotal con descuento + iva)
       $totalProducto = $subtotalConDescuentoProducto + $ivaProducto;
       
       // Acumular totales generales
       $subtotalSinDescuento += $subtotalProducto;
       $subtotalConDescuento += $subtotalConDescuentoProducto;
       $ivaGeneral += $ivaProducto;
       ?>
      <tr>
        <td class="text-center"><strong>IDT.{{$producto->id}}</strong></td>
        <td>{{$producto->name}} </td>
        <td class="text-center text-primary"><b>{{$producto->referencia}}</b></td>
        <td class="text-center"><strong>{{$producto->quantity}}</strong></td>
        <td class="text-center"><strong>$ {{number_format($producto->precio,0,",",".")}}</strong></td>
        <td class="text-center"><strong>{{$producto->descuento}}%</strong></td>
        <td class="text-center"><strong>$ {{number_format($ivaProducto,0,",",".")}}</strong></td>
        <td class="text-right"><strong>$ {{number_format($totalProducto,0,",",".")}}</strong></td>
       </tr>
     @endforeach
     
     @foreach($totales as $total)
     <?php
     // Costo de envío
     $envio = $total->cos_envio ?? 0;
     
     // Aplicar cupón si existe (sobre el subtotal CON descuento de productos)
     $descuentoCupon = 0;
     if(isset($total->tipo) && $total->tipo != '' && $total->tipo > 0) {
         $descuentoCupon = $subtotalConDescuento * ($total->tipo / 100);
     }
     
     // Subtotal final después de todos los descuentos (productos + cupón)
     $subtotalFinal = $subtotalConDescuento - $descuentoCupon;
     
     // Total general = subtotal final + IVA + envío
     $totalGeneral = $subtotalFinal + $ivaGeneral + $envio;
     ?>
     
     <!-- Fila de Descuento por Productos -->
     @php
         $descuentoTotalProductos = $subtotalSinDescuento - $subtotalConDescuento;
     @endphp
     @if($descuentoTotalProductos > 0)
     <tr>
       <td colspan="7" class="text-right text-uppercase"><strong>Descuento por Productos:</strong></td>
       <td class="text-right"><strong>$ {{number_format($descuentoTotalProductos,0,",",".")}}</strong></td>
     </tr>
     @endif
     
     <!-- Fila de Cupón -->
     @if($descuentoCupon > 0)
     <tr>
       <td colspan="7" class="text-right text-uppercase"><strong>Cupón ({{$total->tipo}}%):</strong></td>
       <td class="text-right"><strong>- $ {{number_format($descuentoCupon,0,",",".")}}</strong></td>
     </tr>
     @endif
     
     <!-- Fila de Subtotal (con descuentos aplicados) -->
     <tr>
       <td colspan="7" class="text-right text-uppercase"><strong>Subtotal (con descuentos):</strong></td>
       <td class="text-right"><strong>$ {{number_format($subtotalFinal,0,",",".")}}</strong></td>
     </tr>
     
     <!-- Fila de IVA -->
     @if($ivaGeneral > 0)
     <tr>
       <td colspan="7" class="text-right text-uppercase"><strong>IVA (19%):</strong></td>
       <td class="text-right"><strong>$ {{number_format($ivaGeneral,0,",",".")}}</strong></td>
     </tr>
     @endif
     
     <!-- Fila de Costo Envío -->
     @if($envio > 0)
     <tr>
       <td colspan="7" class="text-right text-uppercase"><strong>Costo Envío:</strong></td>
       <td class="text-right"><strong>$ {{number_format($envio,0,",",".")}}</strong></td>
     </tr>
     @endif
     
     <!-- Fila de Valor Total -->
     <tr class="active">
       <td colspan="7" class="text-right text-uppercase"><strong>Valor Total:</strong></td>
       <td class="text-right"><strong>$ {{number_format($totalGeneral,0,",",".")}}</strong></td>
     </tr>
     
     @endforeach
    </tbody>
   </table>
  </div>
 </div>
</div>

<!-- Datos Envío -->
<div class="container">
  <div class="row">
    <div class="col-sm-12">
      <div class="block">
        <div class="block-title">
          <h2><i class="fa fa-user"></i> <strong>Datos</strong> Envío</h2>
        </div>
        @if($informacion)
        <h4><strong>{{$informacion->nombre ?? 'N/A'}} {{$informacion->apellido ?? ''}}</strong></h4>
        <address>
            <b>{{$informacion->ciudad ?? 'N/A'}}</b><br>
            {{$informacion->departamento ?? 'N/A'}}<br>
            <br>
            {{$informacion->direccion ?? 'N/A'}}<br>
            @if(isset($informacion->inmueble))
              @if($informacion->inmueble == 1)
                <strong>Casa</strong>
              @elseif($informacion->inmueble == 2)
                <strong>Apartamento</strong>
              @elseif($informacion->inmueble == 3)
                <strong>Oficina</strong>
              @endif
            @endif
            <br>
            <br>                                
            <i class="fa fa-phone"></i> {{$informacion->telefono ?? 'N/A'}}<br>
            <i class="fa fa-envelope-o"></i> <a href="javascript:void(0)">{{$informacion->email ?? 'N/A'}}</a>
        </address>
        @else
        <p>No hay información de envío disponible</p>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="/adminsite/js/pages/tablesDatatables.js"></script>
<script>$(function(){ TablesDatatables.init(); });</script>
@endif

@stop