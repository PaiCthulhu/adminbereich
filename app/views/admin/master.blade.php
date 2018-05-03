<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Teste</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.6/css/all.css">
    <link rel="stylesheet" href="{{PATH}}{{DS}}css{{DS}}admin.min.css" type="text/css" />
    <link rel="icon" type="image/x-icon" href="{{PATH}}{{DS}}img/favicon.ico"/>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>

@include('admin.components.header')

@if($_page != 'admin.login')
<aside id="sidebar">
    @php $menu = \abApp\Admin::MENU @endphp
    @include('admin.components.sidebar', ['options'=>$menu])
</aside>
@endif

<div class="content">

    @yield('content')

</div>

</body>
</html>