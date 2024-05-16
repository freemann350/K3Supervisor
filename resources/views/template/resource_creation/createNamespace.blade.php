<script>
    function appendInput(baseDivName, inputName) {
        const baseDiv = document.getElementById(baseDivName);
        const baseInput = document.createElement('div');
        
        if (baseDivName === 'labels' || baseDivName === 'annotations') {
            baseInput.innerHTML = `
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Key</span>
                </div>
                <input type="text" class="form-control" name="key_${inputName}">
                <div class="input-group-prepend">
                    <span class="input-group-text">Value</span>
                </div>
                <input type="text" class="form-control" name="value_${inputName}">
                <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
            </div>
            `;
        }
        
        if (baseDivName === 'finalizers') {
            baseInput.innerHTML = `
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Key</span>
                </div>
                <input type="text" class="form-control" name="${inputName}">
                <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
            </div>
            `;
        }
        baseDiv.appendChild(baseInput);
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('removeInput')) {
            event.target.closest('.input-group').remove();
        }
    });
</script>