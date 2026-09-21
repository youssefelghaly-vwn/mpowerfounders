@props(['rows' => []])

{{--
    Key/value block — project reference, stage, submitted-by and so on.
    $rows is an ordered ['Label' => 'value'] map; empty values are skipped
    so a partially filled record doesn't render blank lines.
--}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="margin:0 0 28px;background-color:#e8e4da;border-radius:14px;">
    <tr>
        <td style="padding:20px 24px;">
            @foreach ($rows as $label => $value)
                @continue(blank($value))
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:10px;">
                    <tr>
                        <td style="font-family:'Courier New',Courier,monospace;font-size:11px;letter-spacing:0.08em;text-transform:uppercase;color:#8a8a8f;padding-bottom:2px;">
                            {{ $label }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:14.5px;font-weight:600;color:#0d0d0f;line-height:1.5;">
                            {{ $value }}
                        </td>
                    </tr>
                </table>
            @endforeach
        </td>
    </tr>
</table>
