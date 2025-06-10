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
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-danger">
        Failed to fetch data from OpenSenseMap.
    </div>
<?php endif; ?>
