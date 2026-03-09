<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /*     $fillable: seguridad (qué campos se pueden asignar masivamente).solo esos campos se pueden llenar con 
        create()/update().
    $casts: tipo de datos (cómo convertir atributos al leer/escribir). */

    protected $fillable = [
        'role_name'
    ];

    /**
     * Relación: un rol tiene muchos usuarios.
     * users.role_id -> roles.id
     */

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }
}
