<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_departamento extends Model{
use UsesTenantConnection;
protected $table = 'cms_departamentos';
public $timestamps = true;
}