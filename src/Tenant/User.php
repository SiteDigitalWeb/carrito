<?php

namespace Sitedigitalweb\Carrito\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
use UsesTenantConnection;
	protected $table = 'users';

	protected $hidden = array('password', 'remember_token');

	public function orders(){
	return $this->hasMany('Sitedigitalweb\Carrito\Tenant\Order');
	
}

}

