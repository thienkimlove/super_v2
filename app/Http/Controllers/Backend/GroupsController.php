<?php namespace App\Http\Controllers\Backend;

use App\Group;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use Illuminate\Http\Request;


class GroupsController extends Controller
{

    public function index()
    {
        return view('groups.index');
    }

    public function create()
    {
        return view('groups.create');
    }

    public function store(GroupRequest $request)
    {
        $request->store();

        flash()->success('Success!', 'Group successfully created.');

        return redirect()->route('groups.index');
    }

    public function edit($id)
    {
        $group = Group::find($id);

        return view('groups.edit', compact('group'));
    }

    public function update(GroupRequest $request, $id)
    {
        $request->save($id);

        flash()->success('Thành công', 'Cập nhật thành công!');

        return redirect()->route('groups.edit', $id);
    }

    public function dataTables(Request $request)
    {
        return Group::getDataTables($request);
    }

}
