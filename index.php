<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Geo Injector Pro</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 40px; text-align: center; }
        table { width: 90%; margin: auto; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; }
        th { background: #2c3e50; color: white; }
        .btn { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn:disabled { background: #bdc3c7; }
        .msg { font-weight: bold; margin-left: 10px; transition: opacity 0.5s; opacity: 0; }
        .error-hint { color: red; font-size: 11px; display: block; }
    </style>
</head>
<body>
    <h1>🌍 Inyector Geográfico Inteligente</h1>
    <table>
        <thead>
            <tr>
                <th>Región</th>
                <th>Tabla SQL</th>
                <th>Cantidad</th>
                <th>Acción</th>
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
                ["África Sub", "sur_africa", "region/africa"],
                ["Oceanía", "oceania", "region/oceania"],
                ["Antártida", "antartida", "region/antarctic"]
            ];
            foreach ($regiones as $r): ?>
            <tr>
                <td><strong><?= $r[0] ?></strong></td>
                <td><code><?= $r[1] ?></code></td>
                <td>
                    <input type="number" id="cant_<?= $r[1] ?>" value="1" min="1" oninput="validar('<?= $r[1] ?>')">
                    <small id="max_<?= $r[1] ?>" style="display:block; color: #7f8c8d;">Cargando...</small>
                    <span id="err_<?= $r[1] ?>" class="error-hint"></span>
                </td>
                <td>
                    <button id="btn_<?= $r[1] ?>" class="btn" onclick="lanzar('<?= $r[1] ?>', '<?= $r[2] ?>')" disabled>INYECTAR</button>
                    <span id="msg_<?= $r[1] ?>" class="msg"></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    // Al cargar la página, obtenemos los límites reales de la API
    window.onload = () => {
        const regs = <?= json_encode($regiones) ?>;
        regs.forEach(r => {
            fetch(`https://restcountries.com/v3.1/${r[2]}`)
            .then(res => res.json())
            .then(data => {
                const total = data.length;
                document.getElementById('cant_'+r[1]).max = total;
                document.getElementById('max_'+r[1]).innerText = `(Disponibles: ${total})`;
                document.getElementById('btn_'+r[1]).disabled = false;
            });
        });
    };

    function validar(tabla) {
        const input = document.getElementById('cant_'+tabla);
        const btn = document.getElementById('btn_'+tabla);
        const err = document.getElementById('err_'+tabla);
        const max = parseInt(input.max);
        const val = parseInt(input.value);

        if (val > max) {
            err.innerText = "¡No hay tantos!";
            btn.disabled = true;
        } else if (val < 1 || isNaN(val)) {
            err.innerText = "Mínimo 1";
            btn.disabled = true;
        } else {
            err.innerText = "";
            btn.disabled = false;
        }
    }

    function lanzar(tabla, filtro) {
        const msg = document.getElementById('msg_'+tabla);
        const cant = document.getElementById('cant_'+tabla).value;
        
        msg.innerText = "Procesando...";
        msg.style.opacity = "1";
        msg.style.color = "orange";

        const form = new FormData();
        form.append('tabla', tabla);
        form.append('filtro', filtro);
        form.append('cantidad', cant);

        fetch('procesar.php', { method: 'POST', body: form })
        .then(res => res.text())
        .then(data => {
            msg.innerText = data;
            msg.style.color = "green";
            setTimeout(() => { 
                msg.style.opacity = "0";
                setTimeout(() => msg.innerText = "", 500);
            }, 3000);
        })
        .catch(() => {
            msg.innerText = "Error de red";
            msg.style.color = "red";
        });
    }
    </script>
</body>
</html>