# Agent Collaboration Rules

This repository uses persistent handover docs.

## Mandatory Update Rule
For every meaningful progress step, update documentation in `docs/rag_fastapi/`:
- `WORKLOG.md` (append-only progress log)
- `HANDOVER_STATUS.md` (current architecture, done, risks)
- `NEXT_STEPS.md` (what should be done next)

Before finishing a task, always ensure:
1. Worklog has a fresh entry with timestamp.
2. Handover status reflects the latest implementation state.
3. Next steps are updated and realistic.

## Logging Format
Use concise entries:
- Date/time
- Scope changed
- Files touched
- Verification result
- Remaining risk

## Scope
This rule applies to:
- AI assistants
- Developers
- Any automated coding agent working in this repo
