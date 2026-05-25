<?php

namespace App\Listeners;

use App\Events\ProjectIdeaEvaluated;
use App\Events\UserCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof ProjectIdeaEvaluated) {
            $this->handleProjectIdeaEvaluated($event);
        } elseif ($event instanceof UserCreated) {
            $this->handleUserCreated($event);
        }
    }

    /**
     * Handle ProjectIdeaEvaluated event.
     */
    protected function handleProjectIdeaEvaluated(ProjectIdeaEvaluated $event): void
    {
        $project = $event->project;
        $recipients = $this->resolveProjectRecipients($project);

        $subject = "Actualización de estado: " . $project->title;
        $view = 'emails.projects.evaluated';
        
        $content = [
            'projectTitle' => $project->title,
            'status' => $event->status,
            'comments' => $event->comments,
            'url' => route('projects.show', $project->id)
        ];

        foreach ($recipients as $recipient) {
            $this->notificationService->sendNotification($recipient, $subject, $view, $content);
        }
    }

    /**
     * Handle UserCreated event.
     */
    protected function handleUserCreated(UserCreated $event): void
    {
        $user = $event->user;
        $recipient = $user->email;
        $subject = "Bienvenido a ABI";
        $view = 'emails.users.welcome';

        $content = [
            'name' => $event->data['name'] ?? $user->name,
            'role' => $user->role,
            'url' => route('login')
        ];

        if ($recipient) {
            $this->notificationService->sendNotification($recipient, $subject, $view, $content);
        }
    }

    /**
     * Resolve who should receive project notifications.
     */
    protected function resolveProjectRecipients($project): array
    {

        $emails = [];

        // Add students
        foreach ($project->students as $student) {
            if ($student->user && $student->user->email) {
                $emails[] = $student->user->email;
            }
        }

        // Add professors
        foreach ($project->professors as $professor) {
            if ($professor->user && $professor->user->email) {
                $emails[] = $professor->user->email;
            }
        }

        return array_unique($emails);
    }
}
