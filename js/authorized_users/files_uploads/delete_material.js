document.querySelectorAll('.delete-material-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const materialId = this.dataset.materialId;

        if (!confirm('Are you sure?')) return;

        fetch('/delete_material', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'material_id=' + encodeURIComponent(materialId)
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    this.closest('.material-item').remove();
                } else {
                    alert('Error: ' + result.error);
                }
            });
    });
});