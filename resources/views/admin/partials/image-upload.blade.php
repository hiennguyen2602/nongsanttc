@props([
    'name',
    'label',
    'existing' => null,
    'existingField' => null,
    'required' => false,
    'hint' => null,
    'imgClass' => '',
])

<div class="mb-3" x-data="featuredImage(@js($existing), @js($label))">
    <label class="form-label" @if($attributes->has('for')) for="{{ $attributes->get('for') }}" @endif>{{ $label }}@if ($required) *@endif</label>

    <div class="image-grid mb-2" x-show="existing || newImage" style="display:none">
        <template x-if="existing">
            <div class="image-card">
                <img :src="existing.url" alt="" @if($imgClass) class="{{ $imgClass }}" @endif>
                <div class="image-card-bar">
                    <button type="button" class="image-card-remove" @click="removeExisting()">Xóa</button>
                </div>
            </div>
        </template>
        <template x-if="newImage">
            <div class="image-card">
                <img :src="newImage.url" alt="" @if($imgClass) class="{{ $imgClass }}" @endif>
                <div class="image-card-bar">
                    <button type="button" class="image-card-remove" @click="removeNew()">Xóa</button>
                </div>
            </div>
        </template>
    </div>

    <input
        type="file"
        x-ref="fileInput"
        name="{{ $name }}"
        accept="image/*"
        @change="addFile($event)"
        @if ($required) required @endif
        class="form-control"
    >

    @error($name)
        <p class="field-error">{{ $message }}</p>
    @enderror

    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @else
        <p class="mt-1 text-xs text-slate-500">{{ image_upload_hint() }}</p>
    @endif

    @if ($existingField)
        <template x-if="existing">
            <input type="hidden" name="{{ $existingField }}" :value="existing.path">
        </template>
    @endif
</div>
