<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class Cms_autor extends Model{
use UsesTenantConnection;


protected $table = 'cms_autor';
public $timestamps = false;

}