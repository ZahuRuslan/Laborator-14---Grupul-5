const modalNota = document.getElementById('modal-nota');
const modalStergere = document.getElementById('modal-stergere');
const titluInput = document.getElementById('titlu');
const originalInput = document.getElementById('original');
const continutInput = document.getElementById('continut');
const contor = document.getElementById('contor');

function deschide(modal) {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function inchide(modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function inchideTot() {
    inchide(modalNota);
    inchide(modalStergere);
}

function actualizeazaContor() {
    contor.textContent = continutInput.value.length;
}

// Notă nouă
document.getElementById('btn-nou').onclick = function () {
    document.getElementById('titlu-modal').textContent = 'Notă nouă';
    originalInput.value = '';
    titluInput.value = '';
    continutInput.value = '';
    actualizeazaContor();
    deschide(modalNota);
};

// Editare
document.querySelectorAll('.btn-editeaza').forEach(function (buton) {
    buton.onclick = function () {
        document.getElementById('titlu-modal').textContent = 'Editează nota';
        originalInput.value = buton.dataset.titlu;
        titluInput.value = buton.dataset.titlu;
        continutInput.value = buton.dataset.continut;
        actualizeazaContor();
        deschide(modalNota);
    };
});

// Ștergere (cu confirmare)
document.querySelectorAll('.btn-sterge').forEach(function (buton) {
    buton.onclick = function () {
        document.getElementById('stergere-nume').textContent = buton.dataset.titlu;
        document.getElementById('stergere-fisier').value = buton.dataset.titlu + '.txt';
        deschide(modalStergere);
    };
});

// Anulează, click pe fundal, tasta Escape
document.querySelectorAll('.btn-inchide').forEach(function (buton) {
    buton.onclick = inchideTot;
});
[modalNota, modalStergere].forEach(function (modal) {
    modal.onclick = function (e) {
        if (e.target === modal) {
            inchideTot();
        }
    };
});
document.onkeydown = function (e) {
    if (e.key === 'Escape') {
        inchideTot();
    }
};

continutInput.oninput = actualizeazaContor;

// Căutare live după titlu (fără Enter)
const cautare = document.getElementById('cautare');
const carduri = document.querySelectorAll('.note-card');
const gol = document.getElementById('gol');

cautare.oninput = function () {
    const text = cautare.value.trim().toLowerCase();
    let vizibile = 0;

    carduri.forEach(function (card) {
        const potrivire = card.dataset.titlu.includes(text);
        card.classList.toggle('hidden', !potrivire);
        if (potrivire) {
            vizibile++;
        }
    });

    gol.classList.toggle('hidden', vizibile > 0);
};