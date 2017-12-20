<?php namespace App\Http\Controllers\Backend;

use App\Group;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;


class UsersController extends AdminController
{
    public $permissions;
    public $groups;

    public function __construct()
    {
        parent::__construct();

        $this->permissions = [
            '' => 'Choose user level'
        ];

        foreach (config('permissions') as $key =>  $permission) {
            $this->permissions[$key] = $permission['label'];
        }
        $this->groups = ['' => 'Choose group'] + Group::pluck('name', 'id')->all();
    }

    public function index()
    {
        return view('admin.user.index');
    }

    public function dataTables(Request $request)
    {
        return User::getDataTables($request);
    }

    public function create()
    {
        $permissions = $this->permissions;
        return view('admin.user.form', compact('permissions'))->with(['groups' => $this->groups]);
    }

    public function store(UserRequest $request)
    {

        try {

            User::create([
                'email' => $request->input('email'),
                'username' => $request->input('username'),
                'contact' => $request->input('contact'),
                'permission_id' => $request->input('permission_id'),
                'group_id' => $request->input('group_id'),
                'status' => ($request->input('status') == 'on') ? true : false
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Create users success!', 'success');
        return redirect('admin/users');
    }


    public function edit($id)
    {
        $permissions = $this->permissions;
        $user = User::find($id);
        return view('admin.user.form', compact('permissions', 'user'))->with(['groups' => $this->groups]);
    }


    public function update($id, UserRequest $request)
    {
        $user = User::find($id);


        $data = [
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'contact' => $request->input('contact'),
            'group_id' => $request->input('group_id'),
            'permission_id' => $request->input('permission_id'),
            'status' => ($request->input('status') == 'on') ? true : false
        ];

        try {
            $user->update($data);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                $e->getMessage()
            ]);
        }

        flash('Update users success!', 'success');
        return redirect('admin/users');
    }


    public function destroy($id)
    {
        User::find($id)->delete();
        flash('Success deleted user!');
        return redirect('admin/users');
    }

}
