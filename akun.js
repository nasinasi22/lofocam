// Event listener untuk tombol simpan di halaman edit akun
document.querySelector('#simpanAkun').addEventListener('click', function (e) {
    const username = document.querySelector('#username').value;
    const email = document.querySelector('#email').value;

    if (!username || !email) {
        e.preventDefault();
        alert('Username dan email tidak boleh kosong!');
    }
});
