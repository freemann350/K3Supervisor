<script>
    @if (!old('rules'))
    let ruleCount = 0;
    @endif

    function appendInput(baseDivName, inputName) {
        const baseDiv = document.getElementById(baseDivName);
        const baseInput = document.createElement('div');
        baseInput.classList.add('dynamic-input');
        
        if (baseDivName === 'labels' || baseDivName === 'annotations') {
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

        if (baseDivName === 'rules') {
            baseInput.classList.add('col-md-6', 'mb-4');
            ruleCount++;
            baseInput.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Rule #${ruleCount} Details</h5>
                        <hr>
                        <div class="form-group">
                            <label class="col-form-label">Host name</label>
                            <input type="text" name="rules[${ruleCount}][host]" class="form-control" placeholder="example.com">
                        </div>
                        <div>
                            <h6>Paths *</h6>
                            <button type="button" class="btn btn-dark" onclick="addPath(${ruleCount})">+ Add Path</button>
                            <div id="paths-${ruleCount}"></div>
                        </div>
                        <button type="button" class="btn btn-danger removeInput mt-3"><i class="ti-trash removeInput"></i> Remove Rule</button>
                    </div>
                </div>
            `;
        }

        baseDiv.appendChild(baseInput);
    }

    function addPath(ruleId) {
        const pathDiv = document.createElement('div');
        pathDiv.classList.add('input-group', 'mb-3', 'dynamic-input');
        pathDiv.innerHTML = `
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Path</span>
            </div>
            <input type="text" class="form-control" name="rules[${ruleId}][path][pathName][]" placeholder="/nginx">
            <div class="input-group-prepend">
                <span class="input-group-text">Type</span>
            </div>
            <select class="form-select fix-height" name="rules[${ruleId}][path][pathType][]">
                <option value="Prefix" selected>Prefix</option> 
                <option value="Exact">Exact</option> 
                <option value="ImplementationSpecific">ImplementationSpecific</option> 
            </select>
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">Service</span>
            </div>
            <input type="text" class="form-control" name="rules[${ruleId}][path][serviceName][]" placeholder="my-service">
            <div class="input-group-prepend">
                <span class="input-group-text">Port</span>
            </div>
            <input type="text" class="form-control" name="rules[${ruleId}][path][portNumber][]" placeholder="80">
            <button type="button" class="btn btn-danger removeInput"><i class="ti-trash removeInput"></i></button>
        </div>
        `;
        document.getElementById(`paths-${ruleId}`).appendChild(pathDiv);
    }

    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('removeInput')) {
            event.target.closest('.dynamic-input').remove();
        }
    });
</script>
