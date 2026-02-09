<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampanhaEncerrada extends Notification
{
    use Queueable;

    public function __construct(
        public string $motivo,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        $mensagem = match ($this->motivo) {
            'esgotamento' => 'A campanha foi encerrada automaticamente: todos os 100.000 números da sorte foram distribuídos!',
            'prazo' => 'A campanha foi encerrada: o prazo da promoção expirou.',
            default => 'A campanha foi encerrada manualmente.',
        };

        return [
            'tipo' => 'campanha_encerrada',
            'motivo' => $this->motivo,
            'mensagem' => $mensagem,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $motivo = match ($this->motivo) {
            'esgotamento' => 'Todos os 100.000 números da sorte foram distribuídos.',
            'prazo' => 'O prazo da promoção expirou.',
            default => 'A campanha foi encerrada manualmente.',
        };

        return (new MailMessage)
            ->subject('[INFO] Campanha Promoção Modelo Encerrada')
            ->greeting('Campanha Encerrada!')
            ->line("A campanha Promoção Modelo foi encerrada.")
            ->line("**Motivo:** {$motivo}")
            ->line('Nenhum novo cadastro ou cupom será aceito a partir de agora.')
            ->action('Acessar Painel de Controle', url('/admin'));
    }
}
