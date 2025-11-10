<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proxy Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #e53e3e;
            margin-bottom: 1rem;
        }
        p {
            color: #666;
            margin-bottom: 1.5rem;
        }
        .refresh-btn {
            background-color: #3182ce;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .refresh-btn:hover {
            background-color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Proxy Connection Error</h1>
        <p>Unable to connect to the Next.js frontend server.</p>
        <p>Please make sure the development server is running on port 3000.</p>
        <button class="refresh-btn" onclick="location.reload()">Refresh Page</button>
    </div>
</body>
</html>
