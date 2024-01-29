<!DOCTYPE html>
@extends('base.app')
@section('content')
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Formulario</title>

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
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="/update/{{$user->id}}" method="post">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="input1">Nombre(s):</label>
                <input type="text" class="form-control" id="input1" name="nombre" placeholder="nombre" value="{{$user->nombre}}">
            </div>
            <div class="form-group">
                <label for="input2">Apellidos</label>
                <input type="text" class="form-control" id="input2" name="apellido" placeholder="apellido" value="{{$user->apellido}}">
            </div>
            <div class="form-group">
                <label for="input3">Email: </label>
                <input type="text" class="form-control" id="input3" name="email" placeholder="email" value="{{$user->email}}">
            </div>
            <button type="submit" class="btn btn-primary">Enviar</button>
            <a href="/index" class="btn btn-danger">Cancelar</a>
        </form>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    </body>
    </html>
@endsection
