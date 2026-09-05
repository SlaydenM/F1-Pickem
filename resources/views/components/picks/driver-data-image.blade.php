@props(['driver', 'year', 'alt' => null])

<img
    class="driver-data widget border-[3.75px] border-black rounded-[18.75px] text-[1px] w-[250px] flex aspect-[3/2] justify-center items-center text-center box-border xl:border-[3px] xl:rounded-[15px] xl:w-[200px]"
    src="{{ route('driver.logo', ['year' => $year, 'filename' => $driver->getFileName()]) }}"
    alt="{{ $alt ?? $driver->name }}"
/>
