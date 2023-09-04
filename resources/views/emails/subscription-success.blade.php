<!DOCTYPE html>
<html>
<head>
    <style>
        /* Add some basic styling for the email */
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-top: 5px solid #007bff;
        }
        .header {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 10px;

        }
        .logo {
            max-width: 250px;
        }
        .thank-you {
            margin-top: 20px;
            font-size: 18px;
            text-align: center;
        }
        .subscription-summary {
            margin-top: 30px;
            font-size: 16px;
        }
        .bank-accounts {
            margin-top: 30px;
        }
        .bank {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .bank-logo {
            max-width: 100px;
            margin-right: 30px;
        }
        .bank-info {
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="{{ env('AUTH_APP_URL') }}/assets/images/easeweldo-logo.png" alt="Easeweldo Logo">
        </div>
        <div class="thank-you">
            <h1>Thank You for Joining Us!</h1>
            <p>We are delighted to welcome you to Easeweldo! Your subscription request to our payroll management service has been received, and we're thrilled to have you on board.</p>
        </div>
        <div class="subscription-summary">
            <p>Here's a summary of your subscription:</p>
            <ul>
                <li>Subscription Type: {{ $subscription->title }}</li>
                <li>Subscription Start Date: {{ date('F j, Y', strtotime($company_subscription->start_date)) }}</li>
                <li>Subscription End Date: {{ date('F j, Y', strtotime($company_subscription->end_date)) }}</li>
            </ul>
        </div>
        <div class="bank-accounts">
            <p>You can deposit your payment to one of the following bank accounts:</p>
            @foreach ($payment_methods as $paymentMethod)
            <div class="bank">
                <img class="bank-logo" src="{{ env('AUTH_APP_URL') }}/{{ $paymentMethod->logo }}" alt="Bank 3 Logo">
                <div class="bank-info">
                    <p>Bank Name: {{ $paymentMethod->bank_name }}</p>
                    <p>Account Number: {{ $paymentMethod->account_number }}</p>
                    <p>Account Holder: {{ $paymentMethod->account_name }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <p class="">
            Once we receive the payment, we will set your company's account subscription accordingly.
        </p>
    </div>
</body>
</html>
