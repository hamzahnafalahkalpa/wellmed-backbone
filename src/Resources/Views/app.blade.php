<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/wellmed-backbone/export.css') }}">
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            box-sizing: border-box;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

            .bg-soft-blue {
                background-color: #b3d9ff;
            }

            .bg-navy {
                background-color: #004d99;
            }

        @yield("css")
    </style>
</head>
<body class="w-full m-0 p-0 box-border">
    @yield('content')
</body>
</html>
