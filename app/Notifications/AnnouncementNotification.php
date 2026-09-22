<?php

namespace App\Notifications;

use App\Models\SchoolAnnouncement;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification
{
    public function __construct(public SchoolAnnouncement $announcement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'audience' => $this->announcement->audience,
            'announcement_id' => $this->announcement->id,
        ];
    }
}