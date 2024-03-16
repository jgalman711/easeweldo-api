@extends('emails.disbursement.layout')

@section('content')
    <p>Dear {{ $bankProvider }},</p>

    <p>
        Please find attached a PDF document containing the details of our employees for the upcoming
        salary disbursement scheduled for {{ $salaryDate }}. This includes their bank account numbers,
        salaries, names, and other relevant information.
    </p>

    <p>Kindly use this information to facilitate the timely and accurate disbursement of salaries to our employees.</p>

    <p>If you need further assistance, feel free to contact us at {{ $companyContactNumber }}.</p>

    <p>Thank you for your attention to this matter.</p>
@endsection
