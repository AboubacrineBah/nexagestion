@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'col' => 'col-md-6'])
<div class="{{ $col }}">
    <label class="form-label @if($required) required @endif" for="{{ $name }}">{{ $label }}</label>
    <input class="form-control" id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           value="{{ old($name, $value) }}" @if($required) required @endif>
</div>