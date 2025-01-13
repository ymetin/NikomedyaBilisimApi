<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>İletişim Sayfası Email</title>
</head>

<body>
    <h2>
        Bu bir bilgilendirme mailidir.
    </h2>
    <p>Ad: {{ $mailData['name'] }}</p>
    <p>Email: {{ $mailData['email'] }}</p>
    <p>Telefon: {{ $mailData['phone'] }}</p>
    <p>Konu: {{ $mailData['subject'] }}</p>
    <p>Mesaj</p>
    <p>{{ $mailData['message'] }}</p>

    <p>Tesekkurler</p>
</body>

</html>
