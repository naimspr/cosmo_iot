<?php
// Fetch sensor box data from OpenSenseMap API
$apiUrl = 'https://api.opensensemap.org/boxes/623f6e24c4de74001c66f4cb';
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IoT Project</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            padding: 40px 20px;
        }
        .flex-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
            align-items: flex-start;
        }
        .access-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            min-width: 300px;
            max-width: 400px;
            flex: 1 1 350px;
            height: fit-content;
        }
        .access-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .button-group button {
            padding: 12px 28px;
            font-size: 1.1rem;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: white;
            transition: background-color 0.3s ease;
            min-width: 120px;
        }
        .button-group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="flex-container">

    <!-- OpenSenseMap Box (original layout from box.php) -->
    <div class="container p-0" style="flex: 1 1 500px; max-width: 400px; max-height: fit-content">
        
        <?php if (isset($data)): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-3"><?= htmlspecialchars($data['name'] ?? 'Unnamed') ?></h2>

                    <?php
                    $location = 'Unknown';
                    if (isset($data['loc']['coordinates'])) {
                        $location = implode(', ', $data['loc']['coordinates']);
                    } elseif (isset($data['loc'][0]['geometry']['coordinates'])) {
                        $location = implode(', ', $data['loc'][0]['geometry']['coordinates']);
                    }
                    ?>
                    <p><strong>Location:</strong> <?= htmlspecialchars($location) ?></p>
                    <p><strong>Exposure:</strong> <?= htmlspecialchars($data['exposure'] ?? 'N/A') ?></p>

                    <h4 class="mt-4">Sensors:</h4>
                    <ul class="list-group">
                        <?php if (!empty($data['sensors']) && is_array($data['sensors'])): ?>
                            <?php foreach ($data['sensors'] as $sensor): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><strong><?= htmlspecialchars($sensor['title'] ?? 'Unnamed Sensor') ?></strong></span>
                                    <span>
                                        <?= htmlspecialchars($sensor['lastMeasurement']['value'] ?? 'N/A') ?>
                                        <?= htmlspecialchars($sensor['unit'] ?? '') ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item">No sensors found.</li>
                        <?php endif; ?>
                    </ul>

                    <button class="btn btn-primary mt-4 w-100"
                        onclick="window.open('https://opensensemap.org/explore/623f6e24c4de74001c66f4cb', '_blank')">
                        Visit OpenSenseMap
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger mt-3">Failed to fetch data from OpenSenseMap.</div>
        <?php endif; ?>
    </div>

    <!-- User Access Box -->
    <div class="access-box">
        <h2>User Access</h2>
        <div class="button-group">
            <button onclick="location.href='login.php'">Login</button>
            <button onclick="location.href='signup.php'">Sign Up</button>
        </div>
    </div>

</div>

</body>
</html>
