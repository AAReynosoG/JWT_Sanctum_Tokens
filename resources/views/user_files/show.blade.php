<!DOCTYPE html>
<html>
<head>
    <title>Mi archivo</title>
</head>
<body>
<div class="container">
    <h1>Mi archivo</h1>
    <ul>
        <li>
            <a href="{{ $fileUrls }}" target="_blank">{{ $fileNames }}</a>
            <br>
            <br>
            @if (pathinfo($fileUrls, PATHINFO_EXTENSION) == 'pdf')
                <img src="/public/png/pdf-icon-9.png" alt="Icono de PDF">
            @else
                <img src="{{ $fileUrls }}" alt="{{ $fileNames }}" style="width: 250px; height: 250px;">
            @endif
        </li>
    </ul>
</div>
</body>
</html>
