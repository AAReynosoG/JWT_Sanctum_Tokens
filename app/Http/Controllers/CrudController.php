<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


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

        Log::channel('slack')->info('Se ha registrado un nuevo usuario', ['user' => $user]);
        return view('crud.ok', ['nombre' => $user->email, 'msg1' => 'Registro', 'msg2' => 'registrado']);
    }

    public function index(){
        $users = User::all();
        if ($users->isEmpty()) {
            Log::channel('slack')->error('Se intento consumir la lista de usuarios, pero no hay usuarios registrados');
            return view('crud.alert', ['msg' => 'No hay usuarios registrados']);
        }
        Log::channel('slack')->info('Se ha consultado la lista de usuarios', ['users' => $users]);
        return view('crud.index', ['users' => $users]);
    }


    public function update(Request $request, $userId){
        if ($userId == null) {
            return view('crud.alert', ['msg' => 'No existe ese ID']);
        }
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|min:4',
            'apellido' => 'required|string|max:255|min:4',
            'email' => 'required|email|max:100|unique:users,email,'.$userId,
        ]);

        if ($validate->fails()) {
            return redirect()->back()->withInput()->withErrors($validate);
        }

        $oldUser = User::find($userId);
        if ($oldUser == null) {
            return view('crud.alert', ['msg' => 'El usuario no existe']);
        }
        $user = User::find($userId);
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->save();

        if ($oldUser != $user) {
            Log::channel('slack')->info('Se ha actualizado un usuario', ['oldUser' => $oldUser, 'newUser' => $user]);
            return view('crud.ok', ['nombre' => $user->email, 'msg1' => 'Update', 'msg2' => 'actualizado']);
        }else{
            Log::channel('slack')->info('Se intento actualizar un usuario, pero no se ha actualizado ningún dato', ['oldUser' => $oldUser, 'newUser' => $user]);
            return view('crud.alert', ['msg' => 'No se ha actualizado ningún dato']);
        }
    }

    public function delete($userId)
    {
        $user = User::find($userId);
        if ($user == null) {
            Log::channel('slack')->error('Se intento eliminar un usuario, pero no existe', ['userId' => $userId]);
            return view('crud.alert', ['msg' => 'El usuario no existe']);
        }
        $user->delete();
        Log::channel('slack')->info('Se ha eliminado un usuario', ['user' => $user]);
        return view('crud.ok', ['nombre' => $user->email, 'msg1' => 'Delete', 'msg2' => 'eliminado']);

    }

    public function updateView($userId){
        $user = User::find($userId);
        if (!$user) {
            return view('crud.alert', ['msg' => 'El usuario no existe']);
        }
        return view('crud.update', ['user' => $user]);
    }

    public function deleteView($userId){
        $user = User::find($userId);
        if ($user == null) {
            return view('crud.alert', ['msg' => 'El usuario no existe']);
        }
        return view('crud.delete', ['user' => $user]);
    }
}
