<?php


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\FileController;

Route::any('/errormsg', function (Request $request){
    (new App\Http\Controllers\UserController)->SlackNotification('Se ha intentado realizar una accion con un usuario no auth y ha fallado.');
    Log::channel('slack')->critical('Fail', [$request]);
    return response()->json([
        "msg" => "No autorizado"
    ], 401);
})->name('errormsg');


Route::any('/activationMsg', function (){
    return view('emails.succes');
})->name('activationMsg');


Route::any('/activation/{user}', [ActivationController::class, 'activate'])->name('activation');


Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout'); #JWT logout
Route::post('/sanctum/login', [UserController::class, 'loginSanctum']);
Route::post('/login', [UserController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        $user = $request->user();
        $userE = json_encode($user, JSON_PRETTY_PRINT);
        Log::channel('slack')->info('UserConsulted', [$user]);
        (new App\Http\Controllers\UserController)->SlackNotification('Se ha consultado la información del usuario: ' . $userE . ' con Sanctum.');
        return response()->json($user);
});


Route::get('/me', [UserController::class, 'me'])->name('me');





Route::group(['middleware' => ['auth:api:jwt']], function () {
    Route::get('/files', [FileController::class, 'index']);
    Route::get('/files/{path}', [FileController::class, 'show']);
    Route::post('/savefile', [FileController::class, 'store']);
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/sanctum/savefile', [FileController::class, 'storeSanctum']);
    Route::get('/sanctum/files', [FileController::class, 'indexSanctum']);
    Route::get('/sanctum/files/{path}', [FileController::class, 'showSanctum']);
});











