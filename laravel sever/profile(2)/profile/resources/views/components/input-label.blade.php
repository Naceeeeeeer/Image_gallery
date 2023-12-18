@props(['value'])

<label  class="block text-blue-300 py-2 font-bold mb-2" {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>
