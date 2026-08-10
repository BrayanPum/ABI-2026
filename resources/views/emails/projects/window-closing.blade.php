<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio: Cierre de Convocatoria</title>
</head>
<body>
    <h1>¡Hola!</h1>
    <p>Este es un recordatorio de que la convocatoria <strong>{{ $windowName }}</strong> finalizará pronto.</p>
    
    <p>Quedan aproximadamente <strong>{{ $daysLeft }} días</strong> para que el periodo de recepción se cierre.</p>
    <p><strong>Fecha y hora límite:</strong> {{ $endDate }}</p>

    <p>Si aún no has enviado tu propuesta, te invitamos a hacerlo lo antes posible a través del siguiente enlace:</p>
    <p><a href="{{ $url }}">{{ $url }}</a></p>

    <p>Recuerda que después de la fecha límite no se recibirán más propuestas por este medio.</p>

    <p>Atentamente,<br>Equipo ABI</p>
</body>
</html>
