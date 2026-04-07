<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_municipio extends Model{
use UsesTenantConnection;
protected $table = 'cms_municipios';
public $timestamps = true;
}