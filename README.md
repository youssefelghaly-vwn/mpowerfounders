# MPower Founders

A production platform for founder media. Clients upload raw podcasts and video with a brief;
our team moves each project through an admin-defined production pipeline, uploads cuts and
deliverables against each stage, and the client is emailed at every step.

## How it fits together

### Accounts

Registration is an **application**, not a sign-up:

1. Someone registers at `/register`. The account is created with `status = pending` and **no role**,
   so it cannot sign in anywhere. They get a confirmation email; the team gets an alert.
2. An admin reviews it under **Admin → Clients** and activates it. Activation attaches the `client`
   role, stamps who activated it, and sends the welcome email.
3. The client can now sign in and lands in `/portal`.

Admins can also **add a client directly** (Admin → Clients → Add client). No password is invented
on their behalf — the account is created active and the welcome email carries a password-set link
built from a genuine password-reset token.

Suspending an account locks the user out on their very next request (`active` middleware) and emails
them why.

The first staff account cannot come from the UI, since registration only ever produces pending
clients. Create it with:

```sh
php artisan mpower:create-admin
```

### Super admin

A role can be flagged **super admin** (`roles.is_superadmin`). Anyone holding such a role passes
every ability check in the app without a single permission being attached to it — the `Gate::before`
hook in `AppServiceProvider` short-circuits, so new admin sections are reachable the moment they
exist, with no permission to seed or tick.

Two rules protect the flag:

- **Only a super admin can grant it.** Someone with `roles.manage` but not the flag has the field
  ignored — otherwise that permission would be a one-request escalation to full access.
- **The last super admin role cannot be demoted or deleted**, so the panel can't be locked shut.

The flag grants *abilities*, not role membership: a super admin still can't enter `/portal`, which is
gated on holding the `client` role. The seeded `admin` role carries the flag (and still holds every
permission, so it degrades sensibly if the flag is removed).

### Pipeline

**Admin → Pipeline** manages the stages every project travels through. A stage is a database row,
not a PHP enum, so the pipeline changes without a deploy. Each one carries:

| Field | Meaning |
| --- | --- |
| `position` | Order on the board |
| `is_default` | Where new uploads land (exactly one stage) |
| `is_final` | Reaching it completes the project and sends the delivery email |
| `notifies_client` | Whether arriving here emails the client at all |
| `client_message` | Copy included in that email |

A stage still holding projects cannot be deleted.

The seeded pipeline is Intake → Preparing → Editing → Internal review → Client review → Delivered.

### Projects

A project is one client request. It carries the brief, the client's source files, and — per stage —
our own notes and uploads. Every move is appended to `project_stage_histories`, which drives the
timeline the client sees.

Files are marked `visible_to_client`; internal working material never appears in the portal and
returns 403 on the media routes.

## Storage

> Full setup, production values, limits and troubleshooting: **[docs/CONFIGURATION.md](docs/CONFIGURATION.md)**.

Uploads go to S3. Locally that is MinIO, which speaks the same API:

```yaml
services:
  minio:
    image: minio/minio:latest
    command: server /data --console-address ":9001"
    environment:
      MINIO_ROOT_USER: admin
      MINIO_ROOT_PASSWORD: change-this-to-a-strong-password
    ports:
      - "9100:9000"
      - "9101:9001"
    volumes:
      - minio_data:/data
```

Create the bucket once from the console at <http://localhost:9101>, then point `.env` at it:

```dotenv
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=admin
AWS_SECRET_ACCESS_KEY=change-this-to-a-strong-password
AWS_BUCKET=mpower-media
AWS_ENDPOINT=http://localhost:9100
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Playback and downloads redirect to a short-lived signed object-store URL
(`MEDIA_TEMPORARY_URL_MINUTES`), so video bytes never stream through PHP. Objects are keyed
`projects/{reference}/{stage-or-source}/{ulid}.{ext}`.

Upload limits live in `config/media.php` (`MEDIA_MAX_UPLOAD_KB`, default 2 GB). PHP's own
`upload_max_filesize` and `post_max_size` must be at least as large, or the request never reaches
Laravel's validator.

## Email

> Transport setup for local and production: **[docs/CONFIGURATION.md](docs/CONFIGURATION.md)**.

Every action that changes what a client should expect sends mail. All of it goes through
`App\Services\MailService`, which logs delivery failures rather than throwing — a dead SMTP host
must not roll back an activation or a stage change.

| Trigger | To the client | To the team |
| --- | --- | --- |
| Registration | `RegistrationReceivedMail` | `NewRegistrationAlertMail` |
| Account activated | `AccountActivatedMail` | — |
| Client added by an admin | `ClientInvitationMail` (password link) | — |
| Account suspended | `AccountSuspendedMail` | — |
| Project submitted | `ProjectSubmittedMail` | `NewProjectAlertMail` |
| Moved to a stage with `notifies_client` | `ProjectStageChangedMail` | — |
| Team file shared | `ProjectMediaAddedMail` | — |
| Moved to a stage with `is_final` | `ProjectCompletedMail` | — |

Internal alerts reach every user holding a non-client role, plus anything in
`MAIL_ADMIN_ADDRESSES`. Templates share `<x-mail.layout>`; every one of them is rendered for real
by `tests/Feature/TransactionalEmailTest.php`.

## Architecture

Controller → FormRequest → Service → Repository → Model, the convention already used by the roles
and permissions screens.

- `app/Services` — orchestration and the only place email is raised (`ClientService`,
  `ProjectService`, `PipelineStageService`, `MediaStorageService`, `MailService`).
- `app/Repositories` — every query, so controllers never touch the query builder.
- `app/Policies` — `ProjectPolicy` and `ProjectMediaPolicy` gate the two audiences: staff by
  `projects.*` permission, clients to their own work.
- Middleware aliases: `role`, `permission` (pre-existing) and `active` (account must be activated).
- Navigation is covered by `tests/Feature/NavigationTest.php`, which enumerates every `admin.*.index`
  route and fails if one is missing from the sidebar — an admin section can't ship unreachable.

Permissions added for this feature: `pipeline.view`, `pipeline.manage`, `clients.view`,
`clients.manage`, `projects.view`, `projects.manage`. A `producer` role bundles the production ones;
`client` holds none at all — it exists purely to open the portal.

## Setup

```sh
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan mpower:create-admin
npm install && npm run build
composer dev
```

## Tests

```sh
php artisan test
```

Uploads are faked onto a local disk in tests (`MEDIA_DISK=local` in `phpunit.xml`) — no S3 or MinIO
needed to run the suite.

---

Built on [Laravel](https://laravel.com/docs). Framework documentation, Laracasts and the
contribution guide are all at <https://laravel.com/docs>.
