<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ asset('css/payslip.css') }}" type="text/css">
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div class="header">
            <table class="payslip-hearder">
                <tr>
                    <th colspan="3"><h2>{{ ucwords($company->name) }}</h3></th>
                    <th style="text-align: right"><h2>PAYSLIP</h3></th>
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
