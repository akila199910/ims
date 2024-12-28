@extends('layouts.business')

@section('title')
Manage Vendors
@endsection

@section('content')
    <div class="page-header">
        <div class="row">
            <div class="col-sm-8">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business.suppliers') }}">Manage Vendors</a></li>
                    <li class="breadcrumb-item"><i class="feather-chevron-right"></i></li>
                    <li class="breadcrumb-item active">Update Vendor</li>
                </ul>
            </div>
            <div class="col-sm-4 text-end">
                <a href="{{ route('business.suppliers') }}" class="btn btn-primary btn-lg me-2" style='width:100px'>Back</a>
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
                                    <h4>Vendor Information - {{ $supplier['supplier_info']['supplier_code'] }}</h4>
                                </div>
                            </div>
                            <input type="hidden" name="id" value="{{$supplier['supplier_info']['id']}}">
                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="supplier_name">Vendor Name <span class="text-danger">*</span> </label>
                                    <input type="text" name="supplier_name" class="form-control supplier_name" id="supplier_name"
                                        maxlength="190" value="{{ $supplier['supplier_info']['name'] }}">
                                    <small class="text-danger font-weight-bold err_supplier_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="supplier_address">Address <span class="text-danger">*</span></label>
                                    <input type="text" name="supplier_address" class="form-control supplier_address" id="supplier_address"
                                        maxlength="300" value="{{ $supplier['supplier_info']['address'] }}">
                                    <small class="text-danger font-weight-bold err_supplier_address"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label" for="email">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control email" id="email"
                                        maxlength="190" value="{{ $supplier['supplier_info']['email'] }}">
                                    <small class="text-danger font-weight-bold err_email"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="contact">Contact <span class="text-danger">*</span></label>
                                    <input type="text" name="contact" class="form-control contact number_only_val"
                                        maxlength="10" id="contact" value="{{$supplier['supplier_info']['contact']}}" >
                                    <small class="text-danger font-weight-bold err_contact"></small>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block select-gender">
                                    <label class="gen-label" for="status">Status Inactive/Active</label>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status" name="status" {{$supplier['supplier_info']['status'] == 1 ? 'checked' : ''}} class="check">
                                        <label for="status" class="checktoggle">checkbox</label>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="agreement_document">Agreement Document</label>
                                    <input class="upload-path form-control" disabled />
                                    <div class="upload">
                                        <input type="file" name="agreement_document" accept=".pdf, .doc, .docx"
                                        class="form-control agreement_document" id="agreement_document">
                                        <span class="custom-file-label" id="file-label">Choose File...</span>
                                    </div>
                                    <small class="text-danger font-weight-bold err_agreement_document"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label text-uppercase"  for="date_of_agree">Date Of Agreement<span class="text-danger">*</span></label>
                                    <input type="date" name="date_of_agree" value="{{$supplier['supplier_info']['date_of_agree']}}" max="{{ date('Y-m-d') }}"  class="form-control datepicker date_of_agree" id="date_of_agree">
                                    <small class="text-danger font-weight-bold err_date_of_agree"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label text-uppercase" for="date_agree_exp">Date Of Exp<span class="text-danger">*</span></label>
                                    <input type="date" name="date_agree_exp"  value="{{$supplier['supplier_info']['date_agree_exp']}}"  class="form-control datepicker date_agree_exp" id="date_agree_exp">
                                    <small class="text-danger font-weight-bold err_date_agree_exp"></small>
                                </div>
                            </div>

                            <hr>

                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Vendor Payment Information</h4>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="account_name">Account Name <span class="text-danger">*</span></label>
                                    <input type="text" name="account_name" class="form-control account_name"
                                        id="account_name" maxlength="190" value="{{$supplier['payment_information']['account_name']}}">
                                    <small class="text-danger font-weight-bold err_account_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label" for="bank_name">Bank Name <span
                                            class="text-danger">*</span></label>
                                    <input type="bank_name" name="bank_name" class="form-control bank_name"
                                        id="bank_name" maxlength="190" value="{{$supplier['payment_information']['bank_name']}}">
                                    <small class="text-danger font-weight-bold err_bank_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label" for="branch_name">Branch Name <span
                                            class="text-danger">*</span></label>
                                    <input type="branch_name" name="branch_name" class="form-control branch_name"
                                        id="branch_name" maxlength="190" value="{{$supplier['payment_information']['branch_name']}}">
                                    <small class="text-danger font-weight-bold err_branch_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="account_number">Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="account_number"
                                        class="form-control account_number number_only_val"
                                        id="account_number" maxlength="190" value="{{$supplier['payment_information']['account_number']}}">
                                    <small class="text-danger font-weight-bold err_account_number"></small>
                                </div>
                            </div> --}}


                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="payement_term">Payment Terms <span class="text-danger">*</span></label>
                                    <select class="form-control payement_term" name="payement_term" id="payement_term">
                                        <option value="">Select Payment Terms</option>
                                        @foreach ($payment_terms as $item)
                                            <option value="{{ $item->id }}"{{ $item->id == $supplier['payment_information']['payement_term'] ? ' selected' : '' }}>{{ $item->payement_term }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-danger font-weight-bold err_payement_term"></small>
                                </div>
                            </div>

                            <hr>

                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Sale Representative Information</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="contact_person_name">Sale Representative Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="contact_person_name"
                                        class="form-control contact_person_name" id="contact_person_name"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_contact_person_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="contact_person_contact">Sale Representative Contact <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="contact_person_contact"
                                        class="form-control contact_person_contact number_only_val" maxlength="10"
                                        id="contact_person_contact" maxlength="190">
                                    <small class="text-danger font-weight-bold err_contact_person_contact"></small>
                                    <small class="text-danger font-weight-bold err_person_contact"></small>

                                </div>
                            </div>

                            @if (Auth::user()->hasPermissionTo('Update_Supplier'))
                                <div class="col-12 col-md-12 col-xl-12 mb-2 text-end">
                                    <button type="button" onclick="add_contacts()"
                                        class="btn btn-lg btn-secondary text-uppercase submit-form me-2">+</button>
                                </div>
                            @endif

                            <div class="col-12 col-md-12 col-xl-12" id="contact_list_div">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sale Ref Name</th>
                                                <th>Sale Ref Contact</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="_add_contact_list">
                                            @foreach ($supplier['contacts'] as $key => $item)
                                                <tr id="row_{{$key}}">
                                                    <td>
                                                        <input type="hidden" name="person_name[]" value="{{$item['name']}}" >
                                                        {{ Str::limit($item['name'],30) }}
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="person_contact[]" value="{{$item['contact']}}" >
                                                        {{ Str::limit($item['contact'],30) }}
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="remove_contact({{$key}},{{ $item['contact']}})" id="btn_{{$key}}"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <hr>
                        </div>
                        @if (Auth::user()->hasPermissionTo('Update_Supplier'))
                            <div class="row justify-content-end">
                                <div class="col-12 col-md-8 col-lg-4">
                                    <div class="d-flex justify-content-end">
                                        @if ($supplier['supplier_info']['status'] == 1)
                                            <button type="button" onclick="change_status({{$supplier['supplier_info']['id']}},0)" name="action"  class="btn btn-lg btn-outline-danger text-uppercase me-2">
                                                In active
                                            </button>
                                        @else
                                            <button type="button" onclick="change_status({{$supplier['supplier_info']['id']}},1)" name="action"  class="btn btn-lg btn-outline-success text-uppercase me-2">
                                               Active
                                            </button>
                                        @endif

                                        <button type="submit" name="action" value="update" class="btn btn-primary text-uppercase submit-form">
                                            Update
                                        </button>
                                    </div>
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

        $('#submitForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData($('#submitForm')[0]);

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.suppliers.update') }}",
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

        function errorClear() {
            $('#supplier_name').removeClass('is-invalid')
            $('.err_supplier_name').text('')

            $('#supplier_address').removeClass('is-invalid')
            $('.err_supplier_address').text('')

            $('#email').removeClass('is-invalid')
            $('.err_email').text('')

            $('#contact').removeClass('is-invalid')
            $('.err_contact').text('')

            $('#agreement_document').removeClass('is-invalid')
            $('.err_agreement_document').text('')

            $('#account_name').removeClass('is-invalid')
            $('.err_account_name').text('')

            $('#bank_name').removeClass('is-invalid')
            $('.err_bank_name').text('')

            $('#branch_name').removeClass('is-invalid')
            $('.err_branch_name').text('')

            $('#account_number').removeClass('is-invalid')
            $('.err_account_number').text('')

            $('.err_person_contact').text('')
        }

        var person_emails = [];
        var person_contacts = {!! json_encode($contacts) !!};
        var count = '{{count($supplier['contacts'])}}';

        function add_contacts() {
            var data = {
                'contact_person_name': $('#contact_person_name').val(),
                'contact_person_contact': $('#contact_person_contact').val(),
                'person_contacts': person_contacts
            }

            $.ajax({
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                url: "{{ route('business.suppliers.add_update_contacts') }}",
                data: data,
                dataType: 'JSON',
                success: function(response) {
                    $("#loader").hide();
                    clearContactError()
                    if (response.status == false) {
                        $.each(response.message, function(key, item) {
                            if (key) {
                                $('.err_' + key).text(item)
                                $('#' + key).addClass('is-invalid');
                            }
                        });
                    } else {
                        // successPopup(response.message, '')
                        append_table(response.data.contact_person_name,
                            response.data.contact_person_contact)

                        $('#contact_person_name').val('')
                        $('#contact_person_contact').val('')
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
        }

        function clearContactError() {
            $('#contact_person_name').removeClass('is-invalid')
            $('.err_contact_person_name').text('')

            $('#contact_person_contact').removeClass('is-invalid')
            $('.err_contact_person_contact').text('')

            $('.err_person_contact').text('')
        }

        function append_table(person_name, person_contact) {
            // Increment count for unique row ID
            count++;

            // Build the HTML string
            var html = "<tr id='row_" + count + "'>";
            html += "<td>";
            html += '<input type="hidden" name="person_name[]" value="' + person_name + '" >';
            html += person_name;
            html += "</td>";
            html += "<td>";
            html += '<input type="hidden" name="person_contact[]" value="' + person_contact + '" >';
            html += person_contact;
            html += "</td>";
            html += "<td>";
            // Button to remove the contact using count as row ID
            html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="remove_contact(' + count +
                ', \'' + person_contact + '\')" id="btn_' + count +
                '"><i class="fa fa-minus"></i></button>';
            html += "</td>";
            html += "</tr>"; // Correct closing tag

            // Ensure the table is displayed
            $('#contact_list_div').css('display', 'block');

            // Append the HTML row to the table
            $('._add_contact_list').append(html);

            // Add the email and contact to the arrays
            person_contacts.push(person_contact);
        }

        function remove_contact(count, person_contact) {
            // Remove the row with the specific ID
            console.log(person_contacts);

            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: 'Do you want to Delete the Selected Supplier Contact?',
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            $('#row_' + count).remove();

                            // Optionally, you can also remove the email and contact from the arrays
                            person_contacts = person_contacts.filter(contact => contact !== person_contact);

                            if (person_contacts.length == 0) {
                                $('#contact_list_div').css('display', 'none');
                            }
                        }
                    },

                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-red',
                        action: function() {

                        }
                    },
                }
            });
        }

        function change_status(vendor_id, status)
        {
            var data = {
                'vendor_id' : vendor_id,
                'status' : status
            }

            var message = '';

            if (status == 0) {
                message = 'Do you want to Inactive the selected vendor?'
            }
            else
            {
                message = 'Do you want to Active the selected vendor?'
            }

            $.confirm({
                theme: 'modern',
                columnClass: 'col-lg-6 col-md-8 col-sm-10 col-12',
                icon: 'far fa-question-circle text-danger',
                title: 'Are you Sure!',
                content: message,
                type: 'red',
                autoClose: 'cancel|10000',
                buttons: {
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-green',
                        action: function() {
                            $("#loader").show();

                            $.ajax({
                                type: "POST",
                                url: "{{ route('business.suppliers.update_status') }}",
                                data: data,
                                success: function(response) {
                                    $("#loader").hide();
                                    successPopup(response.message, response.route)
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
                        }
                    },

                    cancel: {
                        text: 'Cancel',
                        btnClass: 'btn-red',
                        action: function() {

                        }
                    },
                }
            });
        }
    </script>
@endsection
