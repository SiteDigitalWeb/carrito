<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;


class Cms_producto extends Model

{
use UsesTenantConnection;
protected $table = 'cms_producto_online';
	public $timestamps = true;

	public function categories(){

		return $this->belongsTo('Sitedigitalweb\Carrito\Tenant\cms_subcategoria');
	}

	  	public function pages(){

		return $this->belongsTo('Page');
	}

}