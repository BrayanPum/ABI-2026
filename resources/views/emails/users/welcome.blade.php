<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a ABI</title>
</head>
<body>
    <h1>¡Hola {{ $name }}!</h1>
    <p>Tu cuenta en la plataforma ABI ha sido creada exitosamente con el rol de <strong>{{ $role }}</strong>.</p>
    
    <p>Puedes acceder al sistema en el siguiente enlace:</p>
    <p><a href="{{ $url }}">{{ $url }}</a></p>

    <p>Si tienes alguna duda, por favor contacta al personal de investigación.</p>

    <p>Atentamente,<br>Equipo ABI</p>
</body>
</html>
