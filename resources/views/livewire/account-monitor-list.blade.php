@php
    use Carbon\Carbon;
@endphp

<div id="account-monitor-list">
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
                                        @php
                                            $roles = explode(',', $user->roles); // 分割角色名稱為數組
                                        @endphp
                                        @if (in_array('Monitor', $roles))
                                            <i class="fa-solid fa-crown" style="color: #8C4623"></i>
                                        @endif
                                        {{ $user->username }}
                                    </div>
                                </div>
                            </div>

                            <div class="col col-md">
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
                                            <a href="{{ route('user.monitor.show', ['teacherId' => $user->teacher_user_id, 'id' => $user->id]) }}"
                                                class="btn btn-info rounded-4">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>

                                            <a href="{{ route('user.monitor.edit', ['teacherId' => $user->teacher_user_id, 'id' => $user->id]) }}"
                                                class="btn btn-warning text-dark">
                                                <i class="fa-solid fa-pen-nib"></i>
                                            </a>

                                            <form method="POST"
                                                action="{{ route('user.monitor.destroy', ['teacherId' => $user->teacher_user_id, 'id' => $user->id]) }}">
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

                            <hr class="mb-0">

                            <div class="col col-sm-6 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Student Name:
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $user->student_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Class :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $user->class_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-auto">
                                <div class="row gap-2 d-md-block">
                                    <div class="col-12 text-muted">
                                        Course :
                                    </div>

                                    <div class="col-12 mt-1">
                                        {{ $user->course_name }}
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
