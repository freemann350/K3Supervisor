<div class="form-group">
    <label class="col-sm-3 col-form-label">Labels</label>
    <div class="col-sm-12" id="labels">
        <button type="button" class="btn btn-dark" onClick="appendInput('labels', 'labels[]')">+ Add Label</button>
        @if(old('key_labels'))
            @foreach(old('key_labels') as $index => $key)
                <div class="input-group mb-3 dynamic-input">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Key</span>
                    </div>
                    <input type="text" class="form-control @error("key_labels.{$index}") is-invalid @enderror" name="key_labels[]" value="{{ $key }}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Value</span>
                    </div>
                    <input type="text" class="form-control @error("value_labels.{$index}") is-invalid @enderror" name="value_labels[]" value="{{ old('value_labels')[$index] }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger removeInput"><i class="ti-trash dynamic-input"></i></button>
                    </div>
                    @error("key_labels.{$index}")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @error("value_labels.{$index}")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endforeach
        @endif
    </div>
</div>
<div class="form-group">
    <label class="col-sm-12 col-form-label">Annotations</label>
    <div class="col-sm-12" id="annotations">
        <button type="button" class="btn btn-dark" onClick="appendInput('annotations', 'annotations[]', 2)">+ Add Annotation</button>
        @if(old('key_annotations'))
            @foreach(old('key_annotations') as $index => $key)
                <div class="input-group mb-3 dynamic-input">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Key</span>
                    </div>
                    <input type="text" class="form-control @error("key_annotations.{$index}") is-invalid @enderror" name="key_annotations[]" value="{{ $key }}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Value</span>
                    </div>
                    <input type="text" class="form-control @error("value_annotations.{$index}") is-invalid @enderror" name="value_annotations[]" value="{{ old('value_annotations')[$index] }}">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger removeInput"><i class="ti-trash dynamic-input"></i></button>
                    </div>
                    @error("key_annotations.{$index}")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @error("value_annotations.{$index}")
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endforeach
        @endif
    </div>
</div>