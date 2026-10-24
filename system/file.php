<!-- 1. Logo Upload -->
<input type="file" data-preview="previewLogo" data-input="logo" />
<input type="hidden" name="logo">
<div id="previewLogo" style="display:flex;"></div>

<!-- 2. Profile Upload -->
<input type="file" data-preview="previewProfile" data-input="profile" />
<input type="hidden" name="profile">
<div id="previewProfile" style="display:flex;"></div>

<!-- 3. Multiple Docs Upload -->
<input type="file" multiple data-preview="previewDocs" data-input="docs" />
<input type="hidden" name="docs">
<div id="previewDocs" style="display:flex;"></div>

<!-- Common Progress Bar -->
<div id="progressBarContainer" style="display:none;">
  <progress id="uploadProgress" value="0" max="100"></progress>
</div>

<script>
document.querySelectorAll("input[type='file']").forEach(input => {
  input.addEventListener("change", function () {
    const files = this.files;
    const previewId = this.dataset.preview;
    const inputName = this.dataset.input;

    const previewArea = document.getElementById(previewId);
    const hiddenInput = document.querySelector(`input[name='${inputName}']`);
    const progressBar = document.getElementById('uploadProgress');
    const progressContainer = document.getElementById('progressBarContainer');

    previewArea.innerHTML = '';
    progressBar.value = 0;
    progressContainer.style.display = 'block';

    const formData = new FormData();
    Array.from(files).forEach(file => formData.append('files[]', file));

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'upload.php', true);

    xhr.upload.onprogress = function (e) {
      if (e.lengthComputable) {
        progressBar.value = (e.loaded / e.total) * 100;
      }
    };

    xhr.onload = function () {
      progressContainer.style.display = 'none';
      if (xhr.status === 200) {
        const response = JSON.parse(xhr.responseText);
        const paths = [];

        response.files.forEach(file => {
          const div = document.createElement('div');
          div.style.margin = '5px';

          if (file.type.startsWith('image/')) {
            div.innerHTML = `<img src="${file.url}" height="60"><br><a href="${file.url}" target="_blank">Download</a>`;
          } else {
            div.innerHTML = `<a href="${file.url}" target="_blank">${file.name}</a>`;
          }

          previewArea.appendChild(div);
          paths.push(file.url);
        });

        hiddenInput.value = this.multiple ? paths.join(',') : paths[0];
      } else {
        previewArea.innerHTML = 'Upload failed.';
      }
    };

    xhr.send(formData);
  });
});
</script>
