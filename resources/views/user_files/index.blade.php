<!DOCTYPE html>
<html>
<head>
    <title>Mis archivos</title>
</head>
<body>
<div class="container">
    <h1>Mis archivos</h1>
    <ul>
        @foreach ($fileUrls as $index => $url)
            <li>
                <a href="{{ $url }}" target="_blank">{{ $fileNames[$index] }}</a>
                <br>
                <br>
                @if (pathinfo($url, PATHINFO_EXTENSION) == 'pdf')
                    <img src="/public/png/pdf-icon-9.png" alt="Icono de PDF">
                @else
                    <img src="{{ $url }}" alt="{{ $fileNames[$index] }}" style="width: 100px; height: 100px;">
                @endif
            </li>
        @endforeach
    </ul>
</div>
</body>
</html>
