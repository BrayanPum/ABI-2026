<!DOCTYPE html>
<html>
<head>
    <title>Actualización de Proyecto</title>
</head>
<body>
    <h1>Hola,</h1>
    <p>El proyecto <strong>{{ $projectTitle }}</strong> ha sido evaluado.</p>
    <p><strong>Nuevo estado:</strong> {{ $status }}</p>
    
    @if($comments)
        <p><strong>Comentarios:</strong></p>
        <p>{{ $comments }}</p>
    @endif

    <p>Puedes ver más detalles en el siguiente enlace:</p>
    <p><a href="{{ $url }}">{{ $url }}</a></p>

    <p>Atentamente,<br>Equipo ABI</p>
</body>
</html>
