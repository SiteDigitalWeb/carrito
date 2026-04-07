<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_configuracion extends Model
{
    use UsesTenantConnection;
	protected $table = 'cms_configuracion';
    public $timestamps = false;

}
