'use strict';

document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. GLOBAL AJAX TOAST / FLASH MESSAGE SYSTEM
    // =========================================================================
    const getToastContainer = () => {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container';
            document.body.appendChild(container);
        }
        return container;
    };

    window.showToast = (type, message) => {
        const container = getToastContainer();
        const toast = document.createElement('div');

        toast.className = `toast-message toast-${type === 'success' ? 'success' : 'danger'}`;
        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">${type === 'success' ? '✓' : '⚠️'}</span>
                <span class="toast-text">${message}</span>
            </div>
            <button class="toast-close">&times;</button>
        `;

        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    };

    document.body.addEventListener('click', (e) => {
        if (e.target.classList.contains('toast-close')) {
            const toast = e.target.closest('.toast-message');
            if (toast) {
                toast.classList.add('fade-out');
                setTimeout(() => toast.remove(), 300);
            }
        }
    });

    document.querySelectorAll('.toast-message').forEach(toast => {
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    });

    // =========================================================================
    // 2. FORM TAMBAH KERANJANG (EVENT DELEGATION AJAX)
    // =========================================================================
    document.body.addEventListener("submit", async function(e) {

        // 🚀 PERBAIKAN: Memastikan target benar-benar elemen form agar JS tidak crash
        if (e.target && e.target.tagName === 'FORM' && (e.target.classList.contains("form-tambah") || e.target.classList.contains("add-to-cart-form"))) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const tombol = form.querySelector("button[type='submit']");
            const cartCounter = document.getElementById("cart-counter");

            if (!tombol) return;

            const teksAsli = tombol.innerText;
            const warnaAsli = tombol.style.background || "";

            tombol.innerText = "⏳ Menambahkan...";
            tombol.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                });

                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                const result = await response.json();

                if (result.status === "success") {
                    if (cartCounter) cartCounter.innerText = result.total_items;
                    tombol.innerText = "✓ Ditambahkan!";
                    tombol.style.background = "#4CAF50";
                    if (window.showToast) window.showToast('success', result.pesan || "Produk berhasil dimasukkan.");
                } else {
                    throw new Error(result.pesan || "Terjadi kesalahan sistem.");
                }
            } catch (error) {
                tombol.innerText = "❌ Gagal!";
                tombol.style.background = "#f44336";
                if (window.showToast) window.showToast('error', error.message);
            } finally {
                setTimeout(() => {
                    tombol.innerText = teksAsli;
                    tombol.style.background = warnaAsli;
                    tombol.disabled = false;
                }, 1500);
            }
        }
    });

    // =========================================================================
    // 3. GLOBAL MODAL KONFIRMASI (DELEGATION SYSTEM)
    // =========================================================================
    const modalOverlay = document.getElementById('custom-modal-overlay');
    if (modalOverlay) {
        const modalMsg = document.getElementById('modal-message');
        const btnCancel = document.getElementById('modal-btn-cancel');
        const btnConfirm = document.getElementById('modal-btn-confirm');
        let pendingAction = null;

        document.body.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-confirm]');
            if (trigger) {
                e.preventDefault();
                pendingAction = trigger;
                modalMsg.innerText = trigger.getAttribute('data-confirm');
                modalOverlay.style.display = 'flex';
            }
        });

        btnCancel.addEventListener('click', () => {
            modalOverlay.style.display = 'none';
            pendingAction = null;
        });

        btnConfirm.addEventListener('click', () => {
            modalOverlay.style.display = 'none';
            if (pendingAction) {
                // 1. Prioritas Tautan <a> (Untuk tombol hapus individu)
                if (pendingAction.tagName.toLowerCase() === 'a' && pendingAction.href) {
                    window.location.href = pendingAction.href;
                }
                // 2. Prioritas Tombol Submit (Untuk Hapus Massal)
                else {
                    // Cari form: Pertama dari atribut 'form="id"', jika tidak ada baru cari pembungkusnya
                    const formId = pendingAction.getAttribute('form');
                    const form = formId ? document.getElementById(formId) : pendingAction.closest('form');

                    if (form) {
                        form.submit();
                    }
                }
                pendingAction = null;
            }
        });
    }

    // =========================================================================
    // 4. NAVBAR & SIDEBAR TOGGLE
    // =========================================================================
    const menuBtn = document.getElementById('userMenuBtn');
    const dropdown = document.getElementById('userDropdown');
    if (menuBtn && dropdown) {
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && e.target !== menuBtn) dropdown.classList.remove('show');
        });
    }

    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const adminWrapper = document.getElementById('adminWrapper');
    if (sidebarToggleBtn && adminWrapper) {
        sidebarToggleBtn.addEventListener('click', () => adminWrapper.classList.toggle('collapsed'));
    }

    // =========================================================================
    // 5. DETAIL PRODUK GALLERY & STICKY CART
    // =========================================================================
    const galleryContainer = document.querySelector('.gallery-thumbnails');
    const mainProductImage = document.getElementById('mainProductImage');
    if (galleryContainer && mainProductImage) {
        galleryContainer.addEventListener('click', (e) => {
            const clickedThumb = e.target.closest('.thumb-item');
            if (clickedThumb) {
                mainProductImage.src = clickedThumb.src;
                galleryContainer.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
                clickedThumb.classList.add('active');
            }
        });
    }

    const btnStickyCart = document.getElementById('btn-sticky-cart');
    if (btnStickyCart) {
        btnStickyCart.addEventListener('click', () => {
            const form = document.querySelector('.add-to-cart-form');
            if (form) {
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            }
        });
    }

    // =========================================================================
    // 6. CHECKOUT ALAMAT TOGGLE
    // =========================================================================
    document.body.addEventListener('change', (e) => {
        if (e.target.classList.contains('radio-alamat-toggle')) {
            const showFormAlamatBaru = (e.target.value === 'baru');
            const uiAlamatBaru = document.getElementById('ui-alamat-baru');
            if (uiAlamatBaru) {
                uiAlamatBaru.style.display = showFormAlamatBaru ? 'block' : 'none';
                uiAlamatBaru.querySelectorAll('input, textarea').forEach(i => i.required = showFormAlamatBaru);
            }
        }
    });

    // =========================================================================
    // 7. UNIVERSAL ASYNC MODAL SYSTEM
    // =========================================================================
    const universalModal = document.getElementById('universalModal');
    if (universalModal) {
        window.openModal = function(title, url) {
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            if (modalTitle) modalTitle.innerText = title;
            fetch(url)
                .then(r => { if (!r.ok) throw new Error("Gagal memuat form."); return r.text(); })
                .then(html => {
                    if (modalBody) {
                        modalBody.innerHTML = html;
                        universalModal.style.display = 'flex';
                    }
                })
                .catch(err => { if (window.showToast) window.showToast('error', err.message); });
        };

        const btnUnivCancel = document.getElementById('btn-universal-cancel');
        const btnUnivSubmit = document.getElementById('btn-universal-submit');

        if (btnUnivCancel) btnUnivCancel.addEventListener('click', () => universalModal.style.display = 'none');
        if (btnUnivSubmit) btnUnivSubmit.addEventListener('click', () => {
            const form = document.querySelector('#modalBody form');
            if (form) form.submit();
        });
    }

    // =========================================================================
    // 8. MISC HELPERS
    // =========================================================================
    const btnPrint = document.getElementById('btnPrint');
    if (btnPrint) btnPrint.addEventListener('click', () => window.print());

    const btnBack = document.getElementById('btnBack');
    if (btnBack) btnBack.addEventListener('click', () => window.history.back());

    const browserFingerprint = document.getElementById('browserFingerprint');
    if (browserFingerprint) {
        const fp = navigator.userAgent + " | Res: " + screen.width + "x" + screen.height + " | Color: " + screen.colorDepth + " | Lang: " + navigator.language;
        browserFingerprint.value = btoa(fp);
    }

    // =========================================================================
    // 9. BATCH DELETE (HAPUS MASSAL) UNTUK KERANJANG & ADMIN
    // =========================================================================
    const selectAllCb = document.getElementById('selectAllCb');
    const itemCbs = document.querySelectorAll('.item-checkbox');
    const btnBatchDelete = document.getElementById('btnBatchDelete');

    if (selectAllCb && itemCbs.length > 0) {
        const updateBatchBtn = () => {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            if (btnBatchDelete) {
                btnBatchDelete.disabled = checkedCount === 0;
                btnBatchDelete.innerText = checkedCount > 0 ? `🗑️ Hapus Terpilih (${checkedCount})` : '🗑️ Hapus Terpilih';
            }
        };

        selectAllCb.addEventListener('change', function() {
            itemCbs.forEach(cb => cb.checked = this.checked);
            updateBatchBtn();
        });

        document.body.addEventListener('change', function(e) {
            if (e.target.classList.contains('item-checkbox')) {
                const allChecked = document.querySelectorAll('.item-checkbox:checked').length === itemCbs.length;
                selectAllCb.checked = allChecked;
                updateBatchBtn();
            }
        });
    }
});
