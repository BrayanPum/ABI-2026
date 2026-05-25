<?php

namespace App\Services;

use App\Mail\GenericNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Sends a notification using the generic mailable.
     *
     * @param string $recipient Destination email
     * @param string $subject Email subject
     * @param string $view Blade template path
     * @param array $content Data to pass to the view
     * @param array $attachments List of file paths or Attachment objects
     * @param string|null $sender Optional sender email (will use default if null)
     * @return void
     */
    public function sendNotification(
        string $recipient,
        string $subject,
        string $view,
        array $content = [],
        array $attachments = [],
        ?string $sender = null
    ): void {
        try {
            $mailable = new GenericNotificationMail($subject, $view, $content, $attachments);
            
            if ($sender) {
                $mailable->from($sender);
            }

            Mail::to($recipient)->send($mailable);
        } catch (\Exception $e) {
            Log::error("Failed to send notification to {$recipient}: " . $e->getMessage(), [
                'subject' => $subject,
                'view' => $view,
                'exception' => $e
            ]);
        }
    }

    /**
     * Alternative method for more complex logic if needed in the future.
     * (e.g., sender customization, multiple recipients, etc.)
     */
    public function send(array $config): void
    {
        $this->sendNotification(
            $config['recipient'],
            $config['subject'],
            $config['view'],
            $config['content'] ?? [],
            $config['attachments'] ?? []
        );
    }
}
