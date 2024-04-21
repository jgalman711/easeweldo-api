@extends('emails.layout')

@section('title', $title)

@section('content')
    <p>
        Dear {{ $bank->name }} - {{ $bank->branch }} Branch,<br><br><br>

        Please find attached a PDF document containing the details of our employees for the upcoming salary disbursement scheduled for {{ $disbursement->salary_date }}. This includes their bank account numbers, salaries, names, and other relevant information.<br><br><br>

        Best regards,<br>
        <span class="bold">{{ $sender->first_name . $sender->last_name }}</span><br>
        {{ $sender->employee->job_title }} @ {{ $company->name }}
    </p>
@endsection
