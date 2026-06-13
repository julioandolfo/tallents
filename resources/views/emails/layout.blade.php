<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', config('app.name'))</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:24px 0;">
        <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;">
                <tr>
                    <td style="background:linear-gradient(90deg,#4f46e5,#7c3aed);padding:24px 32px;">
                        <span style="color:#ffffff;font-size:20px;font-weight:bold;">{{ config('app.name', 'Tallents Gestão') }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <h1 style="margin:0 0 16px;font-size:20px;color:#111827;">@yield('titulo')</h1>
                        <div style="font-size:14px;line-height:1.6;color:#374151;">
                            @yield('conteudo')
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;">
                        <span style="font-size:12px;color:#9ca3af;">Esta é uma mensagem automática do {{ config('app.name', 'Tallents Gestão') }}.</span>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
