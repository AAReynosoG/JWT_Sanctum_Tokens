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
            .btn-primary {
                background-color: #007bff;
                border-color: #007bff;
            }
            .btn-danger {
                background-color: #dc3545;
                border-color: #dc3545;
            }
        </style>
    </head>
    <body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">¿Estas seguro?</h5>
                <p class="card-text">De eliminar al usuario: {{$user->email}}</p>
                <form action="/delete/{{$user->id}}" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary">Si</button>
                </form>
                <br>
                <a href="/index" class="btn btn-danger">Cancelar</a>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    </body>
    </html>
@endsection
