<!DOCTYPE html>
@extends('base.app')
@section('content')
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tabla con Bootstrap</title>

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .table {
                border-collapse: collapse;
                width: 100%;
                margin: 20px 0;
                color: #444;
            }
            .table th {
                background-color: #f8f9fa;
                text-align: left;
                padding: 10px;
            }
            .table td {
                border: 1px solid #ddd;
                padding: 10px;
            }
            .btn {
                margin-right: 5px;
            }
        </style>
    </head>
    <body>
    <div class="container mt-5">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Apellido</th>
                    <th scope="col">Email</th>
                    <!--<th scope="col">Verificado</th>-->
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <th scope="row">{{$user->id}}</th>
                        <td>{{$user->nombre}}</td>
                        <td>{{$user->apellido}}</td>
                        <td>{{$user->email}}</td>
                        <!--@if($user->esta_activo == 1)
                            <td>True</td>
                        @else
                            <td>False</td>
                        @endif-->
                        <td><a href="/form/update/{{$user->id}}" class="btn btn-primary"><i class="fas fa-edit"></i></a></td>
                        <td><a href="/form/delete/{{$user->id}}" class="btn btn-danger"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </body>
    </html>
@endsection
