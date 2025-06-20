{{-- resources/views/admin/settings/partials/field.blade.php --}}

@switch($setting->type)
    @case('text')
    @case('email')
        <input type="{{ $setting->type }}" 
               name="{{ $setting->key }}" 
               id="{{ $setting->key }}"
               class="form-control @error($setting->key) is-invalid @enderror" 
               value="{{ old($setting->key, $setting->value) }}"
               placeholder="{{ $setting->description ?? $setting->label }}"
               {{ $setting->is_required ? 'required' : '' }}>
        @break

    @case('number')
        <input type="number" 
               name="{{ $setting->key }}" 
               id="{{ $setting->key }}"
               class="form-control @error($setting->key) is-invalid @enderror" 
               value="{{ old($setting->key, $setting->value) }}"
               placeholder="{{ $setting->description ?? $setting->label }}"
               {{ $setting->is_required ? 'required' : '' }}
               @if(str_contains($setting->validation_rules ?? '', 'min:'))
                   min="{{ explode('min:', $setting->validation_rules)[1] ? explode('|', explode('min:', $setting->validation_rules)[1])[0] : '' }}"
               @endif
               @if(str_contains($setting->validation_rules ?? '', 'max:'))
                   max="{{ explode('max:', $setting->validation_rules)[1] ? explode('|', explode('max:', $setting->validation_rules)[1])[0] : '' }}"
               @endif>
        @break

    @case('textarea')
        <textarea name="{{ $setting->key }}" 
                  id="{{ $setting->key }}"
                  class="form-control @error($setting->key) is-invalid @enderror" 
                  rows="3"
                  placeholder="{{ $setting->description ?? $setting->label }}"
                  {{ $setting->is_required ? 'required' : '' }}>{{ old($setting->key, $setting->value) }}</textarea>
        @break

    @case('select')
        <select name="{{ $setting->key }}" 
                id="{{ $setting->key }}"
                class="form-control @error($setting->key) is-invalid @enderror"
                {{ $setting->is_required ? 'required' : '' }}>
            @if(!$setting->is_required)
                <option value="">-- Select Option --</option>
            @endif
            @if($setting->options)
                @foreach($setting->options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" 
                            {{ old($setting->key, $setting->value) == $optionValue ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            @endif
        </select>
        @break

    @case('boolean')
        <div class="form-check form-switch">
            <input type="hidden" name="{{ $setting->key }}" value="0">
            <input class="form-check-input @error($setting->key) is-invalid @enderror" 
                   type="checkbox" 
                   name="{{ $setting->key }}" 
                   id="{{ $setting->key }}"
                   value="1"
                   {{ old($setting->key, $setting->value) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $setting->key }}">
                Enable {{ $setting->label }}
            </label>
        </div>
        @break

    @case('file')
        <div class="file-input-wrapper">
            <input type="file" 
                   name="{{ $setting->key }}" 
                   id="{{ $setting->key }}"
                   class="form-control @error($setting->key) is-invalid @enderror"
                   accept="@if(str_contains($setting->validation_rules ?? '', 'image'))image/*@endif">
            
            @if($setting->value)
                <div class="current-file mt-2">
                    <small class="text-muted">Current file:</small>
                    @if(str_contains($setting->validation_rules ?? '', 'image'))
                        <div class="current-image">
                            <img src="{{ asset('storage/' . $setting->value) }}" 
                                 alt="{{ $setting->label }}" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px; max-height: 100px;">
                        </div>
                    @else
                        <div class="current-file-name">
                            <a href="{{ asset('storage/' . $setting->value) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="material-icons">file_download</i> View Current File
                            </a>
                        </div>
                    @endif
                    <div class="mt-1">
                        <label class="form-check-label">
                            <input type="checkbox" name="remove_{{ $setting->key }}" value="1" class="form-check-input">
                            Remove current file
                        </label>
                    </div>
                </div>
            @endif
        </div>
        @break

    @case('json')
        @if($setting->options)
            {{-- Multiple select for JSON arrays --}}
            <select name="{{ $setting->key }}[]" 
                    id="{{ $setting->key }}"
                    class="form-control @error($setting->key) is-invalid @enderror"
                    multiple>
                @php
                    $selectedValues = old($setting->key, json_decode($setting->value ?? '[]', true) ?? []);
                @endphp
                @foreach($setting->options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" 
                            {{ in_array($optionValue, $selectedValues) ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple options</small>
        @else
            {{-- Text area for JSON input --}}
            <textarea name="{{ $setting->key }}" 
                      id="{{ $setting->key }}"
                      class="form-control @error($setting->key) is-invalid @enderror font-monospace" 
                      rows="4"
                      placeholder='{"key": "value"}'
                      {{ $setting->is_required ? 'required' : '' }}>{{ old($setting->key, $setting->value) }}</textarea>
            <small class="form-text text-muted">Enter valid JSON format</small>
        @endif
        @break

    @default
        <input type="text" 
               name="{{ $setting->key }}" 
               id="{{ $setting->key }}"
               class="form-control @error($setting->key) is-invalid @enderror" 
               value="{{ old($setting->key, $setting->value) }}"
               placeholder="{{ $setting->description ?? $setting->label }}"
               {{ $setting->is_required ? 'required' : '' }}>
@endswitch

@error($setting->key)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

@if($setting->validation_rules && !$errors->has($setting->key))
    <small class="form-text text-muted">
        @if(str_contains($setting->validation_rules, 'required'))
            <span class="text-danger">*</span> Required field. 
        @endif
        @if(str_contains($setting->validation_rules, 'max:'))
            Max: {{ explode('max:', $setting->validation_rules)[1] ? explode('|', explode('max:', $setting->validation_rules)[1])[0] : '' }}
        @endif
        @if(str_contains($setting->validation_rules, 'min:'))
            Min: {{ explode('min:', $setting->validation_rules)[1] ? explode('|', explode('min:', $setting->validation_rules)[1])[0] : '' }}
        @endif
    </small>
@endif