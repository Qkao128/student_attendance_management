<div>
    <div class="row align-items-center g-0 mb-4">
        <div class="col">
            <div class="search-input-group">
                <div class="search-input-icon">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="form-control search-input rounded-4" placeholder="Search username"
                    wire:keydown.debounce.250ms="filterUsername($event.target.value)" wire:model="filter.username">
            </div>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-link text-secondary" onclick="toggleFilter('#filter')">
                <i class="fa-solid fa-filter"></i>
            </button>
        </div>
    </div>


    <div id="filter" class="filter-popup-wraper d-none">
        <div class="filter-popup-content">
            <form wire:submit.prevent="applyFilter" id='filter-form'>
                <div class="filter-popup-body">
                    <h3 class="fw-bold text-center">Filter</h3>

                    <button type="button" class="btn btn-link text-dark filter-popup-close-btn p-0"
                        onclick="toggleFilter('#filter')">
                        <i class="fa-solid fa-xmark"></i>
                    </button>


                    <div class="row mt-3">
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-username">Username</label>
                                <input type="text" class="form-control" id="filter-username"
                                    wire:model="filter.username" placeholder="Enter username">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-phone_no">Phone No</label>
                                <input type="text" class="form-control" id="filter-phone_no"
                                    wire:model="filter.phone_no" placeholder="Enter phone no">
                            </div>
                        </div>


                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-3" wire:ignore>
                                <label class="form-label" for="filter-currency_id">Currency</label>
                                <select class="form-select" id="filter-currency_id" style="width:100%;">
                                </select>
                            </div>
                        </div>


                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-show_formula">Show Formula</label>
                                <select class="form-control form-select" id="filter-show_formula"
                                    wire:model="filter.show_formula">
                                    <option value="">Please select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label" for="filter-is_disabled">Status</label>
                                <select class="form-control form-select" id="filter-is_disabled"
                                    wire:model="filter.is_disabled">
                                    <option value="">Please select</option>
                                    <option value="0">Active</option>
                                    <option value="1">Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-popup-footer">
                    <div class="row g-2 p-3">
                        <div class="col-4 col-lg-6">
                            <button type="button" class="btn btn-danger text-white rounded-4 btn-lg w-100"
                                wire:click="resetFilter()" onclick="toggleFilter('#filter')">
                                Reset
                            </button>
                        </div>
                        <div class="col-8 col-lg-6">
                            <button type="submit" class="btn btn-primary text-white rounded-4 btn-lg w-100"
                                onclick="toggleFilter('#filter')">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @foreach ($customers as $customer)
            <div class="col-12">
                <a href="{{ route('admin.customer.show', ['id' => $customer->id]) }}" class="text-decoration-none">
                    <div class="card border-0 rounded-4 card-shadow card-hover"
                        style="background-color: {{ $customer->is_disabled ? '#e8e8e8' : '#ffffff' }};">
                        <div class="card-body px-md-4">
                            <div class="row g-2 g-md-3 align-items-center">

                                <div class="col-auto">
                                    <div class="d-flex gap-2 d-md-block">
                                        <div class="fw-bold">
                                            {{ $customer->user_username }}
                                        </div>

                                        <div class="text-muted">
                                            {{ $customer->user_phone_no }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-auto">
                                    <div class="badge bg-primary">
                                        {{ $customer->currency }}
                                    </div>

                                    @if ($customer->show_formula)
                                        <div class="badge bg-success">
                                            Show Formula
                                        </div>
                                    @else
                                        <div class="badge bg-danger">
                                            Hide Formula
                                        </div>
                                    @endif

                                    @if ($customer->is_disabled)
                                        <div class="badge bg-danger d-md-none">
                                            Disabled
                                        </div>
                                    @endif
                                </div>

                                <div class="col-12 col-md-auto">
                                    Last Transaction:
                                    @if ($customer->last_transaction_date)
                                        {{ Carbon::parse($customer->last_transaction_date)->format('d-m-Y') }}
                                    @else
                                        <div class="badge bg-danger">
                                            None
                                        </div>
                                    @endif
                                </div>

                                @if ($customer->is_disabled)
                                    <div class="col-auto ms-auto d-none d-md-block">
                                        <div class="badge bg-danger">
                                            Disabled
                                        </div>
                                    </div>
                                @endif

                                @if ($customer->user_remark)
                                    <div class="col-12 mt-md-0">
                                        <hr class="my-2 my-md-3">
                                    </div>

                                    <div class="col-12 text-muted mt-0">
                                        Remark:
                                        <div>
                                            {{ $customer->user_remark }}
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                </a>

            </div>
        @endforeach
    </div>

    <div class="d-grid mt-3">
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

        @if (empty($customers))
            <div class="text-center" wire:loading.remove>
                <img class="no-data-found-icon" src="{{ asset('img/no-data-found.png') }}">
                <div class="mt-4 h5 text-muted">
                    No data found
                </div>
            </div>
        @endif
    </div>
</div>
