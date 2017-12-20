<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'permission_id', 'remember_token', 'contact', 'status', 'username', 'group_id'
    ];

    /**
 * The attributes that should be hidden for arrays.
 *
 * @var array
 */
    protected $hidden = [];

    public function missingPermission($action)
    {
        return ($this->permission_id && in_array($action, config('permissions')[$this->permission_id]['permission']));
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
