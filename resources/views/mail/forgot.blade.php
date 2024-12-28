@extends('layouts.mail')

@section('body')

<table role="presentation" class="mb-100" cellspacing="0" cellpadding="30" width="800" align="center" border="0" style="background-color: #fafafa;">
    <!--container-->
    <!--logo-->
    <tr>
        <td style="border-bottom: 3px solid #337dc5;">
            {{-- <a href="#"><img src="{{$comapny_logo}}" alt="logoSalon" class="logo-responsive logo-mg" style="max-width: 100%; height: auto; width: 80px;" /></a> --}}
        </td>
    </tr>
    <!--logo-->

    <!--lock-image-->
    <!-- <tr>
        <td class="lock-img">
            <a href=""><img src="images/lock.png" alt="lock" class="lock-non"></a>
        </td>
    </tr> -->
    <!--lock-image-->

    <tr>
        <!--Leave Request-->

        <td style="padding: 60px" class="p-50 leave-pad">
            <table
                role="presentation"
                cellspacing="0"
                cellpadding="16"
                width="600"
                align="center"
                class="mb-100 leave-mar"
                style="background-color: #fff; border-radius: 20px 20px; padding: 20px 0px; margin: 0px auto; padding-bottom: 40px;">

                  <tr>
                    <td class="lock-img">
                        <a href=""><img src="{{asset('mail_style/lock.png')}}" alt="lock" class="lock-non"></a>
                    </td>
                  </tr><tr>

                    <tr>
                        <td class="dear">
                            <h1 style="font-size: 22px; color: #000000; font-weight: 600; line-height: 33px; letter-spacing: 2.64px; text-transform: uppercase;" class="dear-sec">Dear {{$name}},</h1>
                        </td>
                    </tr>
                    <td align="center" class="f-16 pt-60 slip" style="text-align: center; text-transform: uppercase; color: #337dc5; font-size: 26px; line-height: 42px; letter-spacing: 3.12px; font-weight: 600;">
                        Forgot your<br />
                        password?
                    </td>
                </tr>

                <tr>
                    <td style="font-size: 12px; font-weight: 300; color: #000000; line-height: 18px; letter-spacing: 1.44px; text-align: center; text-transform: uppercase;">
                        You have requested a new password for your account.
                    </td>
                </tr>

                <tr>
                    <td style="text-align: center; padding: 40px 0px;">
                        <a href="{{url('/forget_password/verify/'.Crypt::encrypt($email))}}" target="_blank">
                            <button type="button" class="btn" style="background: #216fba; color: #e8f4fd; border-radius: 7px 7px; padding: 7px 28px; font-size: 14px; cursor: pointer; line-height: 21px; border: 0;">
                                Reset password
                            </button>
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <!--Leave Request-->

    <!--footer-->
    <tr>
        <td style="background-color: #337dc5; text-align: center; font-size: 10px; line-height: 16px; letter-spacing: 1.2px; color: #ffffff; text-transform: uppercase;" class="footer-sec">
            {{-- {{$business->name}} <br> --}}
            {{-- {{$business->street_address.', '.$business->city}} <br>
            {{$business->contact_1}} --}}
        </td>
    </tr>
    <!--footer-->
</table>


@endsection
