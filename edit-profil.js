// Validasi form edit profil
document.querySelector('#formEditProfil').addEventListener('submit', function (e) {
    const nama = document.querySelector('#nama').value;
    const email = document.querySelector('#email').value;

    if (!nama || !email) {
        e.preventDefault();
        alert('Nama dan email tidak boleh kosong!');
    }
});
