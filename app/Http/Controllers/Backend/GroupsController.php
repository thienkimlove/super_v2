<?php namespace App\Http\Controllers\Backend;

use App\Group;
use App\Http\Requests\GroupRequest;

class GroupsController extends AdminController
{

    public function index()
    {

        $groups = Group::latest('updated_at')->paginate(10);

        return view('admin.group.index', compact('groups'));
    }

    public function create()
    {
        return view('admin.group.form');
    }

    public function store(GroupRequest $request)
    {

        try {

            Group::create([
                'name' => $request->input('name')
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Create group success!', 'success');
        return redirect('admin/groups');
    }


    public function edit($id)
    {
        $group = Group::find($id);
        return view('admin.group.form', compact('group'));
    }


    public function update($id, GroupRequest $request)
    {
        $group = Group::find($id);


        $data = [
            'name' => $request->input('name')
        ];

        try {
            $group->update($data);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Update group success!', 'success');
        return redirect('admin/groups');
    }


    public function destroy($id)
    {
        Group::find($id)->delete();
        flash('Success deleted group!');
        return redirect('admin/groups');
    }

}
