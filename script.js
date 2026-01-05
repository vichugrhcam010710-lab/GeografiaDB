window.onload = () => { actualizarLimites(); };

function actualizarLimites() {
    regs.forEach(r => {
        Promise.all([
            fetch(`https://restcountries.com/v3.1/${r[2]}`).then(res => res.json()),
            fetch(`procesar.php?get_total=${r[1]}`).then(res => res.json())
        ])
            .then(([apiData, dbData]) => {
                const totalApi = apiData.length;
                const totalDb = dbData.total;
                const disponibles = totalApi - totalDb;

                const input = document.getElementById('cant_' + r[1]);
                const info = document.getElementById('max_' + r[1]);
                const btn = document.getElementById('btn_' + r[1]);

                input.max = disponibles;
                info.innerHTML = `Disponibles: <b>${disponibles}</b> <br><small>(DB: ${totalDb} / API: ${totalApi})</small>`;

                if (disponibles <= 0) {
                    input.value = 0;
                    input.disabled = true;
                    btn.disabled = true;
                    btn.innerText = "COMPLETO";
                    info.style.color = "#e74c3c";
                } else {
                    btn.disabled = false;
                }
            });
    });
}

function validar(tabla) {
    const input = document.getElementById('cant_' + tabla);
    const btn = document.getElementById('btn_' + tabla);
    const err = document.getElementById('err_' + tabla);
    const max = parseInt(input.max);
    const val = parseInt(input.value);

    if (val > max) {
        err.innerText = `Límite: ${max}`;
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
    const msg = document.getElementById('msg_' + tabla);
    const btn = document.getElementById('btn_' + tabla);
    const cant = document.getElementById('cant_' + tabla).value;

    btn.disabled = true;
    msg.innerText = "⏳ Inyectando...";
    msg.style.opacity = "1";
    msg.style.color = "#f39c12";

    const form = new FormData();
    form.append('tabla', tabla);
    form.append('filtro', filtro);
    form.append('cantidad', cant);

    fetch('procesar.php', { method: 'POST', body: form })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                msg.innerText = data.message;
                msg.style.color = "#27ae60";
                setTimeout(() => { location.reload(); }, 1000);
            } else {
                msg.innerText = data.message;
                msg.style.color = "#c0392b";
                btn.disabled = false;
                setTimeout(() => { msg.style.opacity = "0"; }, 1000);
            }
        })
        .catch(error => {
            msg.innerText = "❌ Error de conexión";
            msg.style.color = "#c0392b";
            btn.disabled = false;
        });
}