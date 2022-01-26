<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Confirmacion de Pago</title>
</head>
<body>
    <div style="">
        <h1>{{ $email['title'] }}</h1>
        <p>El codigo de confirmacion es: {{ $email['body'] }}</p>
        <br>
        <p>Gracias</p>
    </div>
</body>
</html>