(function () {
    const dropzone   = document.getElementById('dropzone');
    const input      = document.getElementById('csv');
    const submitBtn  = document.getElementById('submitBtn');
    const uploadBtn  = document.getElementById('uploadBtn');
    const fileLabel  = document.getElementById('selectedFile');

    input.addEventListener('change', () => {
        const file = input.files[0] ?? null;
        fileLabel.textContent  = file ? '📄 ' + file.name : '';
        fileLabel.style.display = file ? 'block' : 'none';
        submitBtn.disabled = !file;
        if (uploadBtn) uploadBtn.disabled = !file;
    });

    dropzone.addEventListener('dragover',  (e) => { e.preventDefault(); dropzone.classList.add('drag'); });
    dropzone.addEventListener('dragleave', ()  => dropzone.classList.remove('drag'));
    dropzone.addEventListener('drop',      (e) => {
        e.preventDefault();
        dropzone.classList.remove('drag');
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        }
    });
})();
