<tr><td align="center" style="padding:16px 24px;">
    <table role="presentation" cellpadding="0" cellspacing="0">
        <tr><td align="center" style="padding:16px 32px;font-family:Arial,Helvetica,sans-serif;">
            @if($intro)
                <div style="font-size:14px;color:#18181b;">{{ $intro }}</div>
            @endif
            @if($score)
                <div style="margin-top:6px;font-size:28px;font-weight:bold;color:{{ $primaryColor }};">{{ $score }}</div>
            @endif
            @if($reviewCount)
                <div style="margin-top:4px;font-size:12px;color:#9ca3af;">uit {{ $reviewCount }} beoordelingen</div>
            @endif
        </td></tr>
    </table>
</td></tr>
