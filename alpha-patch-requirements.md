# Alpha Patch Requirements

## Scope
Fix email verification 500s and close remaining workflow-forwarding gaps for step-based forwarding, specific action capture, and dissemination consistency. No code edits should start until this plan is approved.

## Fix 1 - Email Verification 500s
1. Identify the exact 500 sources for both code submit and signed-link verification (review app logs and stack traces).
2. Harden `VerifiedEmailController::verify` against null/mismatched users and missing/expired codes so failures return validation errors instead of 500s.
3. Confirm the signed-link flow (`verification.verify` -> `VerifyEmailController`) returns a safe redirect and does not throw on invalid signatures or missing users.
4. Ensure resend/send actions fail gracefully (mail exceptions -> user-friendly errors) and do not break the verification notice view.

## Fix 2 - Forwarding Steps & Actions (create-forward page)
1. UI: when `appropriate_action` is selected for a step, display a required "Specific Action Needed" field and preserve its value when adding/removing/reordering steps.
2. Validation: enforce `action_required_batch[*]` for `appropriate_action` steps and bind the action text to the stored workflow `remarks` (or a dedicated field if added).
3. Recipients per step: confirm recipients are selectable via checkboxes (not radios) for both offices and users; ensure at least one recipient per step.
4. Sequential progression: verify that all recipients in the same `step_order` must complete before the next step activates (including office-expanded recipients).

## Fix 3 - Forwarding from Review (parallel workflows)
1. For `workflow_type=parallel` and `purpose=appropriate_action`, allow forwarding in steps with a specific action per step.
2. Controller: validate step recipients/actions, create child workflows as `sequential`, and store the per-step action in `remarks`.
3. Restrict step-based forwarding to `appropriate_action` only (hide UI and reject payloads for other purposes).

## Fix 4 - Dissemination Forwards in Parallel
1. When forwarding from a dissemination workflow in parallel mode, force the new workflow steps to remain `dissemination`.
2. Confirm notification text and labels reflect dissemination (information-only) intent.

## Touchpoints (expected)
- Controllers: `app/Http/Controllers/Auth/VerifiedEmailController.php`, `app/Http/Controllers/Auth/VerifyEmailController.php`, `app/Http/Controllers/DocumentWorkflowController.php`
- Views/JS: `resources/views/auth/verify-email.blade.php`, `resources/views/documents/forward.blade.php`, `resources/views/documents/review.blade.php`
- Model helpers (if needed): `app/Models/DocumentWorkflow.php`

## Testing/Validation Plan
1. Email verification: test signed-link and code flows with valid/invalid/expired inputs; confirm no 500s and correct redirects/messages.
2. Create-forward UI: build sequential steps with multiple recipients; verify required action text for appropriate_action and correct persistence on submit.
3. Sequential progression: complete multi-recipient steps and confirm the next step activates only after all recipients in the current step finish.
4. Review forward (parallel, appropriate_action): use step-based forwarding with actions per step; confirm child workflows created as sequential with correct remarks.
5. Dissemination forward: forward from a dissemination workflow and confirm new steps remain dissemination and notifications match.
6. Smoke routes: load verification and forwarding pages to ensure no blade/JS errors.
