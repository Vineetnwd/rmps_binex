<input type="file" id="imageInput" accept="image/*">
<br>
<img id="preview" style="max-width: 200px; margin-top: 10px; display: none;">

<script>
document.getElementById('imageInput').addEventListener('change', function(event) {
  const file = event.target.files[0];
  if (!file || !file.type.startsWith('image/')) return;

  const reader = new FileReader();

  reader.onload = function(e) {
    const img = new Image();

    // Show Preview
    const preview = document.getElementById('preview');
    preview.src = e.target.result;
    preview.style.display = 'block';

    img.onload = function() {
      const canvas = document.createElement('canvas');
      const maxWidth = 800;
      const scale = maxWidth / img.width;

      canvas.width = maxWidth;
      canvas.height = img.height * scale;

      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

      canvas.toBlob(function(blob) {
        const formData = new FormData();
        formData.append('image', blob, 'compressed.jpg');

        // ✅ Add hidden fields
        formData.append('user_id', '12345');
        formData.append('token', 'abcXYZ123');
        formData.append('upload_time', new Date().toISOString());

        fetch('upload.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.text())
        .then(result => {
          console.log('Upload success:', result);
        })
        .catch(error => {
          console.error('Upload error:', error);
        });
      }, 'image/jpeg', 0.7);
    };

    img.src = e.target.result;
  };

  reader.readAsDataURL(file);
});
</script>
