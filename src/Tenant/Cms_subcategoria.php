<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_subcategoria extends Model
{
    use UsesTenantConnection;
	protected $table = 'cms_subcategoria';
    public $timestamps = true;

		public function products(){
	return $this->hasMany('Sitedigitalweb\Carrito\Tenant\Cms_producto');

	}
}

