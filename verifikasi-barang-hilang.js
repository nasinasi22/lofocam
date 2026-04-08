// Event listener untuk tombol verifikasi barang hilang
document.querySelectorAll('.btn-verifikasi').forEach(button => {
    button.addEventListener('click', function () {
        const barangId = this.getAttribute('data-id');

        if (confirm('Apakah Anda yakin ingin memverifikasi barang ini?')) {
            fetch(`verifikasi-barang-hilang.php?id=${barangId}`, {
                method: 'POST'
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      alert('Barang berhasil diverifikasi!');
                      location.reload();
                  } else {
                      alert('Gagal memverifikasi barang.');
                  }
              }).catch(err => {
                  alert('Terjadi kesalahan, coba lagi.');
              });
        }
    });
});
