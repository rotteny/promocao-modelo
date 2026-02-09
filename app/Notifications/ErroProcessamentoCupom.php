<?php

namespace App\Notifications;

use App\Models\CupomFiscal;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ErroProcessamentoCupom extends Notification
{
    use Queueable;

    public function __construct(
        public CupomFiscal $cupom,
        public \Throwable $exception,
    ) {}

    /**
     * Canais de notificação: database (painel) e mail.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Dados para armazenamento no banco (notifications table).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'erro_processamento_cupom',
            'cupom_id' => $this->cupom->id,
            'cupom_numero' => $this->cupom->numero,
            'participante_id' => $this->cupom->participante_id,
            'erro' => $this->exception->getMessage(),
            'mensagem' => "Erro ao processar cupom #{$this->cupom->numero}. A fila de processamento foi bloqueada.",
        ];
    }

    /**
     * Notificação por e-mail.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[ALERTA] Erro no Processamento de Cupom - Promoção Modelo')
            ->error()
            ->greeting('Atenção, Administrador!')
            ->line("Ocorreu um erro ao processar o cupom fiscal **#{$this->cupom->numero}**.")
            ->line("**Erro:** {$this->exception->getMessage()}")
            ->line('A fila de processamento de números da sorte foi **bloqueada automaticamente**. Nenhum novo cupom será processado até que o problema seja resolvido.')
            ->action('Acessar Painel de Controle', url('/admin'))
            ->line('Verifique o problema e desbloqueie a fila no painel administrativo.');
    }
}
