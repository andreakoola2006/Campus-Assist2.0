// script.js - Simple Image Preview (Pure PHP Compatible)
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    
    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {

    // Remove old preview if exists
    const old = imageInput.parentNode.querySelector('.img-wrapper');
    if (old) old.remove();

    // Create wrapper
    const wrapper = document.createElement('div');
    wrapper.classList.add("img-wrapper");
    wrapper.style.position = "relative";
    wrapper.style.width = "150px";
    wrapper.style.marginTop = "10px";

    // Create preview image
    const previewImg = document.createElement('img');
    previewImg.src = e.target.result;
    previewImg.style.width = '100%';
    previewImg.style.height = 'auto';
    previewImg.style.borderRadius = '5px';
    previewImg.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';

    // Create ❌ button
    const removeBtn = document.createElement('span');
    removeBtn.innerHTML = "✕";
    removeBtn.style.position = "absolute";
    removeBtn.style.top = "-8px";
    removeBtn.style.right = "-8px";
    removeBtn.style.background = "red";
    removeBtn.style.color = "white";
    removeBtn.style.borderRadius = "50%";
    removeBtn.style.padding = "2px 6px";
    removeBtn.style.cursor = "pointer";
    removeBtn.style.fontSize = "12px";

    // Remove image on click
    removeBtn.addEventListener("click", function () {
        imageInput.value = "";   // clear file input
        wrapper.remove();        // remove preview
    });

    // Append elements
    wrapper.appendChild(previewImg);
    wrapper.appendChild(removeBtn);

    imageInput.parentNode.appendChild(wrapper);
};

reader.readAsDataURL(file);
        }
    });
});