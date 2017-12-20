<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

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

    public function isAdmin()
    {
        return ($this->permission_id == 1);
    }

    public static function getDataTables($request)
    {
        $user = static::select('*')->with('group');

        return DataTables::of($user)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }

                if ($request->filled('group_id')) {
                    $query->where('group_id', $request->get('group_id'));
                }
            })
            ->editColumn('status', function ($user) {
                return $user->status ? '<i class="ion ion-checkmark-circled text-success"></i>' : '<i class="ion ion-close-circled text-danger"></i>';
            })
            ->addColumn('group_name', function ($user) {
                return $user->group ? $user->group->name : '';
            })
            ->addColumn('action', function ($user) {
                return '<a class="table-action-btn" title="Chỉnh sửa người dùng" href="' . route('users.edit', $user->id) . '"><i class="fa fa-pencil text-success"></i></a>';

            })
            ->rawColumns(['group_name', 'status', 'action', 'email', 'name'])
            ->make(true);
    }
}
