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
                                        New Purchase Order Found
                                    </h3>
                                </td>
                            </tr>



                        </table>
                    </td>
                    <!--end middle-content-verify-email-part-->
                </tr>

            </table>
        </td>
        <!--end midddle content-->
    </tr>
@endsection
