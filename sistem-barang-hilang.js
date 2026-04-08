// Validasi form tambah barang hilang
const submitButton = document.querySelector('button[type="submit"]');
submitButton.addEventListener('click', (e) => {
    const namaBarang = document.querySelector('input[name="nama_barang"]').value;

    if (!namaBarang) {
        e.preventDefault();
        alert('Nama barang tidak boleh kosong!');
    }
});
