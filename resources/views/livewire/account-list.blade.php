@php
    use Carbon\Carbon;
@endphp

<div id="account-list">
    <div class="row align-items-center g-0 mt-3">
        <div class="col">
            <div class="search-input-group">
                <div class="search-input-icon">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="form-control search-input" placeholder="Search account"
                    wire:keydown.debounce.250ms="filterUser($event.target.value)" wire:model="filter.user">
            </div>
        </div>
    </div>

    <h5 class="my-3">
        <span
            class="badge text-black fw-normal {{ $filter['role'] === null ? 'border' : '' }} ms-sm-2 {{ $filter['role'] === null ? 'text-white' : '' }}"
            wire:click="filterByRole(null)" role="button"
            style="background-color: {{ $filter['role'] === null ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
            All Users
        </span>

        <span
            class="badge text-black fw-normal {{ $filter['role'] === 'SuperAdmin' ? 'border' : '' }} {{ $filter['role'] === 'SuperAdmin' ? 'text-white' : '' }}"
            wire:click="filterByRole('SuperAdmin')" role="button"
            style="background-color: {{ $filter['role'] === 'SuperAdmin' ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
            Super Admin
        </span>

        <span
            class="badge text-black fw-normal {{ $filter['role'] === 'Admin' ? 'border' : '' }} ms-sm-2 {{ $filter['role'] === 'Admin' ? 'text-white' : '' }}"
            wire:click="filterByRole('Admin')" role="button"
            style="background-color: {{ $filter['role'] === 'Admin' ? '#007bff' : '#F4F6FA' }}; box-shadow: 0px 4px 2px RGBA(0, 0, 0, 0.25); border-radius: 10px;">
            Admin
        </span>
    </h5>

    <div class="row g-4 mt-2">
        @foreach ($users as $user)
            <div class="col-12">
                <div class="card border-0 card-shadow px-1">
                    <div class="card-body px-md-4">
                        <div class="row g-2 g-md-3 align-items-center">


                            <div class="col">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Username :
                                    </div>
                                    <div class="col-12 mt-1">
                                        @if ($user->roles->contains('name', 'SuperAdmin'))
                                            <i class="fa-solid fa-crown" style="color: #FFD700"></i>
                                        @endif

                                        @if ($user->roles->contains('name', 'Admin'))
                                            <i class="fa-solid fa-crown" style="color: #C0C0C0"></i>
                                        @endif
                                        {{ $user->username }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Email :
                                    </div>
                                    <div class="col-12 mt-1">
                                        {{ $user->email }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Created At :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ Carbon::parse($user->created_at)->format('d-m-Y h:i A') }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-auto">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Action :
                                    </div>

                                    <div class="col-12 mt-1">
                                        <div class="d-inline-flex gap-3">
                                            <a href="{{ route('user.show', ['id' => $user->id]) }}"
                                                class="btn btn-info rounded-4">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <a href="{{ route('user.edit', ['id' => $user->id]) }}"
                                                class="btn btn-warning text-dark">
                                                <i class="fa-solid fa-pen-nib"></i>
                                            </a>

                                            <form method="POST" action="{{ route('user.destroy', $user->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger"
                                                    onclick="deleteFormConfirmation(event)">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>



    <div class="d-grid mt-4">
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

        @if (count($users) === 0)
            <div class="text-center" wire:loading.remove>
                <img class="no-data-found mt-2" src="{{ asset('img/no-data-found.png') }}">
                <div class="mt-4 h5 text-muted">
                    No data found
                </div>
            </div>
        @endif
    </div>
</div>
