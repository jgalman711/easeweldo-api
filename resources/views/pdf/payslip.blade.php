@extends('pdf.layout')

@section('content')
<div class="container">
    <br>
    <table>
        <td style="width: 55%;">
            <div class="employee-details">
                <h3>{{ $employee->full_name }}</h3>
                <p style="margin-bottom: 4px;">{{ $employee->job_title }} - {{ $employee->department }}</p>
                @if($employee->full_address)
                <p>Address: {{ $employee->full_address }}</p>
                @endif
                @if($employee->mobile_number)
                <p>Mobile: {{ $employee->mobile_number }}</p>
                @endif
                @if($employee->email_address)
                <p>Email: {{ $employee->email_address }}</p>
                @endif
            </div>
        </td>
        @if($period)
        <td style="width: 45%;">
            <table class="table payroll-details">
                <tr>
                    <th>Pay Date</th>
                    <td>{{ $period->salary_date }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ ucwords($period->type) }}</td>
                <tr>
                    <th>Period</th>
                    <td>
                        {{ \Carbon\Carbon::parse($period->start_date)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($period->end_date)->format('M d, Y') }}
                    </td>
                </tr>
            </table>
        </td>
        @endif
    </table>
    <br>
    <table>
        <td>
            <table class="table payslip">
                <tr class="column-header">
                    <th>Earnings</th>
                    <th>Rate</th>
                    <th>Hours</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td>Regular Pay</td>
                    <td>1.0</td>
                    <td>{{ $payroll->hours_worked }}</td>
                    <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                @if($payroll->overtime_minutes > 0)
                <tr>
                    <td>Overtime</td>
                    <td>1.1</td>
                    <td>{{ $payroll->overtime_minutes / 60 }}</td>
                    <td>{{ number_format($payroll->overtime_pay, 2) }}</td>
                </tr>
                @endif
                @if($payroll->regular_holiday_hours > 0)
                <tr>
                    <td>Regular Holiday</td>
                    <td>1.0</td>
                    <td>{{ $payroll->regular_holiday_hours }}</td>
                    <td>{{ number_format($payroll->regular_holiday_hours_pay, 2) }}</td>
                </tr>
                @endif
                @if($payroll->regular_holiday_hours_worked > 0)
                <tr>
                    <td>Regular Holiday Worked</td>
                    <td>1.0</td>
                    <td>{{ $payroll->regular_holiday_hours_worked }}</td>
                    <td>{{ number_format($payroll->regular_holiday_hours_worked_pay, 2) }}</td>
                </tr>
                @endif
                @if($payroll->special_holiday_hours > 0)
                <tr>
                    <td>Special Holiday</td>
                    <td>1.0</td>
                    <td>{{ $payroll->special_holiday_hours }}</td>
                    <td>{{ number_format($payroll->special_holiday_hours_pay, 2) }}</td>
                </tr>
                @endif
                @if($payroll->special_holiday_hours_worked > 0)
                <tr>
                    <td>Special Holiday Worked</td>
                    <td>0.3</td>
                    <td>{{ $payroll->special_holiday_hours_worked }}</td>
                    <td>{{ number_format($payroll->special_holiday_hours_worked_pay, 2) }}</td>
                </tr>
                @endif
                @if($payroll->late_minutes > 0)
                <tr>
                    <td colspan="2">Late</td>
                    <td>{{ $payroll->late_minutes / 60 }}</td>
                    <td>-{{ number_format($payroll->late_deductions, 2) }}</td>
                </tr>
                @endif
                @if($payroll->undertime_minutes > 0)
                <tr>
                    <td colspan="2">Undertime</td>
                    <td>{{ $payroll->undertime_minutes / 60 }}</td>
                    <td>-{{ number_format($payroll->undertime_deductions, 2) }}</td>
                </tr>
                @endif
                @if($payroll->absent_minutes > 0)
                <tr>
                    <td colspan="2">Absent</td>
                    <td>{{ $payroll->absent_minutes / 60 }}</td>
                    <td>-{{ number_format($payroll->absent_deductions, 2) }}</td>
                </tr>
                @endif
                @if($payroll->total_taxable_earnings > 0)
                <tr class="column-header">
                    <th colspan="4">Other Earnings</th>
                </tr>
                @foreach($payroll->taxable_earnings as $earning)
                <tr>
                    <td colspan="3">{{ ucwords($earning['name']) }}</td>
                    <td>{{ number_format($earning['pay'], 2) }}</td>
                </tr>
                @endforeach
                @endif
                <tr>
                    <td colspan="3" class="total" style="text-align: right;">Gross Income</td>
                    <td>{{ number_format($payroll->gross_income, 2) }}</td>
                </tr>
                <tr class="column-header">
                    <th colspan="4">Deductions</th>
                </tr>
                <tr>
                    <td colspan="3">SSS</td>
                    <td>-{{ number_format($payroll->sss_contributions, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">PhilHealth</td>
                    <td>-{{ number_format($payroll->philhealth_contributions, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">HDMF</td>
                    <td>-{{ number_format($payroll->pagibig_contributions, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3">Tax Withheld</td>
                    <td>-{{ number_format($payroll->withheld_tax, 2) }}</td>
                </tr>
                @if($payroll->total_taxable_earnings > 0)
                <tr class="column-header">
                    <th colspan="4">Non-taxable Earnings</th>
                </tr>
                @foreach($payroll->non_taxable_earnings as $earning)
                <tr>
                    <td colspan="3">{{ ucwords($earning['name']) }}</td>
                    <td>{{ number_format($earning['pay'], 2) }}</td>
                </tr>
                @endforeach
                @endif
                <tr>
                    <td colspan="3" class="total" style="text-align: right;">Net Income</td>
                    <td>{{ number_format($payroll->net_income, 2) }}</td>
                </tr>
            </table>
        </td>
        {{-- 
        <td style="width: 35%;">
            <table class="table">
                <tr class="main-header">
                    <th colspan="3">Summary</th>
                </tr>
                <tr class="column-header">
                    <th colspan="2">Type</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td colspan="2">Earnings Type 1</td>
                    <td>Amount 1</td>
                </tr>
            </table>
            <br>
            <table class="table">
                <tr class="main-header">
                    <th colspan="3">Year to Date Summary</th>
                </tr>
                <tr class="column-header">
                    <th colspan="2">Type</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <td colspan="2">Earnings Type 1</td>
                    <td>Amount 1</td>
                </tr>
            </table>
        </td>
        --}}
    </table>
</div
@endsection
