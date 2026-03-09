<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class RoleController extends Controller
{
    //

    public function index()
    {

        $data_role = Role::get();
        $data_role->sortBy('name');


        // $title = 'Delete User!';
        // $text = "Are you sure you want to delete?";
        // confirmDelete($title, $text);

        return view('backend.pages.role.index', compact('data_role'));
    }


    public function store(Request $request)
    {
        //request validation .......

        $data_role = Role::firstOrCreate([
            'name' => $request['name'],
            'guard_name' => 'web',
        ]);

        Alert::success('Operation réussi', 'Success Message');

        return back();
    }


    public function update(Request $request, $id)
    {

        //request validation ......

        $data_page = Role::find($id)->update([
            'name' => $request['name'],
        ]);

        Alert::success('Opération réussi', 'Success Message');
        return back();
    }


    public function delete($id)
    {
        Role::find($id)->delete();
        return response()->json([
            'status' => 200,
        ]);
    }
}

