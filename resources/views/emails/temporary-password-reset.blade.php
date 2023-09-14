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
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img class="logo" src="{{ env('AUTH_APP_URL') }}/assets/images/easeweldo-logo.png" alt="Easeweldo Logo">
            </div>
            <div class="thank-you">
                <h1>Temporary Password Reset</h1>
            </div>
            <div style="text-align: center; padding: 10px;">
                <p>Your temporary password:</p>
                <div style="text-align: center; background-color: #f4f4f4; padding: 10px; display: inline-block;">
                    <p style="margin:0; font-size: 24px; font-weight: bold;">{{ $temporaryPassword }}</p>
                </div>
                <p style="font-size: 12px; color: #777;">This password is valid for 1 hour. Please go to your profile and change it upon login.</p>
            </div>
        </div>
    </body>
</html>
