<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CrudController extends Controller
{

    public function store(Request $request){
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:4',
            'apellido' => 'required|string|max:255|min:4',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();

        }

        $user = new User();
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return view('crud.ok', ['nombre' => $user->email, 'msg1' => 'Registro', 'msg2' => 'registrado']);

    }

    public function index(Request $request){
        $users = User::all();
        return view('crud.index', ['users' => $users]);
    }


    public function update(Request $request, $userId){
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:4',
            'apellido' => 'required|string|max:255|min:4',
            'email' => 'required|email|max:100|unique:users,email,'.$userId,
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withInput()->withErrors($validate);

        }

        $oldUser = User::find($userId);
        $user = User::find($userId);
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->save();

        if ($oldUser != $user) {
            return view('crud.ok', ['nombre' => $user->email, 'msg1' => 'Update', 'msg2' => 'actualizado']);
        }else{
            return view('crud.test');
        }
    }

    public function delete($userId)
    {
        $user = User::find($userId);
        $user->delete();
        return view('crud.ok', ['nombre' => $user->nombre, 'msg1' => 'Delete', 'msg2' => 'eliminado']);

    }

    public function updateView($userId){
        $user = User::find($userId);
        return view('crud.update', ['user' => $user]);
    }

    public function deleteView($userId){
        $user = User::find($userId);
        return view('crud.delete', ['user' => $user]);
    }
}
