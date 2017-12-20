<?php namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;


class UsersController extends Controller
{

    public function index()
    {
        return view('users.index');
    }

    public function dataTables(Request $request)
    {
        return User::getDataTables($request);
    }

}
