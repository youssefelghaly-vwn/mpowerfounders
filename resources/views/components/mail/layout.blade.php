@props([
    'title' => 'MPower Founders',
    'preheader' => null,
    'eyebrow' => null,
    'heading' => null,
    'ctaUrl' => null,
    'ctaLabel' => null,
    'footnote' => null,
])

{{--
    Shared shell for every transactional email — same ink/paper/gold system
    as the site and the original password-reset template, extracted so the
    table scaffolding (Outlook conditionals, 600px container, bulletproof
    button) lives in one place.

    Usage:
        <x-mail.layout heading="Your account is live" eyebrow="Welcome"
                       :cta-url="$url" cta-label="Sign in">
            <p style="...">body copy</p>
        </x-mail.layout>
--}}
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ $title }}</title>
<!--[if mso]>
<noscript>
<xml>
<o:OfficeDocumentSettings>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
</noscript>
<![endif]-->
<style>
  body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { -ms-interpolation-mode: bicubic; border: 0; line-height: 100%; outline: none; text-decoration: none; }
  body { margin: 0; padding: 0; width: 100% !important; background-color: #e8e4da; }
  a { text-decoration: none; }
  @media only screen and (max-width: 600px) {
    .email-container { width: 100% !important; }
    .email-padding { padding-left: 28px !important; padding-right: 28px !important; }
  }
</style>
</head>
<body style="margin:0;padding:0;background-color:#e8e4da;">

  @if ($preheader)
    <div style="display:none;font-size:1px;color:#e8e4da;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
      {{ $preheader }}
    </div>
  @endif

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#e8e4da;">
    <tr>
      <td align="center" style="padding:40px 16px;">

        <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0" style="width:600px;max-width:600px;background-color:#f3f1ec;border-radius:20px;overflow:hidden;">

          <!-- header -->
          <tr>
            <td style="background-color:#0d0d0f;padding:26px 40px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:15px;font-weight:700;letter-spacing:0.02em;color:#f3f1ec;">
                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background-color:#caa64f;margin-right:8px;"></span>
                    <span>MPOWER&nbsp;&middot;&nbsp;FOUNDERS</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- body -->
          <tr>
            <td class="email-padding" style="padding:48px 48px 40px;">

              @if ($eyebrow)
                <p style="margin:0 0 10px;font-family:'Courier New',Courier,monospace;font-size:11px;letter-spacing:0.12em;text-transform:uppercase;color:#caa64f;font-weight:700;">
                  {{ $eyebrow }}
                </p>
              @endif

              @if ($heading)
                <h1 style="margin:0 0 16px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:26px;font-weight:800;letter-spacing:-0.02em;color:#0d0d0f;line-height:1.2;">
                  {{ $heading }}
                </h1>
              @endif

              {{ $slot }}

              @if ($ctaUrl && $ctaLabel)
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top:8px;">
                  <tr>
                    <td style="border-radius:999px;background-color:#caa64f;">
                      <!--[if mso]>
                      <a href="{{ $ctaUrl }}" target="_blank" style="height:44px;v-text-anchor:middle;width:240px;" arcsize="50%" strokecolor="#caa64f" fillcolor="#caa64f">
                      <center style="color:#0d0d0f;font-family:Courier New,monospace;font-size:13px;font-weight:700;">{{ $ctaLabel }}</center>
                      </a>
                      <![endif]-->
                      <!--[if !mso]><!-->
                      <a href="{{ $ctaUrl }}" target="_blank"
                         style="display:inline-block;padding:14px 32px;font-family:'Courier New',Courier,monospace;font-size:13px;font-weight:700;letter-spacing:0.02em;color:#0d0d0f;text-decoration:none;border-radius:999px;">
                        {{ $ctaLabel }}
                      </a>
                      <!--<![endif]-->
                    </td>
                  </tr>
                </table>

                <p style="margin:24px 0 0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.6;color:#8a8a8f;">
                  If the button doesn't work, copy and paste this URL into your browser:
                </p>
                <p style="margin:8px 0 0;font-family:'Courier New',Courier,monospace;font-size:12px;line-height:1.6;word-break:break-all;">
                  <a href="{{ $ctaUrl }}" target="_blank" style="color:#caa64f;text-decoration:underline;">{{ $ctaUrl }}</a>
                </p>
              @endif

              @if ($footnote)
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:32px;border-top:1px solid rgba(13,13,15,0.12);">
                  <tr>
                    <td style="padding-top:24px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.6;color:#8a8a8f;">
                      {{ $footnote }}
                    </td>
                  </tr>
                </table>
              @endif

            </td>
          </tr>

          <!-- footer -->
          <tr>
            <td style="padding:22px 48px 32px;">
              <p style="margin:0;font-family:'Courier New',Courier,monospace;font-size:11px;color:#8a8a8f;">
                &copy; {{ date('Y') }} MPower Founders
              </p>
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>

</body>
</html>
