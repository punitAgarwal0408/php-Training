# Training Sessions Management (CakePHP 3.8)

Assignment implementation for Training Sessions CRUD.

## Deliverables
- `src/Model/Table/TrainingSessionsTable.php`
- `src/Model/Entity/TrainingSession.php`
- `src/Controller/TrainingSessionsController.php`
- `src/Template/TrainingSessions/` (index.ctp, view.ctp, add.ctp, edit.ctp)
- `src/Model/Table/RegistrationsTable.php`
- `src/Model/Entity/Registration.php`
- `src/Controller/RegistrationsController.php`
- `src/Template/Registrations/` (index.ctp, view.ctp, add.ctp)
- `config/Migrations/20260201_CreateTrainingSessions.php`
- `config/Migrations/20260201_CreateRegistrations.php`
- `src/Template/Element/pagination.ctp`

## Notes
- Validation enforces: title required; start date must be future; end date must be after start.
- Registration rules: users cannot register twice; registrations are prevented when session is full.
- Virtual fields in entity: `duration`, `is_full` (requires `registrations` relation to be loaded to reflect actual counts).
- Custom finders: `findUpcoming()` and `findByInstructor()` available on `TrainingSessionsTable`.

## Running
1. Add the migration file to your app and run migrations:

```bash
bin/cake migrations migrate
```

2. Seed or create sample `users` entries for instructors.
3. Access the `TrainingSessions` controller via `/training-sessions`.

If you want, I can add a `Registrations` table and scaffolding next. ✅

---

## Added for submission

- Tests: `tests/TestCase/Model/Table/RegistrationsTableTest.php` verifying capacity enforcement.
- Fixtures: `tests/Fixture/*` for quick test setup.
- `phpunit.xml.dist` and `tests/bootstrap.php` to run tests.
- `.gitignore` updated to ignore `tmp/` and `vendor/`.
- Composer `scripts` for `test`, `migrate`, and `serve` convenience.
- A GitHub Actions workflow at `.github/workflows/ci.yml` to run tests automatically on push.

## How to run locally (quick)

1. composer install
2. bin/cake migrations migrate
3. composer test

Notes:
- If you are using PHP 8.5, deprecation notices are logged to `tmp/logs/error.log` and do not affect functionality.
