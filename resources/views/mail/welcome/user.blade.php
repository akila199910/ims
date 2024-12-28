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
                        <table border="0" cellpadding="10" cellspacing="0" height="100%" width="100%"
                            role="presentation" bgcolor="#fff"
                            style="background: #fff;padding: 48px 20px;border-radius: 10px;">


                            <tr>
                                <td align="center" style="padding:80px 0   ;">
                                    <table border="0" width="444" height="100%" role="presentation">
                                        <tr>
                                            <td align="center" style="padding-bottom: 20px;">
                                                <p
                                                    style="font-family: 'Lexend', sans-serif;font-weight: 600;font-size: 18px;color: #1A1A1A;text-transform: uppercase;letter-spacing: 1.8px;">
                                                    Welcome to {{env('APP_NAME')}}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-bottom: 20px;">
                                                <p
                                                    style="font-size: 16px;color: #1A1A1A;text-align: center;font-weight: 600;font-family: 'Barlow', sans-serif;display: block;padding-bottom: 3px;">
                                                    Congrats! You account has been created and now you can access our
                                                    services.</p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding-bottom: 20px;">
                                                <p
                                                    style="font-size: 16px;color: #1A1A1A;text-align: left;font-weight: 600;font-family: 'Barlow', sans-serif;display: block;padding-bottom: 3px; text-transform: uppercase;">
                                                    Following are the login credentials you have to use.</p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding-bottom: 20px;">
                                                <p
                                                    style="font-size: 16px;color: #1A1A1A;text-align: left;font-weight: 500;font-family: 'Barlow', sans-serif;display: block;padding-bottom: 3px; ">
                                                    User Name : {{ $email }}</p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 10px 10px;" class="mb-padding-view">
                                                <table border="0" cellpadding="0" cellspacing="15" width="100%"
                                                    height="100%">
                                                    <tr>
                                                        <td align="center" valign="middle">
                                                            <a href="{{ route('set_password.view', $user->ref_no) }}"
                                                                target="_blank"
                                                                style="font-size: 15px;font-family: 'Lexend', sans-serif;letter-spacing: 1.4px;text-decoration: none;text-transform: uppercase;color: #fff;padding: 14px 65px;background: #1A1A1A;border-radius: 10px;display: inline-block;cursor: pointer;font-weight: 600;">Set
                                                                your Password</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 10px 10px;" class="mb-padding-view">
                                                <table border="0" cellpadding="0" cellspacing="15" width="100%"
                                                    height="100%">
                                                    <tr>
                                                        <td align="center" valign="middle">
                                                            <a href="{{ route('login') }}" target="_blank"
                                                                style="font-size: 15px;font-family: 'Lexend', sans-serif;letter-spacing: 1.4px;text-decoration: none;text-transform: uppercase;color: #fff;padding: 14px 60px;background: #1A1A1A;border-radius: 10px;display: inline-block;cursor: pointer;font-weight: 600;">Click
                                                                Here to Login</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="center" style="padding-bottom: 10px; padding-top: 20px;">
                                                <p
                                                    style="font-family: 'Lexend', sans-serif;font-weight: 600;font-size: 18px;color: #1A1A1A;text-transform: uppercase;letter-spacing: 1.8px;">
                                                    Thank You!</p>
                                            </td>
                                        </tr>
                                    </table>
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
