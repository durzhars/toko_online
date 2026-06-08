'use strict';

document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll(".form-tambah");
    forms.forEach((form) => {
        form.addEventListener("submit", async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const tombol = this.querySelector("button");
            const cartCounter = document.getElementById("cart-counter");
            const teksAsli = tombol.innerText;
            const warnaAsli = tombol.style.background || "";
            tombol.innerText = "⏳ Menambahkan...";
            tombol.disabled = true;

            try {
                const response = await fetch(this.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest" // Header standar penanda AJAX
                    },
                });

                if (!response.ok) {
                    throw new Error(`HTTP Error: ${response.status}`);
                }

                const result = await response.json();
                if (result.status === "success") {
                    if (cartCounter) {
                        cartCounter.innerText = result.total_items;
                    }
                    tombol.innerText = "✓ Ditambahkan!";
                    tombol.style.background = "#4CAF50"; // Hijau Success
                } else {
                    throw new Error(result.pesan || "Terjadi kesalahan sistem.");
                }

            } catch (error) {
                console.error("Cart Error:", error);
                tombol.innerText = "❌ Gagal!";
                tombol.style.background = "#f44336"; // Merah Danger

            } finally {
                setTimeout(() => {
                    tombol.innerText = teksAsli;
                    tombol.style.background = warnaAsli;
                    tombol.disabled = false;
                }, 1500);
            }
        });
    });

    const toasts = document.querySelectorAll('.toast-message');
    toasts.forEach(toast => {
        // Hilangkan toast otomatis setelah 4 detik
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    });

    const modalOverlay = document.getElementById('custom-modal-overlay');

    if (modalOverlay) {
        const modalMsg = document.getElementById('modal-message');
        const btnCancel = document.getElementById('modal-btn-cancel');
        const btnConfirm = document.getElementById('modal-btn-confirm');

        let pendingAction = null; // Menyimpan elemen yang memicu modal

        // Event Delegation: Tangkap semua klik di dalam body
        document.body.addEventListener('click', (e) => {
            // Cari apakah elemen yang diklik (atau parent-nya) punya atribut data-confirm
            const trigger = e.target.closest('[data-confirm]');

            if (trigger) {
                e.preventDefault(); // Hentikan aksi form.submit atau link.href

                pendingAction = trigger;
                modalMsg.innerText = trigger.getAttribute('data-confirm');
                modalOverlay.style.display = 'flex'; // Tampilkan Modal
            }
        });

        // Jika tombol Batal diklik
        btnCancel.addEventListener('click', () => {
            modalOverlay.style.display = 'none';
            pendingAction = null;
        });

        // Jika tombol Ya diklik
        btnConfirm.addEventListener('click', () => {
            modalOverlay.style.display = 'none';

            if (pendingAction) {
                // Skenario 1: Jika trigger berada di dalam sebuah Form (misal: tombol Hapus)
                const form = pendingAction.closest('form');
                if (form) {
                    form.submit(); // Lanjutkan submit form
                }
                // Skenario 2: Jika trigger adalah link <a> (misal: tombol Logout)
                else if (pendingAction.href) {
                    window.location.href = pendingAction.href; // Lanjutkan navigasi
                }

                pendingAction = null;
            }
        });
    }
});
