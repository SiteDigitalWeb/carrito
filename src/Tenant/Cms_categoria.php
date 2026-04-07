<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_categoria extends Model
{ 
	use UsesTenantConnection;
	protected $table = 'cms_categoria';
    public $timestamps = true;
}
