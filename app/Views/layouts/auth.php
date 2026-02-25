<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : 'Authentication' ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous" />
    <style>
        :root {
            --cobalt-950: #03122b;
            --cobalt-900: #07224b;
            --cobalt-800: #0c3774;
            --cobalt-700: #1450a8;
            --cobalt-600: #2a66c5;
            --metal-100: #e7effa;
            --metal-200: #c7d5ea;
            --metal-300: #9fb4d1;
            --text-light: #eef4ff;
            --text-dark: #10203c;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
            background:
                radial-gradient(circle at 18% 18%, rgba(174, 209, 255, 0.26), transparent 45%),
                linear-gradient(140deg, var(--cobalt-950) 0%, var(--cobalt-900) 42%, var(--cobalt-700) 100%);
            position: relative;
            overflow: hidden;
            color: var(--text-light);
        }
        body::before {
            content: "";
            position: fixed;
            inset: -20%;
            background:
                linear-gradient(115deg, rgba(255, 255, 255, 0) 28%, rgba(255, 255, 255, 0.15) 40%, rgba(255, 255, 255, 0) 54%),
                repeating-linear-gradient(160deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0.02) 3px, transparent 3px, transparent 12px);
            pointer-events: none;
            z-index: 0;
        }
        .auth-container {
            background:
                linear-gradient(155deg, rgba(23, 57, 114, 0.92), rgba(10, 34, 73, 0.94));
            border: 1px solid rgba(198, 217, 245, 0.25);
            border-radius: 14px;
            box-shadow:
                0 20px 42px rgba(1, 10, 28, 0.62),
                inset 0 1px 0 rgba(255, 255, 255, 0.28),
                inset 0 -1px 0 rgba(135, 165, 211, 0.16);
            backdrop-filter: blur(4px);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
        }
        .auth-title {
            text-align: center;
            margin-bottom: 30px;
            color: var(--text-light);
            letter-spacing: 0.02em;
            font-weight: 700;
        }
        .form-control {
            background: rgba(247, 251, 255, 0.96);
            border: 1px solid var(--metal-300);
            color: var(--text-dark);
        }
        .form-control:focus {
            border-color: #7ea5df;
            box-shadow: 0 0 0 0.2rem rgba(98, 153, 235, 0.3);
        }
        label, .text-muted, small {
            color: #dbe8ff !important;
        }
        .btn-primary {
            border: none;
            background: linear-gradient(145deg, #4b86de, #245ab5 55%, #123f88);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.35);
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background: linear-gradient(145deg, #5a95eb, #2f66bf 55%, #184894);
        }
        .btn-auth-social {
            border: 1px solid rgba(201, 218, 243, 0.35);
            background: linear-gradient(145deg, rgba(222, 235, 255, 0.18), rgba(127, 162, 212, 0.22));
            color: var(--metal-100);
        }
        .btn-auth-social:hover {
            color: #ffffff;
            border-color: rgba(217, 230, 250, 0.6);
            background: linear-gradient(145deg, rgba(222, 235, 255, 0.26), rgba(127, 162, 212, 0.3));
        }
        .auth-container a {
            color: #aaceff;
        }
        .auth-container a:hover {
            color: #d0e4ff;
        }
        .alert {
            border: 1px solid transparent;
        }
        .alert-danger {
            background: rgba(136, 32, 45, 0.22);
            border-color: rgba(238, 133, 148, 0.4);
            color: #ffd7dd;
        }
        .alert-info {
            background: rgba(58, 95, 155, 0.28);
            border-color: rgba(157, 185, 226, 0.36);
            color: #dce9ff;
        }
        hr {
            border-color: rgba(195, 214, 241, 0.28);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h2 class="auth-title"><?= isset($title) ? esc($title) : 'Welcome' ?></h2>
        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
