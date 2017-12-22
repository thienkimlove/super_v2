<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\Facades\DataTables;

class Group extends Model
{
    protected $fillable = ['name'];


    public static function getDataTables($request)
    {
        $group = static::select('*')->latest('created_at');

        return DataTables::of($group)
            ->filter(function ($query) use ($request) {
                if ($request->filled('name')) {
                    $query->where('name', 'like', '%' . $request->get('name') . '%');
                }
            })
            ->addColumn('action', function ($group) {
                $response_html = '<a class="table-action-btn" title="Chỉnh sửa group" href="' . route('groups.edit', $group->id) . '"><i class="fa fa-pencil text-success"></i></a>';
                return $response_html;

            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
