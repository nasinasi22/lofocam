// Event listener untuk tombol verifikasi barang temuan
document.querySelectorAll('.btn-verifikasi-temuan').forEach(button => {
    button.addEventListener('click', function () {
        const barangId = this.getAttribute('data-id');

        if (confirm('Apakah Anda yakin ingin memverifikasi barang temuan ini?')) {
            fetch(`verifikasi-barang-temuan.php?id=${barangId}`, {
                method: 'POST'
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      alert('Barang temuan berhasil diverifikasi!');
                      location.reload();
                  } else {
                      alert('Gagal memverifikasi barang temuan.');
                  }
              }).catch(err => {
                  alert('Terjadi kesalahan, coba lagi.');
              });
        }
    });
});
