<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\DocumentWorkflow;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentUrgencyAlert extends Mailable
{
    use Queueable, SerializesModels;

    public Document $document;
    public ?DocumentWorkflow $workflow;
    public string $alertType; // 'warning', 'escalation', 'inactivity', 'reroute'
    public array $details;

    public function __construct(Document $document, ?DocumentWorkflow $workflow, string $alertType, array $details = [])
    {
        $this->document  = $document;
        $this->workflow  = $workflow;
        $this->alertType = $alertType;
        $this->details   = $details;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'warning'    => "[WARNING] Document \"{$this->document->title}\" requires attention",
            'escalation' => "[URGENT] Document \"{$this->document->title}\" has exceeded response time",
            'inactivity' => "[INACTIVE] Document \"{$this->document->title}\" has stalled in workflow",
            'reroute'    => "[REROUTED] Document \"{$this->document->title}\" workflow has been changed",
        ];

        return new Envelope(
            subject: $subjects[$this->alertType] ?? "[ALERT] Document \"{$this->document->title}\" needs attention",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.document-urgency-alert',
        );
    }
}
