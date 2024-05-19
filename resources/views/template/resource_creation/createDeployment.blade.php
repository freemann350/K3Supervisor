<script>
    @if (!old('containers'))
    let containerCount = 0;
    @endif
    
    function appendInput(baseDivName, inputName) {
        const baseDiv = document.getElementById(baseDivName);
        const baseInput = document.createElement('div');
        
        if (baseDivName === 'labels' || baseDivName === 'annotations' || baseDivName === 'matchLabels' || baseDivName === 'templateLabels') {
            baseInput.innerHTML = `
            <div class="input-group mb-3 dynamic-input">
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

        if (baseDivName === 'containers') {
            baseInput.classList.add('col-md-6', 'mb-4', 'dynamic-input');
            containerCount++;
            baseInput.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Container #${containerCount} Details</h5>
                        <hr>
                        <div class="form-group">
                            <label class="col-form-label">Container name *</label>
                            <input type="text" name="containers[${containerCount}][name]" class="form-control" placeholder="my-container">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Container image *</label>
                            <input type="text" name="containers[${containerCount}][image]" class="form-control" placeholder="my-container">
                        </div>
                        <div>
                            <h6>Ports</h6>
                            <button type="button" class="btn btn-dark" onclick="addPort(${containerCount})">Add Port</button>
                            <div id="ports-${containerCount}"></div>
                        </div>
                        <div>
                            <h6>Environment Variables</h6>
                            <button type="button" class="btn btn-dark" onclick="addEnv(${containerCount})">Add Environment Variable</button>
                            <div id="env-${containerCount}"></div>
                        </div>
                        <button type="button" class="btn btn-danger removeInput mt-3"><i class="ti-trash removeInput"></i> Remove Container</button>
                    </div>
                </div>
            `;
        }

        baseDiv.appendChild(baseInput);
    }

    function addPort(containerId) {
        const portDiv = document.createElement('div');
        portDiv.classList.add('input-group', 'mb-3', 'dynamic-input');
        portDiv.innerHTML = `
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Port</span>
            </div>
            <input type="text" class="form-control" name="containers[${containerId}][ports][]" placeholder="80">
            <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
        </div>
        `;
        document.getElementById(`ports-${containerId}`).appendChild(portDiv);
    }

    function addEnv(containerId) {
        const envDiv = document.createElement('div');
        envDiv.classList.add('input-group', 'mb-3', 'dynamic-input');
        envDiv.innerHTML = `
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Key</span>
                </div>
                <input type="text" class="form-control" name="containers[${containerId}][env][key][]" placeholder="Key">
                <div class="input-group-prepend">
                    <span class="input-group-text">Value</span>
                </div>
                <input type="text" class="form-control" name="containers[${containerId}][env][value][]" placeholder="Value">
                <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
            </div>
        `;
        document.getElementById(`env-${containerId}`).appendChild(envDiv);
    }

    function handleStrategyChange() {
        const strategyType = document.getElementById('strategy').value;
        const parametersDiv = document.getElementById('strategyParameters');

        parametersDiv.innerHTML = '';

        if (strategyType === 'RollingUpdate') {
            const strategyRollingUpdate = document.createElement('div');

            strategyRollingUpdate.innerHTML = `
                <div class="form-group">    
                    <label for="maxUnavailable">Max Unavailable</label>
                    <input type="text" id="maxUnavailable" name="maxUnavailable" class="form-control" placeholder="1">
                </div>
                <div class="form-group">    
                    <label for="maxSurge">Max Surge</label>
                    <input type="text" id="maxSurge" name="maxSurge" class="form-control" placeholder="1">
                </div>
            `;

            parametersDiv.appendChild(strategyRollingUpdate);
        }
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('removeInput')) {
            event.target.closest('.dynamic-input').remove();
        }
    });
</script>