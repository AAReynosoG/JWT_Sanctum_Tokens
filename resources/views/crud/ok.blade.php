<!DOCTYPE html>
@extends('base.app')
@section('content')
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Carta de Diseño Exitoso</title>

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
            }
            .container {
                max-width: 500px;
                margin: auto;
                padding-top: 50px;
            }
            .card {
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                transition: 0.3s;
            }
        </style>
    </head>
    <body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{$msg1}} Exitoso</h5>
                <p class="card-text">¡Felicidades! Haz {{$msg2}} al usuario:
                    @isset($nombre)
                        {{$nombre}}
                    @else
                        Usuario desconocido
                    @endisset</p>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script>
        setTimeout(function(){
            window.location.href = '/index';
        }, 4000);
    </script>
    </body>
    </html>
@endsection
