@php
    use Carbon\Carbon;
@endphp

@extends('admin/layout/layout')

@section('page_title', 'Web Transfer')

@section('content')
    <div id="admin-course">
        <div class="row mt-2">
            <div class="col text-muted">
                <ul class="breadcrumb mb-2 mb-md-1">
                    <li class="breadcrumb-item">
                        Dashboard
                    </li>
                    <li class="breadcrumb-item">
                        Class Management
                    </li>
                </ul>

            </div>
        </div>

        <div class="row align-items-center my-1">
            <div class="col">
                <h4 class="header-title">Manage Class</h4>
            </div>
        </div>

        <div>
            @livewire('attendance-list')
        </div>
    </div>



    <div class="row">
        <div class="col-12 col-md-5">
            @livewire('admin.web-transfer-list')
        </div>

        <div class="col-12 col-md-7">
            <form action="{{ route('admin.web_transfer.store') }}" id="form" method="POST">
                @csrf

                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <textarea class="form-control" name="table_content" id="table_content" rows="10" required>{{ old('table_content') }}</textarea>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="domain_id">Domain</label>
                            <select class="form-select" id="domain_id" name="domain_id" required style="width:100%;">
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="date">Date</label>

                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ Carbon::now()->toDateString() }}" placeholder="Enter date" required>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-primary text-white rounded-4"
                        onclick="calculate()">calculate</button>
                </div>

            </form>
        </div>
    </div>



    <div class="modal fade" id="transfer-summary-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Transfer Summary
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="bg-primary text-white p-2 px-sm-3">Customer</th>
                                    <th class="bg-primary text-white text-end p-2 px-sm-3" style="width: 1%;">Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="transfer-summary-modal-row">

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end fw-bold border-bottom-0 p-2 px-sm-3">Total:</td>
                                    <td class="text-end fw-bold border-bottom-0 p-2 px-sm-3"
                                        id="transfer-summary-modal-total">0.0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-danger" id="transfer-summary-not-zero-warning">
                        <span class="fw-bold">WARNING:</span> Total is not ZERO, please check!
                    </div>




                    <div class="d-flex float-end gap-2">
                        <button type="submit" class="btn btn-primary text-white rounded-4" id="submit-btn"
                            form="form">Submit</button>
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary rounded-4">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#form').validate({
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                invalidHandler: function(form, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        notifier.show('Warning!', 'Please check all the fields.', 'warning',
                            '', 4000);
                    }
                }
            })

            $("#domain_id").select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: 'Select domain',
                ajax: {
                    url: "{{ route('admin.domain.select_search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var query = {
                            search_term: params.term,
                            page: params.page,
                            except_transaction_date: $('#date').val()
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    text: item.name,
                                    id: item.id,
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },

                }
            });
        });


        function calculate() {
            if ($('#form').valid()) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.web_transfer.calculate') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        table_content: $('#table_content').val(),
                        domain_id: $('#domain_id').val(),
                    },
                    success: function(result) {
                        $("#transfer-summary-modal-row").html('');

                        let total = 0;

                        result.forEach(data => {
                            let amount = parseFloat(data.amount);
                            let amountFormatted = amount.toFixed(2);

                            if ($(`#customer-summary-${data.user_id}`).length == 0) {
                                $("#transfer-summary-modal-row").append(`
                                    <tr>
                                        <td class="p-2 px-sm-3">${data.username}</td>
                                        <td id="customer-summary-${data.user_id}" class="text-end p-2 px-sm-3 ${amount < 0 ? 'text-danger' : ''}">
                                            ${amountFormatted}
                                        </td>
                                    </tr>
                                `);
                            } else {

                                let currentAmount = parseFloat($(`#customer-summary-${data.user_id}`)
                                    .text());
                                currentAmount += amount;

                                $(`#customer-summary-${data.user_id}`).text(currentAmount.toFixed(2))
                                    .toggleClass('text-danger', currentAmount < 0);
                            }

                            total += amount;
                        });

                        $("#transfer-summary-modal-total").text(total.toFixed(2));

                        if (total != 0) {
                            $("#transfer-summary-not-zero-warning").removeClass('d-none');
                        } else {
                            $("#transfer-summary-not-zero-warning").addClass('d-none');
                        }

                        if (total < 0) {
                            $("#transfer-summary-modal-total").addClass('text-danger');
                        } else {
                            $("#transfer-summary-modal-total").removeClass('text-danger');
                        }

                        $("#transfer-summary-modal").modal('show');

                        if (result.length == 0) {
                            $("#submit-btn").addClass('d-none');
                        } else {
                            $("#submit-btn").removeClass('d-none');
                        }
                    },
                });
            }
        }
    </script>

    @stack('scripts')
@endsection
