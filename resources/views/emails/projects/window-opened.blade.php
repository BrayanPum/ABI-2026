<!DOCTYPE html>
<html>
<head>
    <title>Nueva Convocatoria Abierta</title>
</head>
<body>
    <h1>¡Hola!</h1>
    <p>Te informamos que se ha abierto una nueva convocatoria para la recepción de propuestas de proyectos: <strong>{{ $windowName }}</strong>.</p>
    
    <p><strong>Periodo Académico:</strong> {{ $period }}</p>
    <p><strong>Fecha límite de envío:</strong> {{ $endDate }}</p>

    <p>Ya puedes registrar tu propuesta en el sistema accediendo al siguiente enlace:</p>
    <p><a href="{{ $url }}">{{ $url }}</a></p>

    <p>Te recomendamos realizar el proceso con tiempo para evitar inconvenientes de último momento.</p>

    <p>Atentamente,<br>Equipo ABI</p>
</body>
</html>
