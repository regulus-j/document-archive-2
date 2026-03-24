# Document Urgency Matrix Framework

## Overview

The Document Urgency Matrix is an automated system that classifies incoming documents by urgency/criticality using AI-powered analysis, then monitors response times and notifies stakeholders when documents remain unprocessed. It also enables authorized users to reroute inactive workflow steps to different recipients.

---

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                    DOCUMENT UPLOAD                               │
│                 DocumentController@uploadController              │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│              DocumentUrgencyAnalyzer::analyze()                  │
│                                                                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────┐       │
│  │ Keyword  │ │ Metadata │ │ Temporal │ │ LLM (Ollama) │       │
│  │  (25%)   │ │  (20%)   │ │  (15%)   │ │    (40%)     │       │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └──────┬───────┘       │
│       └─────────────┴────────────┴──────────────┘               │
│                         │                                        │
│                  Weighted Score                                   │
│                         │                                        │
│              ┌──────────┴─────────┐                              │
│              │  CRITICAL │ HIGH   │                              │
│              │  MEDIUM   │ LOW    │                              │
│              └──────────┬─────────┘                              │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│              Urgency Stored on Document                          │
│    + Active Workflows Updated (urgency + due_date)              │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│          MonitorDocumentUrgency (Scheduled: every 30 min)       │
│                                                                  │
│   ┌─────────────┐  ┌──────────────┐  ┌───────────────┐         │
│   │   Warning   │  │  Escalation  │  │  Inactivity   │         │
│   │  (approaching│  │  (exceeded   │  │  (no activity │         │
│   │   deadline) │  │   deadline)  │  │   detected)   │         │
│   └──────┬──────┘  └──────┬───────┘  └───────┬───────┘         │
│          │                │                   │                  │
│          └────────────────┴───────────────────┘                  │
│                           │                                      │
│               Email + In-App Notification                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## Urgency Levels

| Level      | Response Time | Warning At | Escalation At | Due Date Offset | Description |
|------------|--------------|------------|---------------|-----------------|-------------|
| **CRITICAL** | 4 hours    | 2 hours    | 4 hours       | Same day        | Legal deadlines, emergencies, compliance violations, court orders, safety hazards |
| **HIGH**     | 24 hours   | 12 hours   | 24 hours      | 1 business day  | Government submissions, financial deadlines, audit responses, expiring contracts |
| **MEDIUM**   | 3 days     | 2 days     | 3 days        | 3 business days | Standard business communications, approvals, reports, memos, proposals |
| **LOW**      | 7 days     | 5 days     | 7 days        | 7 business days | Informational documents, newsletters, FYI notices, reference materials |

---

## Analysis Sources (Weighted Scoring)

### 1. Keyword Analysis (25% weight)
Pattern-matches document title + description + content against curated lexicons per urgency level. Critical keyword matches are weighted 4x, high 3x, medium 2x, low 1x.

**Critical keywords:** urgent, emergency, immediately, asap, court order, subpoena, cease and desist, lawsuit, compliance violation, data breach, security incident, etc.

**High keywords:** deadline, time-sensitive, priority, expedite, action required, overdue, audit, regulatory, contract expiration, penalty, etc.

**Medium keywords:** request, approval, review, memo, report, proposal, recommendation, evaluation, budget, schedule, etc.

**Low keywords:** information, fyi, newsletter, announcement, bulletin, circular, advisory, dissemination, reference, archive, etc.

### 2. Metadata Analysis (20% weight)
Examines document attributes:
- **Purpose** from workflows (e.g., `appropriate_action` → high; `dissemination` → low)
- **Classification** (e.g., `Private` → bumps urgency up)
- **Categories** (e.g., legal, compliance, emergency, financial → bumps to high)

### 3. Temporal Signal Analysis (15% weight)
Regex-based detection of deadline language in content:
- "within X hours", "due today/tomorrow/immediately" → critical boost
- "deadline:", "expires on/by", "not later than", "must be submitted" → high boost
- "within X business days", "as soon as possible" → medium boost

### 4. LLM Analysis (40% weight) — Ollama
Sends a structured prompt to the local Ollama model (`qwen2.5:0.5b`) asking it to classify the document. Falls back gracefully if Ollama is unavailable — the system still works with heuristic-only scoring (keyword + metadata + temporal, re-normalized).

**Prompt format:**
```
LEVEL: [critical|high|medium|low]
CONFIDENCE: [0-100]
REASON: [one sentence]
```

### Score Combination
All four scores are combined using their weight multipliers. Each source provides a level (mapped to 1-4 numeric scale) and confidence. A forced override applies if any high-confidence source flags critical urgency.

---

## Files Created / Modified

### New Files

| File | Purpose |
|------|---------|
| `app/Services/DocumentUrgencyAnalyzer.php` | Core urgency analysis service with keyword, metadata, temporal, and LLM analyzers |
| `app/Mail/DocumentUrgencyAlert.php` | Mailable class for urgency email notifications (warning, escalation, inactivity, reroute) |
| `resources/views/mail/document-urgency-alert.blade.php` | Email template with alert-type-specific headers and styling |
| `app/Console/Commands/MonitorDocumentUrgency.php` | Scheduled Artisan command that scans active workflows and sends notifications |
| `app/Http/Controllers/WorkflowRerouteController.php` | Controller for rerouting workflow steps and fetching available recipients |

### Modified Files

| File | Changes |
|------|---------|
| `app/Models/Document.php` | Added urgency fields to `$fillable` and `$casts`; added `urgency_color` and `urgency_icon` accessors |
| `app/Models/DocumentWorkflow.php` | Added `last_activity_at`, `inactivity_notified_at`, `is_rerouted` to `$fillable`; added `$casts`; added `rerouteLogs()`, `isInactive()`, `recipientUser()`, `senderUser()` methods |
| `app/Http/Controllers/DocumentController.php` | `uploadController()`: triggers urgency analysis after document creation. `show()`: loads reroute logs and passes `$canReroute` to view |
| `routes/web.php` | Added `POST workflows/{workflow}/reroute` and `GET workflows/{document}/reroute-recipients` routes |
| `resources/views/documents/show.blade.php` | Added urgency analysis panel, reroute button per workflow card, reroute history section, reroute modal with JS |
| `app/Providers/AppServiceProvider.php` | Registered `documents:monitor-urgency` command to run every 30 minutes |

### Migration

| Migration | Tables/Columns |
|-----------|---------------|
| `2026_03_02_021128_add_urgency_analysis_fields_to_documents` | `documents`: 7 urgency columns. `document_urgency_notifications`: notification tracking. `workflow_reroute_logs`: reroute history. `document_workflows`: 3 activity tracking columns |

---

## Database Schema

### documents (new columns)
```sql
urgency_level          ENUM('low','medium','high','critical') NULL
urgency_reasoning      TEXT NULL
urgency_keywords       JSON NULL
urgency_analyzed_at    TIMESTAMP NULL
urgency_confidence     INT NULL            -- 0-100
urgency_escalated_at   TIMESTAMP NULL
escalation_count       INT DEFAULT 0
```

### document_urgency_notifications
```sql
id                     BIGINT PK
document_id            FK → documents
workflow_id            FK → document_workflows (nullable)
notification_type      VARCHAR              -- warning, escalation, inactivity, reroute
recipient_user_id      FK → users (nullable)
sent_at                TIMESTAMP NULL
created_at, updated_at TIMESTAMPS
```

### workflow_reroute_logs
```sql
id                     BIGINT PK
document_id            FK → documents
workflow_id            FK → document_workflows
old_recipient_id       FK → users
new_recipient_id       FK → users
rerouted_by            FK → users
reason                 TEXT NULL
old_status             VARCHAR NULL
created_at, updated_at TIMESTAMPS
```

### document_workflows (new columns)
```sql
last_activity_at       TIMESTAMP NULL
inactivity_notified_at TIMESTAMP NULL
is_rerouted            BOOLEAN DEFAULT false
```

---

## Notification Flow

### Automatic Notifications (via Scheduler)

The `documents:monitor-urgency` command runs every 30 minutes and:

1. **Warning** — Sent when a workflow step is approaching its response deadline. Cooldown: 8 hours (won't re-send within that window).

2. **Escalation** — Sent when the deadline is exceeded. Notifies both the assigned recipient AND the document sender/uploader. Increments the document's `escalation_count`. Cooldown: 4 hours.

3. **Inactivity** — Sent when a workflow step has no activity (based on `last_activity_at`) for longer than the urgency-specific inactivity threshold:
   - Critical: 1 hour
   - High: 6 hours
   - Medium: 24 hours
   - Low: 72 hours

### Reroute Notifications

When a workflow step is rerouted:
- **New recipient**: Gets email + in-app notification with document details and reason
- **Old recipient**: Gets in-app notification that the step was reassigned
- **Document uploader** (if different from rerouter): Gets in-app notification

---

## Rerouting System

### Who Can Reroute
- Document uploader (owner)
- Company Admin
- Super Admin

### How It Works
1. On the document show page, each active (pending) workflow step shows a **Reroute** button
2. Clicking opens a modal that:
   - Shows the current assignee
   - Loads available recipients (same company users) via AJAX
   - Requires a reason for the reroute
3. On submit:
   - Workflow recipient is changed
   - Status is reset to `pending`
   - Activity timestamps are refreshed
   - Audit log entry created
   - All stakeholders notified
   - Reroute history recorded in `workflow_reroute_logs`

### Reroute History
Displayed on the document show page below the workflow pipeline, showing:
- Old → New recipient
- Who rerouted and why
- Relative timestamp

---

## Scheduler Setup

The monitoring command is registered to run every 30 minutes. To activate it, add this cron entry on the server:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Or on Windows, create a Task Scheduler entry running:
```
php artisan schedule:run
```

### Manual Execution
```bash
php artisan documents:monitor-urgency
```

---

## Error Handling

- **LLM unavailable**: The system falls back to heuristic-only analysis (keyword + metadata + temporal). The `source` field will be `'heuristic'` instead of `'combined'`.
- **Urgency analysis failure**: Wrapped in try/catch in `uploadController()` — a failure is non-blocking and logged as a warning. Document upload still succeeds.
- **Email send failure**: Each email send is individually try/caught. Failures are logged but don't prevent in-app notifications or other operations.
- **Migration idempotent**: Uses `Schema::hasColumn()` / `Schema::hasTable()` guards so it can safely re-run without errors.
