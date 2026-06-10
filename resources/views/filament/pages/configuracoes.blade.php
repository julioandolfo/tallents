<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Envio de e-mail</x-slot>
            <x-slot name="description">
                Os e-mails do sistema (notificações, recuperação de senha) usam as variáveis de
                ambiente do servidor. Em produção, configure-as no Coolify em
                <strong>Environment Variables</strong>.
            </x-slot>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                @foreach ($this->getMailConfig() as $label => $value)
                    <div class="flex flex-col">
                        <dt class="text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-6 rounded-lg bg-gray-50 dark:bg-white/5 p-4 text-sm text-gray-600 dark:text-gray-300">
                <p class="font-medium mb-2">Variáveis para configurar o e-mail (Coolify → Environment Variables):</p>
                <pre class="whitespace-pre-wrap text-xs leading-relaxed">MAIL_MAILER=smtp
MAIL_HOST=smtp.seuprovedor.com
MAIL_PORT=587
MAIL_USERNAME=usuario
MAIL_PASSWORD=senha
MAIL_FROM_ADDRESS=noreply@tallents.com.br
MAIL_FROM_NAME="Tallents RH"</pre>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Aplicação</x-slot>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="flex flex-col">
                    <dt class="text-gray-500 dark:text-gray-400">Ambiente</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ config('app.env') }}</dd>
                </div>
                <div class="flex flex-col">
                    <dt class="text-gray-500 dark:text-gray-400">URL</dt>
                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ config('app.url') }}</dd>
                </div>
            </dl>
        </x-filament::section>
    </div>
</x-filament-panels::page>
