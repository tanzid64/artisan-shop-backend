@extends('layouts.email_template')
@section('content')
    <style>
        .reset_btn {
            color: #ffffff;
            background-color: #2d63c8;
            font-size: 14px;
            border: 1px solid #2d63c8;
            border-radius: 29px;
            padding: 15px 30px;
            cursor: pointer
        }

        .reset_btn:hover {
            color: #2d63c8;
            background-color: #ffffff;
        }
    </style>
    <table border="0" cellpadding="0" cellspacing="0" class="force-row"
        style="width: 100%;    border-bottom: solid 1px #ccc;">
        <tr>
            <td class="content-wrapper" style="padding-left:24px;padding-right:24px"><br>
                <div class="title"
                    style="font-family: Helvetica, Arial, sans-serif; font-size: 18px;font-weight:400;color: #000;text-align: left;
                 padding-top: 20px;">
                    Dear {{ $full_name }} ,
                </div>
            </td>
        </tr>
        <tr>
            <td class="cols-wrapper" style="padding-left:12px;padding-right:12px">
                <table border="0" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="100%" style="width: 192px;" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0" align="left" class="force-row"
                                style="width: 100%;">
                                <tr>
                                    <td class="row" valign="top"
                                        style="padding-left:12px;padding-right:12px;padding-top:18px;padding-bottom:12px">
                                        <table border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                                            <tr>
                                                <td class="subtitle"
                                                    style="font-family:Helvetica, Arial, sans-serif;font-size:14px;line-height:22px;font-weight:400;color:#333;padding-bottom:30px; text-align: left;">
                                                    <p>Please click the link below to reset password.
                                                    </p>
                                                    <br>
                                                    <a href="{{ config('app.base_url') . '/reset-password/' . $token . '/' . $email }}"
                                                        class="reset_btn">Password reset</a>
                                                    <br>
                                                    <br>
                                                    <p>This password reset link will expire in 60 minutes. <br>

                                                        If you did not request a password reset, no further action is
                                                        required.</p>
                                                    <hr>
                                                    <p>
                                                        If you’re having trouble clicking the "Reset Password" button, copy
                                                        and paste the URL below into your web browser:
                                                        <a
                                                            href="{{ config('app.base_url') . '/reset-password/' . $token . '/' . $email }}">{{ config('app.base_url') . '/reset-password/' . $token . '/' . $email }}</a>
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-family: Helvetica, Arial, sans-serif;font-size: 14px;line-height: 22px;font-weight: 400;color: #333; padding-bottom: 30px;text-align: left;">
                                                    Thanks,<br>The {{ config('app.name') }} Team
                                                </td>
                                            </tr>

                                        </table>
                                        <br>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
