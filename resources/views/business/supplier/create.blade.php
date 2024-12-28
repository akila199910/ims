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
                    <li class="breadcrumb-item active">Add new Vendor</li>
                </ul>
            </div>
            <div class="col-12 text-end">
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
                                    <h4>Vendors Information</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="supplier_name">Vendor Name <span class="text-danger">*</span> </label>
                                    <input type="text" name="supplier_name" class="form-control supplier_name" id="supplier_name"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_supplier_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="supplier_address">Address <span class="text-danger">*</span></label>
                                    <input type="text" name="supplier_address" class="form-control supplier_address" id="supplier_address"
                                        maxlength="300">
                                    <small class="text-danger font-weight-bold err_supplier_address"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label" for="email">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control email" id="email"
                                        maxlength="190">
                                    <small class="text-danger font-weight-bold err_email"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="contact">Contact <span class="text-danger">*</span></label>
                                    <input type="text" name="contact" class="form-control contact number_only_val"
                                        maxlength="10" id="contact" maxlength="190">
                                    <small class="text-danger font-weight-bold err_contact"></small>
                                </div>
                            </div>

                            {{-- <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block select-gender">
                                    <label class="gen-label" for="status">Status Inactive/Active</label>
                                    <div class="status-toggle d-flex justify-content-between align-items-center">
                                        <input type="checkbox" id="status" name="status" checked class="check">
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
                                    <input type="date" name="date_of_agree"  max="{{ date('Y-m-d') }}"  class="form-control datepicker date_of_agree" id="date_of_agree">
                                    <small class="text-danger font-weight-bold err_date_of_agree"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label text-uppercase" for="date_agree_exp">Date Of Exp<span class="text-danger">*</span></label>
                                    <input type="date" name="date_agree_exp"   class="form-control datepicker date_agree_exp" id="date_agree_exp">
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
                                        id="account_name" maxlength="190">
                                    <small class="text-danger font-weight-bold err_account_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label" for="bank_name">Bank Name <span
                                            class="text-danger">*</span></label>
                                    <input type="bank_name" name="bank_name" class="form-control bank_name"
                                        id="bank_name" maxlength="190">
                                    <small class="text-danger font-weight-bold err_bank_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label class="form-label" for="branch_name">Branch Name <span
                                            class="text-danger">*</span></label>
                                    <input type="branch_name" name="branch_name" class="form-control branch_name"
                                        id="branch_name" maxlength="190">
                                    <small class="text-danger font-weight-bold err_branch_name"></small>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="account_number">Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="account_number"
                                        class="form-control account_number number_only_val"
                                        id="account_number" maxlength="190">
                                    <small class="text-danger font-weight-bold err_account_number"></small>
                                </div>
                            </div> --}}

                            <div class="col-12 col-md-6 col-xl-6">
                                <div class="input-block local-forms">
                                    <label for="payement_term">Payment Terms <span class="text-danger">*</span></label>
                                    <select class="form-control payement_term" name="payement_term" id="payement_term">
                                        <option value="">Select Payment Terms</option>
                                        @foreach ($payment_terms as $item)
                                            <option value="{{$item->id}}">{{ $item->payement_term }}</option>
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
                                    <label for="contact_person_contact">Sale Representative Mobile Number  <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="contact_person_contact"
                                        class="form-control contact_person_contact number_only_val" maxlength="10"
                                        id="contact_person_contact" maxlength="190">
                                    <small class="text-danger font-weight-bold err_contact_person_contact"></small>
                                    <small class="text-danger font-weight-bold err_person_contact"></small>

                                </div>
                            </div>

                            @if (Auth::user()->hasPermissionTo('Create_Supplier'))
                                <div class="col-12 col-md-12 col-xl-12 mb-2 text-end">
                                    <button type="button" onclick="add_contacts()"
                                        class="btn btn-lg btn-secondary text-uppercase submit-form me-2">+</button>
                                </div>
                            @endif

                            <div class="col-12 col-md-12 col-xl-12" id="contact_list_div" style="display: none">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sale Ref Name</th>
                                                <th>Sale ref Contact</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="_add_contact_list">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <hr>
                        </div>
                        @if (Auth::user()->hasPermissionTo('Create_Supplier'))
                            <div class="col-12">
                                <div class="doctor-submit text-end">
                                    <button type="submit"
                                        class="btn btn-primary text-uppercase submit-form me-2">Create</button>
                                </div>
                            </div>
                        @endif
                        {{-- <div class="col-12 col-md-4">
                            <div class="doctor-submit text-end">
                                <button type="submit"
                                    class="btn btn-primary text-uppercase submit-form me-2">Inactive/Active</button>
                            </div>
                        </div> --}}

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
                url: "{{ route('business.suppliers.create') }}",
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

            $('#payement_term').removeClass('is-invalid')
            $('.err_payement_term').text('')

            $('#date_of_agree').removeClass('is-invalid')
            $('.err_date_of_agree').text('')

            $('#date_agree_exp').removeClass('is-invalid')
            $('.err_date_agree_exp').text('')

            $('.err_person_contact').text('')
        }

        var person_emails = [];
        var person_contacts = [];
        var count = 0;

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
    </script>

@endsection
