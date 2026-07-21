<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Case Study Backend</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

@php($frontendUrl = rtrim(config('app.frontend_url'), '/'))

<div class="text-center">
    <h1 class="fw-bold">Case Study Backend</h1>
    <p class="lead">This is the backend with API endpoints.</p>
    <p class="mt-4 text-muted">
        To check out the <strong>React Frontend</strong>, visit
        <a href="{{ $frontendUrl }}" class="link-primary">{{ $frontendUrl }}</a>
    </p>
</div>

</body>
</html>
