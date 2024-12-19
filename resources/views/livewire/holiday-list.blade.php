@php
    use Carbon\Carbon;
@endphp

<div>
    <div class="table-responsive">
        <table class="table table-striped table-borderless">
            <thead>
                <tr>
                    <th class="bg-primary text-white p-2 px-sm-3">Holidays</th>
                    <th class="bg-primary text-white p-2 px-sm-3 text-left text-sm-center" style="width: 150px;">Date
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holidays as $holiday)
                    <tr role="button">
                        <td class="p-2 px-sm-3 d-flex align-self-center text-wrap text-break"
                            style="max-width: 300px; min-width: 210px;">
                            <div class="p-1" style="min-width: 50px; height: 27px;border: 1px solid black;">
                                <div class="w-100 h-100" style="background-color: {{ $holiday->background_color }};">
                                </div>
                            </div>
                            <span class="ms-2">{{ $holiday->title }}</span>
                        </td>

                        <td class="p-2 px-sm-3 text-left text-sm-center" style="min-width: 210px;width: 100%;">
                            <span
                                class="{{ $holiday->date_from && Carbon::parse($holiday->date_from)->isToday() ? 'text-success' : '' }}">
                                {{ $holiday->date_from && $holiday->date_to ? Carbon::parse($holiday->date_from)->format('Y/m/d') . ' - ' . Carbon::parse($holiday->date_to)->format('Y/m/d') : '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>


    <div class="d-grid">
        <div x-intersect.full="$wire.loadMore()">
        </div>

        <div wire:loading>
            <div class="d-flex justify-content-center">
                <div class="more-loader-pulse-container">
                    <div class="more-loader-pulse-bubble more-loader-pulse-bubble-1"></div>
                    <div class="more-loader-pulse-bubble more-loader-pulse-bubble-2"></div>
                    <div class="more-loader-pulse-bubble more-loader-pulse-bubble-3"></div>
                </div>
            </div>
        </div>

        @if (count($holidays) === 0)
            <div class="alert text-center" wire:loading.remove>
                <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
                <div class="mt-4 h5 text-muted">
                    No data found
                </div>
            </div>
        @endif
    </div>
</div>
