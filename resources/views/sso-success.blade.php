<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSO Success</title>
</head>
<body>
    <p>Authentication successful. You can close this window now.</p>
    <script>
        if (window.opener) {
            // Redirect the parent window to the admin dashboard
            window.opener.location.href = '/admin';
            // Close the current popup window
            window.close();
        } else {
            // If it's not a popup, redirect the current window
            window.location.href = '/admin';
        }
    </script>
</body>
</html>
