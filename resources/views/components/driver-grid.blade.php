@props(['drivers'])

<div class="grid grid-cols-3 gap-3 md:hidden justify-center" id="driver-grid-sm">
    @foreach($drivers as $driver)
        <div class="driver-grid-item" data-driver-id="{{ $driver->id }}">
            <x-driver-card :driver="$driver" size="sm" :draggable="true" />
        </div>
    @endforeach
</div>
<div class="hidden md:flex gap-2 flex-wrap justify-center" id="driver-grid-md">
    @foreach($drivers as $driver)
        <div class="driver-grid-item" data-driver-id="{{ $driver->id }}">
            <x-driver-card :driver="$driver" size="md" :draggable="true" />
        </div>
    @endforeach
</div>
