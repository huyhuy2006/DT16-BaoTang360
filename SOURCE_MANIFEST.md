# DT-16 — Source manifest

## Stack và cấu trúc
- Backend: Laravel 11 / PHP API, Laravel Sanctum, Form Requests, Eloquent models, migrations, seeders, services, role middleware.
- Frontend: Vue 3 + Vite, Vue Router, Pinia, Vue I18n, responsive layout, theme switching, Pannellum 360 viewer.
- Python: FastAPI ETL clean-up, analytics summary, topic recommender and health endpoint; Laravel integration uses timeout/fallback.
- Data: CSV file contains exactly 300 mock rows (excluding header), divided evenly among 10 record types. Database seeder creates >300 relational app records for local demonstration.
- Media: sample panorama tiles, preview images, artifact/tour/space images, demo audio.
- Deployment assets: Docker Compose, PHP-FPM Dockerfile/entrypoint, Nginx configuration, environment example and PowerShell initializer.
- Full requirements: `docs/REQUIREMENTS_DT16.md`; traceability `docs/FEATURE_TRACEABILITY.csv` maps all 22 functional requirements to source/API paths; `docs/NON_FUNCTIONAL_CHECKLIST.md` tracks all 17 nonfunctional requirements.

## Test definitions (not evidence of execution)
- PHPUnit: 20 test methods across API feature test files.
- Playwright: 40 test declarations across 4 E2E files under `frontend/tests/e2e`; run them from `frontend` so the local package resolves `@playwright/test`.
- Total automated test definitions: 60.
- Manual test matrix: `05_KiemThu/60-ca-kiem-thu.csv` contains exactly 60 cases and 13 fields; actual result/tester/date/evidence are blank, and status is `Chưa chạy` until the project owner executes each test.

## Explicit limitations
- Source ZIP does not include `.git` history, a generated Composer lockfile, local `.env`, or installed package folders.
- Runtime PHP tests, Vite build and Playwright have not been claimed as passing merely from static source checks. Execute them on the target machine after installing Composer/npm dependencies.
- CSV data, database demo records, artifact text, and demo media are simulated. Replace/verify content and document usage licenses before real public release.
