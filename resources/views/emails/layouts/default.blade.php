<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Poppins', 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .logo {
            max-width: 150px;
            height: auto;
        }
        .bold {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('uploads/companies/images/' . $company->logo) }}" alt="{{ $company->name }}" class="logo">
        @yield('content')
        <hr>
        <p class="bold center">{{ $company->name }}</p>
        <p class="center">{{ $company->address_line }} {{ $company->barangay_town_city_province }} </p>
    </div>
</body>
</html>
