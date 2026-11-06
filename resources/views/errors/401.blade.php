
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Unauthorized Access | {{ config('app.name', 'Laravel') }}</title>
    @include('layouts.partial.__favicon')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom CSS Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #333333;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            max-width: 500px;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: slide-up 0.5s ease-out forwards;
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: #333333;
            cursor: pointer;
            transition: transform 0.3s;
        }

        h1:hover {
            transform: scale(1.1);
        }

        p {
            font-size: 24px;
            margin-bottom: 40px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x!important;            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 18px;
            transition: background-color 0.3s, color 0.3s;
        }

        .button:hover {
            background-color: #555555;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 style="color: darkred" onclick="window.location.href='{{ route('dashboard') }}'">401 <i class="fa fa-exclamation-circle" aria-hidden="true"></i></h1>
    <p style="color: darkred">Oops! Unauthorized Access.</p>
    <a href="{{ route('dashboard') }}" class="button">Go Dashboard</a>
</div>
</body>
</html>

