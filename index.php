<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Geo Injector Pro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>🌍 Inyector Geográfico Inteligente</h1>
    <p style="color: #7f8c8d;">Los datos se sincronizan con la API y se validan contra la base de datos local.</p>

    <table>
        <thead>
            <tr>
                <th>Región / Subregión</th>
                <th>Tabla Destino</th>
                <th>Cantidad a Inyectar</th>
                <th>Estado de Sincronización</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $regiones = [
                ["Sur América", "sur_america", "subregion/south america"],
                ["Norte América", "norte_america", "subregion/north america"],
                ["Sur Europa", "sur_europa", "subregion/southern europe"],
                ["Norte Europa", "norte_europa", "subregion/northern europe"],
                ["Este Asia", "este_asia", "subregion/eastern asia"],
                ["Sur Asia", "sur_asia", "subregion/southern asia"],
                ["Norte África", "norte_africa", "subregion/northern africa"],
                ["África Central", "sur_africa", "region/africa"],
                ["Oceanía", "oceania", "region/oceania"],
                ["Antártida", "antartida", "region/antarctic"]
            ];
            foreach ($regiones as $r): ?>
            <tr>
                <td><strong><?= $r[0] ?></strong></td>
                <td><code><?= $r[1] ?></code></td>
                <td>
                    <input type="number" id="cant_<?= $r[1] ?>" value="1" min="1" oninput="validar('<?= $r[1] ?>')">
                    <span id="err_<?= $r[1] ?>" class="error-hint"></span>
                </td>
                <td class="action-cell">
                    <button id="btn_<?= $r[1] ?>" class="btn" onclick="lanzar('<?= $r[1] ?>', '<?= $r[2] ?>')" disabled>INYECTAR</button>
                    <span id="msg_<?= $r[1] ?>" class="msg"></span>
                    <small id="max_<?= $r[1] ?>" style="display:block; color: #7f8c8d; margin-top: 8px;">Calculando disponibilidad...</small>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>const regs = <?= json_encode($regiones) ?>;</script>
    <script src="script.js"></script>

</body>
</html>