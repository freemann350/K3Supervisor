<script>
    function appendInput(baseDivName, inputName) {
        const baseDiv = document.getElementById(baseDivName);
        const baseInput = document.createElement('div');
        baseInput.classList.add('dynamic-input');
        
        if (baseDivName === 'labels' || baseDivName === 'annotations' || baseDivName === 'selectorLabels') {
            baseInput.innerHTML = `
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Key</span>
                </div>
                <input type="text" class="form-control fix-height" name="key_${inputName}">
                <div class="input-group-prepend">
                    <span class="input-group-text">Value</span>
                </div>
                <input type="text" class="form-control fix-height" name="value_${inputName}">
                <button type="button" class="btn btn-danger fix-height removeInput"><i class="ti-trash removeInput"></i></button>
            </div>
            `;
        }

        if (baseDivName === 'ports') {
            baseInput.innerHTML = `
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Name</span>
                </div>
                <input type="text" class="form-control fix-height" name="portName[]">
                <div class="input-group-prepend">
                    <span class="input-group-text">Protocol</span>
                </div>
                <select class="form-select fix-height" name="protocol[]">
                    <option value="TCP" selected>TCP</option> 
                    <option value="UDP">UDP</option> 
                </select>
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Port</span>
                </div>
                <input type="text" class="form-control fix-height" name="port[]">
                <div class="input-group-prepend">
                    <span class="input-group-text">Target</span>
                </div>
                <input type="text" class="form-control fix-height" name="target[]">
                <div class="input-group-prepend">
                    <span class="input-group-text">Node</span>
                </div>
                <input type="text" class="form-control fix-height" name="nodePort[]">
                <button type="button" class="btn btn-danger removeInput fix-height"><i class="ti-trash removeInput"></i></button>
            </div>
            `;
        }


        baseDiv.appendChild(baseInput);
    }

    function handleChange(id) {
        const elementId = document.getElementById(id).value;
        const parametersDiv = document.getElementById(id+'Parameter');
        console.log(elementId);
        parametersDiv.innerHTML = '';

        const update = document.createElement('div');
        if (elementId === 'ExternalName') {
            update.innerHTML = `
                <div class="form-group">    
                    <label class="col-sm-3 col-form-label">External Name</label>
                    <input type="text" name="externalName" class="form-control" placeholder="my-name.domain.test">
                </div>
            `;
        } else if ((elementId === 'ClientIP')) {
            update.innerHTML = `
                <div class="form-group">    
                    <label>Session Affinity Timeout</label>
                    <input type="text" name="sessionAffinityTimeoutSeconds" class="form-control" placeholder="10800">
                </div>
            `;
        }
        parametersDiv.appendChild(update);
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('removeInput')) {
            event.target.closest('.dynamic-input').remove();
        }
    });
</script>
