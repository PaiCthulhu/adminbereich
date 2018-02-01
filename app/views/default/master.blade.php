<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Teste</title>
    <link rel="stylesheet" href="{{ROOT}}{{DS}}public{{DS}}css{{DS}}style.css" type="text/css" media="screen" />
</head>
<body>

@include('header')

<div class="content">

    @yield('content')

</div>

@include('footer')

</body>
</html>