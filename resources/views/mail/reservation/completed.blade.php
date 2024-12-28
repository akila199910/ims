@extends('layouts.mail')

@section('body')
    <tr>
        <!--middle content-->
        <td align="center" valign="top" bgcolor="#F5F7F7" style="background: #F5F7F7; display: table;width: 100%;">
            <table border="0" cellpadding="0" cellspacing="15" height="100%" width="100%" id="content"
                style="padding: 47px 40px;">


                <tr>
                    <!--middle-content-verify-email-part-->
                    <td>
                        <table border="0" cellpadding="10" cellspacing="10" height="100%" width="100%"
                            role="presentation" bgcolor="#fff"
                            style="background: #fff;padding: 20px 20px;border-radius: 10px;">
                            <tr>
                                <td align="center" valign="center" style="padding-bottom: 8px;">
                                    <img src="{{ asset('mail_layout/table_img.svg') }}" alt="cup" class="img-fluid"
                                        width="54" height="54">
                                </td>

                            </tr>
                            <tr>
                                <td align="center" valign="center">
                                    <h3
                                        style=" font-size: 25px;font-family: 'Lexend', sans-serif; font-weight: 500;color: #1A1A1A;line-height: 31px;">
                                        Dear <span style="font-weight: 700;text-transform: uppercase;">{{$name}}</span>
                                    </h3>
                                </td>
                            </tr>

                            <tr>
                                <td align="center" valign="center">
                                    <h3
                                        style=" font-size: 25px;font-family: 'Lexend', sans-serif; font-weight: 500;color: #1A1A1A;line-height: 31px;">
                                        Your reservation has been Confirmed.
                                    </h3>
                                </td>
                            </tr>

                            <tr>
                                <td style="padding: 20px 0 0;">
                                    <table cellspacing="5" cellpadding="5" border="0">

                                        <tr>
                                            <td style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                               Reference Number : {{$reservation->ref_no}}</td>
                                        </tr>

                                        <tr>
                                            <td style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                               Requested Date : {{date('jS, M Y', strtotime($reservation->request_date))}}</td>
                                        </tr>

                                        <tr>
                                            <td
                                                style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                               Start Time :
                                                {{ date('h:i A', strtotime($reservation->request_start_time)) }}</td>
                                        </tr>

                                        <tr>
                                            <td
                                                style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                                End Time :
                                                {{ date('h:i A', strtotime($reservation->request_end_time)) }}</td>
                                        </tr>

                                        <tr>
                                            <td style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                               Requested Table : {{$reservation->table_info->name}}</td>
                                        </tr>

                                        <tr>
                                            <td style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                               Number of People : {{$reservation->no_of_people}}</td>
                                        </tr>

                                        <tr>
                                            <td
                                                style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                                Number of Extra People : {{ $reservation->extra_people }}</td>
                                        </tr>

                                        <tr>
                                            <td style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                               Amount : {{ number_format((($reservation->amount + $reservation->extra_amount+  $reservation->service_amount)- $reservation->discount),2,'.','') }}</td>
                                        </tr>

                                        <tr>
                                            <td
                                                style="font-size: 16px; letter-spacing: 1px; font-weight:500;color:#5C7575;text-transform:uppercase;font-family: 'Lexend', sans-serif;    padding: 5px 0;">
                                                Location Details -

                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <p
                                                    style="font-size: 16px;color: #1A1A1A;text-align: left;font-weight: 400;font-family: 'Lexend', sans-serif;display: block; padding-bottom: 3px;
                                                     width: 100%;">
                                                    {{ $reservation->location_info->location_name }}, <br>
                                                    {{ $reservation->location_info->address }}, <br>
                                                    {{ $reservation->location_info->contact_no }}. <br>
                                                    @if ($reservation->location_info->google_location != '')
                                                        <a href="{{ $reservation->location_info->google_location }}">Google
                                                            Map</a>
                                                    @endif
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- <tr>
                                <td style="padding: 10px 60px;" class="mb-padding-view">
                                    <table border="0" cellpadding="0" cellspacing="15" width="100%" height="100%">
                                        <tr>
                                            <td align="center" valign="middle">
                                                <a href="{{route('reservation.reviewus', $reservation->ref_no)}}" target="_blank"
                                                    style="font-size: 15px;font-family: 'Lexend', sans-serif;letter-spacing: 1.4px;text-decoration: none;text-transform: uppercase;color: #fff;padding: 14px 38px;background: #1A1A1A;border-radius: 10px;display: inline-block;cursor: pointer;font-weight: 600;">Review Us</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr> --}}

                        </table>
                    </td>
                    <!--end middle-content-verify-email-part-->
                </tr>

            </table>
        </td>
        <!--end midddle content-->
    </tr>
@endsection
