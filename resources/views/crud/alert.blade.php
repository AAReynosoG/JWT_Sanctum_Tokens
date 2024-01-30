<!doctype html>
@extends('base.app')
@section('content')
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
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
            .alert {
                border-radius: 15px;
                font-size: 18px;
                text-align: center;
            }
        </style>
    </head>
    <body>
    <div class="container mt-5">
        <div class="alert alert-danger" role="alert">
            {{$msg}}
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </body>
    </html>
@endsection
