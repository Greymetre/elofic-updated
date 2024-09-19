<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background-image: url('{{ asset("assets/img/winp.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .coming-soon-container {
            background: rgba(0, 0, 0, 0.5);
            padding: 40px;
            text-align: center;
            color: white;
            border-radius: 10px;
        }

        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="coming-soon-container">
        <h1>Coming Soon</h1>
        <p>This module is under construction. We'll be here soon with our new awesome module.</p>
        <a href="{{ url()->previous() }}" class="btn btn-primary btn-back">Go Back</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
