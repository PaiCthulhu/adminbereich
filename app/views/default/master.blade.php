<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Teste</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ROOT}}{{DS}}public{{DS}}css{{DS}}style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/c51246e0c3.css">
</head>
<body>

@include('header')

<div class="content">

    @yield('content')

</div>

@include('footer')

</body>
</html>