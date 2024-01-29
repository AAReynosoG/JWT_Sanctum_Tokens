<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class FileController extends Controller
{
    public function store(Request $request, $file_name='#'){
        $user = auth()->guard('api:jwt')->user();

        $validate = Validator::make($request->all(), [
                'file' => 'required|mimes:pdf,jpeg,png|max:2048',
            ]);

            if ($validate->fails()) {
                Log::channel('slack')->error('FileUploadFail', [$validate->errors()]);
                $UserController = new UserController();
                $UserController->SlackNotification('Se ha intentado subir un archivo con el formato incorrecto via JWT y ha fallado.');
                return response()->json([
                    'msg' => 'File not uploaded',
                    'errors' => $validate->errors()
                ], 400);
            }

            $path = Storage::disk('digitalocean')->putFile('alonso', $request->file('file'), 'public');
            $userFile = new UserFile();
            $userFile->user_id = $user->id;
            $userFile->file_path = $path;
            $userFile->file_name = $file_name;
            $userFile->save();

            Log::channel('slack')->info('FileUploaded', [$userFile]);
            $UserController = new UserController();
            $UserController->SlackNotification('El usuario: '.$user->nombre.', ha subido un archivo via JWT.');

            return response()->json([
                'msg' => 'File uploaded successfully',
                'file' => $userFile->file_path
            ]);
    }

    public function index(){
        $user = auth()->guard('api:jwt')->user();

        $userName = $user->nombre;

        $folderName = strtolower($userName);

        $files = Storage::disk('digitalocean')->files($folderName);

        if(!$files){
            Log::channel('slack')->error('FilesNotFound', [$files]);
            $UserController = new UserController();
            $UserController->SlackNotification('Se a solicitado ver archivos via JWT y no se han encontrado.');
            return response()->json([
                'msg' => 'No files found'
            ], 404);
        }

        Log::channel('slack')->info('FilesConsulted', [$files]);
        $UserController = new UserController();
        $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver archivos via JWT.');
        return response()->json(['files' => $files]);

    }

    public function show($file_path){
        $user = auth()->guard('api:jwt')->user();
        $userId = $user->id;

        $userName = $user->nombre;
        $folderName = strtolower($userName);

        $path = $folderName.'/'. $file_path;

        $userFile = UserFile::where('user_id', $userId)->where('file_path', $path)->first();

        if (!$userFile){
            Log::channel('slack')->error('FileNotFound', [$file_path]);
            $UserController = new UserController();
            $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver un archivo via JWT y no se ha encontrado.');
            return response()->json([
                'msg' => 'File not found'
            ], 404);
        }

        $fileUrl = Storage::disk('digitalocean')->url($userFile->file_path);

        $context = stream_context_create(['http' => ['method' => 'HEAD']]);
        $response = @get_headers($fileUrl, 0, $context);

        if(!$response || $response[0] !== 'HTTP/1.1 200 OK') {
            $dbFile = UserFile::where('user_id', $userId)->where('file_path', $path)->delete();
            Log::channel('slack')->error('FileNotFound', [$file_path]);
            $UserController = new UserController();
            $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver un archivo via JWT y no se ha encontrado.');
            return response()->json([
                'msg' => 'No files found'
            ], 404);
        }
        else {
            Log::channel('slack')->info('FileConsulted', [$fileUrl]);
            $UserController = new UserController();
            $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver un archivo via JWT.');

            // Descarga el archivo de DigitalOcean Spaces a tu servidor local
            $localPath = tempnam(sys_get_temp_dir(), 'tmp');
            file_put_contents($localPath, file_get_contents($fileUrl));

            // Devuelve la imagen o el PDF directamente
            return response()->file($localPath);
        }
    }

    ##########################################
    ##########################################

    public function storeSanctum(Request $request, $file_name){
        $user = $request->user();

        $validate = Validator::make($request->all(), [
            'file' => 'required|mimes:jpeg,png|max:2048',
        ], [
            'file.mimes' => 'El archivo debe ser de tipo jpeg o png',
            'file.max' => 'El archivo no debe pesar mas de 2MB',
            'file.required' => 'El archivo es requerido'
        ]);

        if ($validate->fails()) {
            Log::channel('slack')->error('FileUploadFail', [$validate->errors()]);
            $UserController = new UserController();
            $UserController->SlackNotification('Se ha intentado subir un archivo con el formato incorrecto via sanctum y ha fallado.');
            return response()->json([
                'msg' => 'File not uploaded',
                'errors' => $validate->errors()
            ], 400);
        }

        $path = Storage::disk('digitalocean')->putFile('alonso', $request->file('file'), 'public');
        $userFile = new UserFile();
        $userFile->user_id = $user->id;
        $userFile->file_path = $path;
        $userFile->file_name = $file_name;
        $userFile->save();

        Log::channel('slack')->info('FileUploaded', [$userFile]);
        $UserController = new UserController();
        $UserController->SlackNotification('El usuario: '.$user->nombre.', ha subido un archivo via JWT.');

        return response()->json([
            'msg' => 'File uploaded successfully'
        ]);
    }

    public function indexSanctum(Request $request){
        $user = $request->user();
        $userId = $user->id;
        $userFiles = UserFile::where('user_id', $userId)->get();

        $fileUrls = $userFiles->map(function ($userFile) {
            return Storage::disk('digitalocean')->url($userFile->file_path);
        });

        if($fileUrls->isEmpty())
        {
            Log::channel('slack')->error('FilesNotFound', [$fileUrls]);
            $UserController = new UserController();
            $UserController->SlackNotification('Se a solicitado ver archivos via JWT y no se han encontrado.');
            return response()->json([
                'msg' => 'No files found'
            ], 404);
        }

        $fileNames = $userFiles->map(function ($userFile) {
            return $userFile->file_path;
        });

        if($fileUrls->isEmpty())
            return response()->json([
                'msg' => 'No files found'
            ], 404);

        Log::channel('slack')->info('FilesConsulted', [$fileUrls]);
        $UserController = new UserController();
        $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver archivos via JWT.');

        return view('user_files.index', ['fileUrls' => $fileUrls, 'fileNames' => $fileNames]);
    }

    public function showSanctum(Request $request,$file_path){
        $user = $request->user();
        $userId = $user->id;

        $path = 'alonso/' . $file_path;

        $userFile = UserFile::where('user_id', $userId)->where('file_path', $path)->first();

        if (!$userFile){
            Log::channel('slack')->error('FileNotFound', [$file_path]);
            $UserController = new UserController();
            $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver un archivo via JWT y no se ha encontrado.');
            return response()->json([
                'msg' => 'File not found'
            ], 404);
        }

        $fileUrl = Storage::disk('digitalocean')->url($userFile->file_path);
        if(!$fileUrl){
            return response()->json([
                'msg' => 'No files found'
            ], 404);
        }

        Log::channel('slack')->info('FileConsulted', [$fileUrl]);
        $UserController = new UserController();
        $UserController->SlackNotification('El usuario: '.$user->nombre.', a solicitado ver un archivo via JWT.');
        return view('user_files.show', ['fileUrls' => $fileUrl, 'fileNames' => $userFile->file_path]);
    }
}
