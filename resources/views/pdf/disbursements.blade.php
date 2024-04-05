@extends('pdf.layout')

@section('content')
<div class="container">
    <br>
    <table class="table disbursement-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Account Number</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($disbursement->payrolls as $payroll)
                @if ($payroll->employee->bank_account_number &&  $payroll->employee->bank_account_name)
                <tr class="items">
                    <td>{{ $payroll->employee->bank_account_name }}</td>
                    <td>{{ $payroll->employee->bank_account_number }}</td>
                    <td>{{ $payroll->net_income }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div
@endsection
