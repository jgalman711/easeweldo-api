<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="{{ asset('css/payslip.css') }}" type="text/css">
    </head>
    <body>
        <div class="header">
            <table class="payslip-hearder">
                <tr>
                    <th colspan="3"><h2>{{ ucwords($company->name) }}</h3></th>
                    <th style="text-align: right"><h2>@yield('title', 'INVOICE')</h3></th>
                </tr>
                @if($company->full_address)
                <tr>
                    <td colspan="4">Address: {{ $company->full_address }}</th>
                </tr>
                @endif
                @if($company->mobile_number || $company->landline_number)
                <tr>
                    <td colspan="4">Contact: {{ $company->mobile_number ?? $company->landline_number }}</th>
                </tr>
                @endif
                @if($company->email_address)
                <tr>
                    <td colspan="4">Email: {{ $company->email_address }}</th>
                </tr>
                @endif
            </table>
        </div>
        @yield('content')
    </body>
</html>
