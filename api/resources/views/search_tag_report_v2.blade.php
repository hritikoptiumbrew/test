<!-- THIS EMAIL WAS BUILT AND TESTED WITH LITMUS http://litmus.com -->
<!-- IT WAS RELEASED UNDER THE MIT LICENSE https://opensource.org/licenses/MIT -->
<!-- QUESTIONS? TWEET US @LITMUSAPP -->
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <title>PhotoEditorLab</title> <!-- The title tag shows in email notifications, like Android 4.4. -->

    <!-- Web Font / @font-face : BEGIN -->
    <!-- NOTE: If web fonts are not required, lines 10 - 27 can be safely removed. -->

    <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
    <!--[if mso]>
    <style>
        * {
            font-family: Arial, sans-serif !important;
        }
    </style>
    <![endif]-->

    <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,500" rel="stylesheet">
    <!--<![endif]-->

    <!-- Web Font / @font-face : END -->

    <!-- CSS Reset -->
    <style>
        /* What it does: Remove spaces around the email design added by some email clients. */
        /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
        html,
        body {
            margin: 0 auto !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
        }

        /* What it does: Stops email clients resizing small text. */
        * {
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        /* What it does: Centers email on Android 4.4 */
        div[style*="margin: 16px 0"] {
            margin: 0 !important;
        }

        /* What it does: Stops Outlook from adding extra spacing to tables. */
        table,
        td {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }

        /* What it does: Fixes webkit padding issue. Fix for Yahoo mail table alignment bug. Applies table-layout to the first 2 tables then removes for anything nested deeper. */
        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 auto !important;
        }

        table table table {
            table-layout: auto;
        }

        /* What it does: Uses a better rendering method when resizing images in IE. */
        img {
            -ms-interpolation-mode: bicubic;
        }

        a {
            text-decoration: none !important;
        }

        /* What it does: A work-around for email clients meddling in triggered links. */
        *[x-apple-data-detectors],
            /* iOS */
        .x-gmail-data-detectors,
            /* Gmail */
        .x-gmail-data-detectors *,
        .aBn {
            border-bottom: 0 !important;
            cursor: default !important;
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* What it does: Prevents Gmail from displaying an download button on large, non-linked images. */
        .a6S {
            display: none !important;
            opacity: 0.01 !important;
        }

        /* If the above doesn't work, add a .g-img class to any image in question. */
        img.g-img+div {
            display: none !important;
        }

        /* What it does: Prevents underlining the button text in Windows 10 */
        .button-link {
            text-decoration: none !important;
        }

        /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
        /* Create one of these media queries for each additional viewport size you'd like to fix */
        /* Thanks to Eric Lepetit @ericlepetitsf) for help troubleshooting */
        @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {

            /* iPhone 6 and 6+ */
            .email-container {
                min-width: 375px !important;
            }
        }

        .header-gradient-bg {
            /* IE10+ */
            background-image: -ms-linear-gradient(top, #424C54 0%, #424C54 100%);

            /* Mozilla Firefox */
            background-image: -moz-linear-gradient(top, #424C54 0%, #424C54 100%);

            /* Opera */
            background-image: -o-linear-gradient(top, #424C54 0%, #424C54 100%);

            /* Webkit (Safari/Chrome 10) */
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #424C54), color-stop(100, #424C54));

            /* Webkit (Chrome 11+) */
            background-image: -webkit-linear-gradient(top, #424C54 0%, #424C54 100%);

            /* W3C Markup */
            background-image: linear-gradient(to bottom, #424C54 0%, #424C54 100%);
        }
    </style>

    <!-- Progressive Enhancements -->
    <style>
        /* What it does: Hover styles for buttons */
        .button-td,
        .button-a {
            transition: all 100ms ease-in;
        }

        .button-td:hover,
        .button-a:hover {
            background: #00b0e1 !important;
            border-color: #00b0e1 !important;
        }

        /* Media Queries */
        @media screen and (max-width: 480px) {

            /* What it does: Forces elements to resize to the full width of their container. Useful for resizing images beyond their max-width. */
            .fluid {
                width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            /* What it does: Forces table cells into full-width rows. */
            .stack-column,
            .stack-column-center {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }

            /* And center justify these ones. */
            .stack-column-center {
                text-align: center !important;
            }

            /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
            .center-on-narrow {
                text-align: center !important;
                display: block !important;
                margin-left: auto !important;
                margin-right: auto !important;
                float: none !important;
            }

            table.center-on-narrow {
                display: inline-block !important;
            }

            /* What it does: Adjust typography on small screens to improve readability */
            .email-container p {
                font-size: 14px !important;
                line-height: 22px !important;
            }
        }
    </style>
</head>

<body width="100%" bgcolor="#F1F1F1" style="margin: 0; mso-line-height-rule: exactly;">
<center style="width: 100%; background: #F1F1F1; text-align: left;">
    <div style="width: 100%; max-width: 100%; margin: auto; background: #ffffff;" class="email-container">
        <!--[if mso]>
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" align="center">
            <tr>
                <td>
        <![endif]-->
        <table role="presentation" cellspacing="0" cellpadding="0" bgcolor="#e4e4e4" border="0" align="center"
               width="100%" style="max-width: 100%;width: 100%;" class="email-container">
            <tr>
                <td>
                    <table align="center" cellpadding="0" cellspacing="0" class="header-gradient-bg" width="100%">
                        <tr>
                            <td align="center" valign="top" width="100%">
                                <div>
                                    <!--<div style="height:47px; display: inline-block">
                                        <a data-click-track-id="8588" href="www.optimumbrew.com"><img
                                                    width="137" height="47"
                                                    src="https://drive.google.com/uc?id=1iYS79PTXBV3zB79QWuBCmodD_Hi7svtt"
                                                    style="image-rendering: -webkit-optimize-contrast;margin-top: 7px;height: 40px; width: 200px;" alt="logo"></a>
                                    </div>-->
                                    <p style="font-size: 24px;line-height: 0.7;
                                                                                                color: white;
                                                                                                display: inline-block;
                                                                                                vertical-align: super">
                                        PhotoEditorLab</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" style="text-align: center;">
                    <!--[if mso]>
                    <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
                        <tr>
                            <td align="center" valign="middle" width="800px">
                    <![endif]-->
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" width="1400px"
                           style="max-width:100%; margin: auto;">
                        <tr>
                            <td height="40" style="font-size:20px; line-height:20px;">&nbsp;</td>
                        </tr>

                        <tr>
                            <td align="center" valign="middle">
                                <table>
                                    <tr>
                                        <td valign="top" style="text-align: center; padding: 30px 0 10px 0;"
                                            bgcolor="#ffffff">
                                            {{--                                            <h1 style="text-transform: uppercase; margin: 0; font-family: 'Montserrat', sans-serif; font-size: 24px; line-height: 36px; color: #565566; font-weight: bold;">Hi--}}
                                            {{--                                                {!! $message_body['user_name'] !!}</h1>--}}
                                            <h2>{{ $heading }} </h2>
                                            <h3>Application name : {{ $app_name }} </h3>
                                            <p>This is search tag report of last 20 success & last 20 fail.</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td valign="top" bgcolor="#ffffff" style="text-align: center; padding: 5px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #757575;">
                                            {{--                                            <p style="margin-top: 0; font-weight: bold;"></p>--}}
                                            {{--                                            <p style="margin: 0;">--}}
                                            {{--                                                {!! $message_body['message'] !!}</p>--}}
                                            <table border=1 style="display: table;border-collapse: separate;padding: 5px;border-color: grey;width: 100%;color: #212529;">
                                                <thead>
                                                <tr style="padding:.50rem;vertical-align: middle;border-bottom-width: 2px;border-top-width: 2px;">
                                                    <th>No</th>
                                                    <th>Search query</th>
                                                    <th>Search count</th>
                                                    <th>Content count</th>
                                                    <th>Sub category name</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @if(!empty($message_body))
                                                    @foreach($message_body AS $i => $row)
                                                        @if($i%2 == 0)
                                                        <tr style="border: 1px solid black; word-break: break-word; background-color: #ffffff;">
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: left;">{{  $i + 1 }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->tag) ? $row->tag : '' }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->search_count) ? $row->search_count : '' }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->content_count) ? $row->content_count :'' }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->sub_category_name) && $row->sub_category_name !='' ? $row->sub_category_name :'-' }}</td>
                                                            @if(isset($row->is_success) && $row->is_success == 1)
                                                                <td style="padding:.50rem;vertical-align: middle;text-align: center;color: green !important;">Success</td>
                                                            @else
                                                                <td style="padding:.50rem;vertical-align: middle;text-align: center;color: #dc3545!important;">Fail</td>
                                                            @endif
                                                        </tr>
                                                        @else
                                                        <tr style="border: 1px solid black; word-break: break-word; background-color: #D3D3D3;">
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: left;">{{  $i + 1 }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->tag) ? $row->tag : '' }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->search_count) ? $row->search_count : '' }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: right;">{{ isset($row->content_count) ? $row->content_count :'' }}</td>
                                                            <td style="padding:.50rem;vertical-align: middle;text-align: left;">{{ isset($row->sub_category_name) && $row->sub_category_name !='' ? $row->sub_category_name :'-' }}</td>
                                                            @if(isset($row->is_success) && $row->is_success == 1)
                                                                <td style="padding:.50rem;vertical-align: middle;text-align: center;color: green !important;">Success</td>
                                                            @else
                                                                <td style="padding:.50rem;vertical-align: middle;text-align: center;color: #dc3545!important;">Fail</td>
                                                            @endif
                                                        </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr style="border: 1px solid black;">
                                                        <td colspan="7" style="padding:.50rem;vertical-align: middle;text-align: center;">No search tag found.</td>
                                                    </tr>
                                                @endif
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#ffffff" style="font-family: 'Montserrat', sans-serif; text-align: left; padding: 40px 15px 30px 40px; font-size: 13px; color: gray;">
                                            <p style="margin:0;">Thanks,</p>
                                            <p style="margin:0;">The Optimumbrew Team</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--[if mso]>
                    </td>
                    </tr>
                    </table>
                    <![endif]-->
                </td>
            </tr>
            <tr>
                <td style="padding: 40px 80px 40px 80px; font-family: sans-serif; font-size: 15px; line-height: 20px;  text-align: center;">
                    <p style="margin: 0; font-weight:bold; color: #757575;">We are here if you need
                        support</p>
                    <p style="font-size: 12px !important; color:#909090; font-weight: 600; font-family: sans-serif;">Email:
                        work.optimum@gmail.com</p>
                </td>
            </tr>

            <tr>
                <td class="header-gradient-bg">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                        <tr>
                            <td style="padding: 10px 40px 10px 40px; font-family: sans-serif; font-size: 12px; line-height: 18px; color: white; font-weight:normal;">
                                <p style="margin: 0;">This email was sent by </p>
                                <p style="margin: 0; font-weight:bold;">work.optimum@gmail.com</p>
                            </td>
                        </tr>


                    </table>
                </td>
            </tr>
        </table>
        <!--[if mso]>
        </td>
        </tr>
        </table>
        <![endif]-->
    </div>
</center>
</body>

</html>
