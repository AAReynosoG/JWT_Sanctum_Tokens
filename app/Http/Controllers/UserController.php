<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Notifications\SlackNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountActivation;
use \Illuminate\Support\Facades\Notification;
class UserController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api:jwt', ['except' => ['register', 'login', 'loginSanctum']]);
    }

    public function SlackNotification($message){
        $noti = new SlackNotification($message);
        Notification::route('slack', env('LOG_SLACK_WEBHOOK_URL'))->notify($noti);
    }

    public function loginSanctum(Request $request)
    {

        Log::info('LogingAttemptFromSanctum', [$request]);
        Log::channel('slack')->info('LogingAttemptFromSanctum', [$request]);

        $user = User::where('email', $request->email)->first();

        if(! $user || !Hash::check($request->password, $user->password)){
            Log::info('LogAttemptFromSanctumFailed', [$request]);
            Log::channel('slack')->info('LogAttemptFromSanctumFailed', [$request]);
            $this->SlackNotification('Se ha intentado logear el usuario: '.$request->email.' con sanctum y ha fallado.');
            return response()->json([
                'msg' => 'No autorizado'
            ], 401);
        }

        Log::info('UserLoged', [$user]);

        $info = [
            'access_token' => $user->createToken('*')->plainTextToken,
            'token_type' => 'Bearer',
        ];

        $this->SlackNotification('Se ha logeado el usuario: '.$user->nombre.' '.$user->apellido.' con sanctum.');
        return response()->json($info);
    }

    public function login(Request $request){

        Log::info('LogingAttemptFromJWT', [$request]);

        $credentials = request(['email', 'password']);

        if(! $token = Auth::guard('api:jwt')->attempt($credentials)){
            Log::info('LogAttemptFromJWTFailed', [$request]);
            Log::channel('slack')->info('LogAttemptFromJWTFailed', [$request]);
            $this->SlackNotification('Se ha intentado logear el usuario: '.$credentials['email'].' con JWT y ha fallado.');
            return response()->json([
                'msg' => 'No autorizado',
            ], 401);
        }

        Log::info('UserLoged', [$user = User::where('email', $credentials['email'])->first()]);
        Log::channel('slack')->info('LogAttemptFromJWT', [$request]);
        $this->SlackNotification('Se ha logeado el usuario: '.$user->nombre.' '.$user->apellido.' con JWT.');

        return $this->respondWithToken($token, $credentials['email']);
    }


    public function me(){
        $user = auth()->guard('api:jwt')->user();
        $userE = json_encode($user, JSON_PRETTY_PRINT);
        Log::channel('slack')->info('UserConsulted', [$user]);
        $this->SlackNotification('Se ha consultado la información del usuario: '.$userE).' con JWT.';
        return response()->json($user);
    }

    protected  function respondWithToken($token, $email){
        $isActive = $this->isActive($email);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'isActive' => $isActive,
            'expires_in' => auth('api:jwt')->factory()->getTTL() * 60,
        ]);
    }

    public function isActive($email){
        $user = User::where('email', $email)->first();
        if($user){
            $isActive = $user->esta_activo;
            return $isActive;
        }
        else {
            return response()->json([
                'msg' => 'Usuario no encontrado'
            ], 404);
        }
    }

    public function logout()
    {
        $user = auth('api:jwt')->user();
        $userE = json_encode($user, JSON_PRETTY_PRINT);
        auth()->logout();
        Log::channel('slack')->info('UserLoggedOut', [$user]);
        $this->SlackNotification('El usuario: ' .$userE . ' ha cerrado sesión.');
        return response()->json(['message' => 'Se ha cerrado sesión correctamente']);
    }

    public function register(Request $request){
        $validate = Validator::make(
            $request->all(),
            [
                "nombre"    =>"required|max:100|min:4",
                "apellido"  =>"required|max:100|min:4",
                "email"      =>"required|email",
                "password" =>"required|min:8"
            ],
            [
                "nombre.required" => "El nombre es requerido",
                "nombre.max" => "El nombre debe tener máximo 100 caracteres",
                "nombre.min" => "El nombre debe tener mínimo 4 caracteres",
                "apellido.required" => "El apellido es requerido",
                "apellido.max" => "El apellido debe tener máximo 100 caracteres",
                "apellido.min" => "El apellido debe tener mínimo 4 caracteres",
                "email.required" => "El email es requerido",
                "email.email" => "El email debe ser válido",
                "password.required" => "La contraseña es requerida",
                "password.min" => "La contraseña debe tener al menos 8 caracteres"
            ]
        );

        if($validate->fails()){
            Log::channel('slack')->error('UserRegisterFailed', [$request]);
            $this->SlackNotification('Se ha intentado registrar un usuario con datos inválidos '. $request);
            return response()->json([
                "msg"=>"Error al validar los datos",
                "error"=>$validate->errors()
            ],422);

        }

        $user = new User();
        $user->nombre = $request->nombre;
        $user->apellido = $request->apellido;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        $userE  = json_encode($user, JSON_PRETTY_PRINT);
        Log::channel('slack')->info('UserRegistered', [$user]);
        $this->SlackNotification('Se ha registrado un usuario con los siguientes datos: '. $userE);

        Mail::to($user->email)->send(new AccountActivation($user));

        return response()->json([
            "msg"=>"Usuario registrado",
            "activo" => false,
        ],201);

    }


}
