<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>SmartSchedule — {{ config('app.name', 'SmartSchedule') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            *, *::before, *::after { box-sizing: border-box; }
            body {
                font-family: 'Inter', sans-serif;
                background: #f7f7f8;
                color: #0f0f10;
                min-height: 100vh;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Subtle grid pattern background */
            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image:
                    linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px);
                background-size: 32px 32px;
                pointer-events: none;
                z-index: 0;
            }

            .auth-wrapper {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 440px;
                padding: 20px;
            }

            .auth-card {
                background: #ffffff;
                border: 1px solid #e5e5e7;
                border-radius: 20px;
                padding: 40px;
                box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            }

            .auth-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                margin-bottom: 32px;
            }

            .auth-logo-icon {
                width: 42px;
                height: 42px;
                background: #0f0f10;
                border-radius: 11px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .auth-logo-text {
                font-size: 1.35rem;
                font-weight: 800;
                color: #0f0f10;
                letter-spacing: -0.02em;
            }

            label {
                display: block;
                font-size: 0.78rem;
                font-weight: 600;
                color: #6c6c70;
                text-transform: uppercase;
                letter-spacing: 0.07em;
                margin-bottom: 7px;
            }

            input[type=email], input[type=password], input[type=text] {
                width: 100%;
                background: #ffffff;
                border: 1px solid #d1d1d6;
                border-radius: 10px;
                padding: 12px 16px;
                color: #0f0f10;
                font-size: 0.925rem;
                font-family: 'Inter', sans-serif;
                transition: border-color 0.2s ease, box-shadow 0.2s ease;
                outline: none;
            }

            input:focus {
                border-color: #0f0f10;
                box-shadow: 0 0 0 3px rgba(15,15,16,0.08);
            }

            input::placeholder { color: #aeaeb2; }

            .auth-form-group { margin-bottom: 20px; }

            .btn-submit {
                width: 100%;
                background: #0f0f10;
                color: white;
                border: none;
                padding: 13px 20px;
                border-radius: 11px;
                font-weight: 700;
                font-size: 0.95rem;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                margin-top: 8px;
            }

            .btn-submit:hover {
                background: #2a2a2e;
                transform: translateY(-1px);
                box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            }

            .auth-link {
                color: #3a3a3c;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 500;
                transition: color 0.2s;
                border-bottom: 1px solid #d1d1d6;
            }
            .auth-link:hover { color: #0f0f10; border-bottom-color: #0f0f10; }

            .error-msg {
                color: #e11d48;
                font-size: 0.8rem;
                margin-top: 5px;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .status-msg {
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                color: #166534;
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 0.85rem;
                margin-bottom: 20px;
            }

            .auth-divider {
                text-align: center;
                margin: 24px 0;
                position: relative;
                color: #aeaeb2;
                font-size: 0.8rem;
            }
            .auth-divider::before, .auth-divider::after {
                content: '';
                position: absolute;
                top: 50%;
                width: 40%;
                height: 1px;
                background: #e5e5e7;
            }
            .auth-divider::before { left: 0; }
            .auth-divider::after  { right: 0; }

            input[type=checkbox] { accent-color: #0f0f10; width: 15px; height: 15px; cursor: pointer; }
        </style>
    </head>
    <body>
        <div class="auth-wrapper">
            {{ $slot }}
        </div>
    </body>
</html>
