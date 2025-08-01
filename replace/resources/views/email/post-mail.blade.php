<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Welcome to IDSC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f8fb;
            padding: 20px;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        h1 {
            color: #2c3e50;
        }

        .credentials {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            font-family: monospace;
        }

        ul {
            padding-left: 20px;
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #7f8c8d;
        }

        a {
            color: #2980b9;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome to Our School Family!</h1>
        <p>Your iDSC Connect Adventure Awaits.</p>

        <p>
            Welcome to <strong>INFOTECH DEVELOPMENT SYSTEMS COLLEGES</strong>! We're so excited to have you as a vital part of our school community, now with enhanced access through <strong>iDSC Connect</strong> – IDSC School Management System.
        </p>

        <p>To get you started, here are your login credentials:</p>

        <div class="credentials">
            <p>🔑 <strong>Your Login Details:</strong></p>
            <p>📧 Email: <strong>{{ $email }}</strong></p>
            <p>🔑 Password: <strong>{{ $password }}</strong></p>
        </div>

        <p><strong>How to Begin Your Adventure:</strong></p>
        <ol>
            <li><strong>Open the Portal:</strong> Click on this magical link to iDSC Connect: <a href="https://idsc.laravel.cloud/">https://idsc.laravel.cloud/</a></li>
            <li><strong>Enter Your Credentials:</strong> Use the email and password provided above.</li>
            <li><strong>Change Account Password:</strong> For enhanced security, we strongly recommend that you navigate to your profile settings immediately upon logging in and change your password to something unique and secure.</li>
        </ol>

        <p>✨ <strong>Tips for Creating a Strong Password:</strong></p>
        <ul>
            <li>Keep it Secret: Your password is like a secret spell. Don't share it with anyone!</li>
            <li>Mix it Up: Use at least 8 characters, including letters (uppercase and lowercase), numbers, and special symbols.</li>
            <li>Be Unique: Avoid common words or easily guessable information like your name.</li>
        </ul>

        <p>💬 <strong>Need Help?</strong> If you have any questions or run into any issues with iDSC Connect, our support team is here to help. You can reach out to us at <a href="mailto:idscollegeincligao@gmail.com">idscollegeincligao@gmail.com</a>.</p>

        <p>We're so excited to have you with us and look forward to all the amazing things you'll achieve this year with iDSC Connect – IDSC School Management System!</p>

        <!-- <p><strong>Best wishes,</strong></p>
        <p><strong>JP DIVINAFLOR</strong><br>
            IT Support Team / System Administration / MEDIA CLUB ADVISER</p> -->

        <p class="footer">P.S. Make sure to explore iDSC Connect for all the latest updates, reports, and management tools! ⭐</p>
    </div>
</body>

</html>