<div id="custom-modal-overlay" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <div class="modal-icon">⚠️</div>
        <h3 id="modal-title">Konfirmasi</h3>
        <p id="modal-message">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
        <div class="modal-actions">
            <button id="modal-btn-cancel" class="btn btn-dark">Batal</button>
            <button id="modal-btn-confirm" class="btn btn-danger">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<div id="universalModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <h3 id="modalTitle">Form Aksi</h3>
        <hr>
        <div id="modalBody">
        </div>
        <div class="modal-actions" style="margin-top: 20px;">
            <button type="button" class="btn btn-dark" onclick="closeModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitModalForm()">Simpan Data</button>
        </div>
    </div>
</div>

<script>
    function openModal(title, url) {
        const modal = document.getElementById('universalModal');
        document.getElementById('modalTitle').innerText = title;

        // Fetch konten form dari URL (partial view)
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('modalBody').innerHTML = html;
                modal.style.display = 'flex';
            });
    }

    function closeModal() {
        document.getElementById('universalModal').style.display = 'none';
    }

    function submitModalForm() {
        // Cari form di dalam modal dan submit
        const form = document.querySelector('#modalBody form');
        if (form) form.submit();
    }
</script>
