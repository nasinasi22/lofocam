// Konfirmasi sebelum menghapus data
function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus data ini?');
}

// Event listener untuk tombol logout
const logoutButton = document.querySelector('.btn-logout');
logoutButton.addEventListener('click', () => {
    if (confirm('Apakah Anda yakin ingin logout?')) {
        window.location.href = '../PHP/logoutSubmit.php';
    }
});
