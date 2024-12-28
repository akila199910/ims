
@extends('layouts.business')

@section('title')
Manage Write Offs
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.writeoff') }}"> Manage Write Offs</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Add new Write Off</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.writeoff') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
            </div>
        </div>
    </div>

    <div class="row">
        <form id="submitForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">

                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Add new Write Off</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="category">Warehouse<span class="text-danger">*</span> </label>
                                    <select class="form-control select2   warehouse" name="warehouse" id="warehouse">
                                        <option value="">Select Warehouse Name</option>
                                        @foreach ( $warehouse as $item)
                                            <option value="{{ $item->id }}">{{ Str::limit($item->name,30) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_warehouse"></small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="items_data">

                                </div>
                            </div>

                        </div>
                        @if (Auth::user()->hasPermissionTo('Create_WriteOff'))
                                <div class="col-12">
                                    <div class="doctor-submit text-end">
                                        <button type="submit"
                                            class="btn btn-primary text-uppercase submit-form me-2">Create</button>
                                    </div>
                                </div>
                         @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })

        })

        $('#warehouse').change(function(e) {
            e.preventDefault();

            var data = {
                'warehouse_id': $(this).val()
            }

            $('#loader').show()

            $.ajax({
                type: "POST",
                url: "{{ route('business.writeoff.item_filter') }}",
                data: data,
                success: function(response) {
                    console.log(response);
                    $('#loader').hide();
                    $('.items_data').html('');
                    $('.items_data').html(response);
                },
                error: function(data) {
                    $('#loader').hide()
                    alert('something went to wrong')
                }
            });
        });

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.writeoff.create') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("#loader").hide();
                    errorClear()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                                $('#' + key).addClass('is-invalid');
                            }
                        });
                    } else {
                        successPopup(response.message, response.route)
                    }
                },
                statusCode: {
                    401: function() {
                        window.location.href =
                            '{{ route('login') }}'; //or what ever is your login URI
                    },
                    419: function() {
                        window.location.href =
                            '{{ route('login') }}'; //or what ever is your login URI
                    },
                },
                error: function(data) {
                    someThingWrong();
                }
            });
        });

        function errorClear()
        {
            $('#product').removeClass('is-invalid')
            $('.err_product').text('')

            $('#warehouse').removeClass('is-invalid')
            $('.err_warehouse').text('')

            $('#qty').removeClass('is-invalid')
            $('.err_qty').text('')

            $('#reason').removeClass('is-invalid')
            $('.err_reason').text('')

        }
    </script>
@endsection

