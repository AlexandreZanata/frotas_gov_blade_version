<?php

namespace App\Notifications;

use App\Models\Run;
use App\Models\ChecklistItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChecklistProblemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Run $run,
        public ChecklistItem $item,
        public string $notes
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Problema Detectado no Checklist - ' . $this->run->vehicle->prefix->name)
            ->greeting('Olá, ' . $notifiable->name)
            ->line('Um problema foi detectado no checklist de um veículo.')
            ->line('**Veículo:** ' . $this->run->vehicle->name . ' (' . $this->run->vehicle->plate . ')')
            ->line('**Prefixo:** ' . $this->run->vehicle->prefix->name)
            ->line('**Motorista:** ' . $this->run->user->name)
            ->line('**Item com Problema:** ' . $this->item->name)
            ->line('**Descrição do Problema:**')
            ->line($this->notes)
            ->action('Ver Detalhes', url('/logbook/' . $this->run->id))
            ->line('Por favor, tome as providências necessárias.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'run_id' => $this->run->id,
            'vehicle_id' => $this->run->vehicle_id,
            'vehicle_name' => $this->run->vehicle->name,
            'vehicle_plate' => $this->run->vehicle->plate,
            'vehicle_prefix' => $this->run->vehicle->prefix->name ?? 'N/A',
            'driver_name' => $this->run->user->name,
            'item_name' => $this->item->name,
            'problem_notes' => $this->notes,
            'type' => 'checklist_problem',
        ];
    }
}
