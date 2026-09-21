# Configuring email and file storage

Everything the platform does that leaves the server — sending a welcome email, storing a two-gigabyte
video — is driven by two groups of settings. This is how to get both working locally and in
production, and how to tell when one of them is silently broken.

Nothing here needs a code change. Every setting is read in a config file (`config/mail.php`,
`config/filesystems.php`, `config/media.php`) from an environment variable, so all of it lives in
`.env`.

---

## The variables at a glance

### Email

| Variable | What it does |
| --- | --- |
| `MAIL_MAILER` | Which transport to use: `log`, `smtp`, `ses`, `postmark`, `resend`, `sendmail`, `array` |
| `MAIL_HOST`, `MAIL_PORT` | SMTP server and port (`smtp` mailer only) |
| `MAIL_USERNAME`, `MAIL_PASSWORD` | SMTP credentials (`smtp` mailer only) |
| `MAIL_SCHEME` | `smtp` (STARTTLS) or `smtps` (implicit TLS). Leave unset to let the port decide |
| `MAIL_FROM_ADDRESS` | The From address on every email the platform sends |
| `MAIL_FROM_NAME` | The From display name — defaults to `APP_NAME` |
| `MAIL_ADMIN_ADDRESSES` | Comma-separated extras copied on internal alerts (new registration, new upload) |
| `APP_URL` | Not a mail variable, but every link in every email is built from it |

### Storage

| Variable | What it does |
| --- | --- |
| `MEDIA_DISK` | Disk uploads go to: `s3` (normal) or `local` (no object store at hand) |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY` | Object-store credentials |
| `AWS_DEFAULT_REGION` | S3 region. MinIO ignores it but the SDK still requires one |
| `AWS_BUCKET` | Bucket name |
| `AWS_ENDPOINT` | Custom API endpoint. **Set for MinIO, leave empty for real S3** |
| `AWS_USE_PATH_STYLE_ENDPOINT` | `true` for MinIO, `false` for real S3 |
| `AWS_URL` | Optional public base URL for `Storage::url()`. Not used by signed playback links |
| `MEDIA_MAX_UPLOAD_KB` | Per-file size cap in KB (default `2097152` = 2 GB) |
| `MEDIA_MAX_FILES_PER_UPLOAD` | How many files one form submission may carry (default 10) |
| `MEDIA_TEMPORARY_URL_MINUTES` | How long a playback/download link stays valid (default 30) |

---

## Two things that must be true either way

**1. `APP_URL` must be the URL people actually type.** Emails are sent from a queue worker or a CLI
process that has no incoming request to learn the host from, so every link in them — the
password-set link, "Track this project", "Download your files" — is built from `APP_URL`. Get it
wrong and your emails send perfectly and land with links to `http://localhost`.

```dotenv
APP_URL=https://app.mpowerfounders.com
```

**2. After editing `.env` in production, rebuild the config cache.** If `php artisan config:cache`
has ever been run, the cached file is what the app reads and `.env` is ignored entirely:

```sh
php artisan config:clear && php artisan config:cache
```

---

## Local development

### Email locally

The default needs no setup at all:

```dotenv
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@mpowerfounders.test"
MAIL_FROM_NAME="MPower Founders"
```

Every email is written to `storage/logs/laravel.log` instead of being sent. To read one, tail the log
while you click through the flow:

```sh
php artisan pail          # or: tail -f storage/logs/laravel.log
```

That is enough to confirm an email fired, but the HTML is hard to judge as a wall of log text. To see
the templates as a recipient would, run **Mailpit** and point SMTP at it:

```sh
docker run -d --name mailpit -p 1025:1025 -p 8025:8025 axllent/mailpit
```

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_SCHEME=null
```

Mail then appears at <http://localhost:8025> and nothing leaves your machine. This is the
recommended local setup — the transactional templates are the client's first impression and worth
looking at.

### Storage locally (MinIO)

MinIO speaks the S3 API, so local development runs the exact same code path as production.

```yaml
# docker-compose.yml
services:
  minio:
    image: minio/minio:latest
    container_name: projects_minio
    restart: unless-stopped
    command: server /data --console-address ":9001"
    environment:
      MINIO_ROOT_USER: admin
      MINIO_ROOT_PASSWORD: change-this-to-a-strong-password
    ports:
      - "9100:9000"   # API — this is what AWS_ENDPOINT points at
      - "9101:9001"   # web console
    volumes:
      - minio_data:/data

volumes:
  minio_data:
```

Bring it up, then **create the bucket once** — the app does not create it for you, and a missing
bucket fails every upload:

```sh
docker compose up -d
```

Open the console at <http://localhost:9101>, sign in with the root user and password above, and
create a bucket named `mpower-media`. Leave it **private**; the app never serves objects publicly, it
signs a short-lived URL per request.

Then in `.env`:

```dotenv
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=admin
AWS_SECRET_ACCESS_KEY=change-this-to-a-strong-password
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=mpower-media
AWS_ENDPOINT=http://localhost:9100
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_URL=
```

`AWS_USE_PATH_STYLE_ENDPOINT=true` matters: MinIO addresses buckets as `host:9100/bucket/key`, while
real S3 uses `bucket.s3.region.amazonaws.com/key`. With it set to `false` the SDK will try to resolve
a hostname that does not exist.

> **If your app also runs in Docker**, `localhost:9100` is the container's own localhost, not MinIO.
> Resist the urge to use the internal hostname (`http://minio:9000`) and rewrite the host afterwards:
> a presigned URL signs the host it was generated for, so a rewritten link is rejected as an invalid
> signature. Instead give MinIO one hostname that resolves **the same from both sides** — add
> `s3.local` to your machine's `/etc/hosts` pointing at `127.0.0.1`, give the MinIO service a network
> alias of `s3.local` in compose, and set `AWS_ENDPOINT=http://s3.local:9100` everywhere.

### Storage locally without any object store

If you just want to click through the app, skip MinIO:

```dotenv
MEDIA_DISK=local
```

Files land in `storage/app/private`. Playback and download links still work — the local disk has
`serve => true`, so Laravel signs a route instead of an S3 URL. This path is not what production
runs, so verify uploads against MinIO before shipping storage changes.

The test suite always uses a faked local disk (`MEDIA_DISK=local` in `phpunit.xml`), so `php artisan
test` needs neither MinIO nor credentials.

---

## Production

### Email in production

Pick one transport. All of them read `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`.

**SMTP** — works with any provider, no extra package:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_SCHEME=smtp
MAIL_FROM_ADDRESS="hello@mpowerfounders.com"
MAIL_FROM_NAME="MPower Founders"
MAIL_ADMIN_ADDRESSES=studio@mpowerfounders.com
```

Port 587 with `MAIL_SCHEME=smtp` (STARTTLS) is the usual choice; port 465 needs
`MAIL_SCHEME=smtps`.

**Amazon SES** — already usable, since `aws/aws-sdk-php` ships with the S3 adapter:

```dotenv
MAIL_MAILER=ses
```

SES credentials come from `config/services.php`, which reads the **same** `AWS_ACCESS_KEY_ID` /
`AWS_SECRET_ACCESS_KEY` / `AWS_DEFAULT_REGION` as the S3 disk. If you use SES for mail and S3 for
storage, that one IAM user needs both sets of permissions — worth knowing before you write a
tightly-scoped storage-only policy and wonder why mail stopped.

**Postmark** or **Resend** each need one package first:

```sh
composer require symfony/postmark-mailer symfony/http-client   # then MAIL_MAILER=postmark, POSTMARK_TOKEN=...
composer require resend/resend-php                             # then MAIL_MAILER=resend, RESEND_KEY=...
```

Whichever you choose, the From domain needs **SPF and DKIM records** published, or activation emails
and password links will land in spam — which reads to a new client as the platform being broken.

`MAIL_MAILER=log` in production means every email is silently written to a file. Check this first if
clients report never hearing from you.

### Storage in production (real S3)

```dotenv
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=AKIA...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=eu-west-1
AWS_BUCKET=mpower-media-production
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_URL=
```

`AWS_ENDPOINT` **must be empty** for real S3 — a leftover MinIO endpoint is the single most common
way a working local setup fails on deploy.

Bucket settings:

- **Block all public access: on.** Objects are never public. Every playback and download is a
  presigned URL valid for `MEDIA_TEMPORARY_URL_MINUTES`, authorised per request by the app.
- **Versioning: your call.** Useful insurance against a deleted deliverable; it does cost storage.
- **Lifecycle rules: recommended.** Raw source footage is large and rarely re-read after delivery.
  Every object sits under the `projects/` prefix, so a rule on that prefix moving objects to
  Infrequent Access after 90 days is cheap to add. (Lifecycle filters match a literal prefix, not a
  glob, so you cannot target `source/` alone by path — tag those objects if you need that split.)

Minimum IAM policy for the app's user:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": ["s3:PutObject", "s3:GetObject", "s3:DeleteObject"],
      "Resource": "arn:aws:s3:::mpower-media-production/*"
    },
    {
      "Effect": "Allow",
      "Action": ["s3:ListBucket"],
      "Resource": "arn:aws:s3:::mpower-media-production"
    }
  ]
}
```

`s3:ListBucket` is included because of how S3 answers a request for an object that isn't there: without
permission to list the bucket it replies `403 Access Denied` rather than `404 Not Found`, so a file
deleted out from under the app looks like a credentials problem. The download route checks existence
before signing a URL, and this keeps that check honest.

### Upload size limits

`MEDIA_MAX_UPLOAD_KB` is only the application's own validation rule. A request larger than what the
web server or PHP accepts is rejected **before** Laravel ever sees it, and the user gets a blank 413
instead of a readable error. All three limits have to agree:

```ini
; php.ini — must be >= MEDIA_MAX_UPLOAD_KB
upload_max_filesize = 2G
post_max_size = 2G
max_execution_time = 600
memory_limit = 512M
```

```nginx
# nginx
client_max_body_size 2G;
# a slow 2 GB upload must not be cut off mid-transfer
client_body_timeout 600s;
fastcgi_read_timeout 600s;
```

`memory_limit` does not need to match the file size — uploads are streamed to the object store, not
read into memory.

### Email delivery is synchronous by default

Mailables are sent during the request. That is deliberate: it keeps local development simple and
means no email is lost to a queue worker nobody started. Delivery failures are caught and logged by
`App\Services\MailService` rather than thrown, so a dead mail host never rolls back an activation or
a stage change — but the request does wait on the mail server.

Under real load, move mail to the queue:

1. Add `implements ShouldQueue` to the mailables in `app/Mail`.
2. Set `QUEUE_CONNECTION=database` (the `jobs` table already exists from the base migrations).
3. Run a worker under a process supervisor: `php artisan queue:work --tries=3`.

Do not do step 1 without step 3, or email stops being sent at all.

---

## Verifying it works

Run both of these on the target environment after configuring it. They exercise exactly the same
code paths the app uses.

**Storage** — writes, reads, signs a URL, cleans up:

```sh
php artisan tinker --execute="
\$disk = config('media.disk');
Storage::disk(\$disk)->put('healthcheck.txt', 'ok');
echo 'disk: '.\$disk.PHP_EOL;
echo 'read back: '.var_export(Storage::disk(\$disk)->get('healthcheck.txt') === 'ok', true).PHP_EOL;
echo 'signed url: '.Storage::disk(\$disk)->temporaryUrl('healthcheck.txt', now()->addMinutes(5)).PHP_EOL;
Storage::disk(\$disk)->delete('healthcheck.txt');
"
```

Open the printed URL in a browser. If it downloads `ok`, storage is correctly configured end to end —
credentials, bucket, path style and signing. If it returns `SignatureDoesNotMatch`, your endpoint host
does not match the host the browser is reaching (see the Docker note above).

**Email** — sends a real message through the configured transport:

```sh
php artisan tinker --execute="
Mail::raw('MPower config check.', fn (\$m) => \$m->to('you@example.com')->subject('MPower config check'));
echo 'sent via: '.config('mail.default').PHP_EOL;
"
```

With `MAIL_MAILER=log` it prints `log` and the message is in `storage/logs/laravel.log`. With any
real transport, it should arrive.

To check a **template** rather than the transport, trigger the real flow: register an account, then
activate it from Admin → Clients. That sends the welcome email through the same path a client gets.

---

## Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| Emails never arrive, no error anywhere | `MAIL_MAILER=log` | Set a real transport; check `storage/logs/laravel.log` for the messages you missed |
| Email links point at `localhost` | `APP_URL` not set for the environment | Set `APP_URL`, then `php artisan config:cache` |
| Email arrives but lands in spam | No SPF/DKIM on the From domain | Publish the records your provider gives you |
| `.env` change appears to do nothing | Cached config is being read | `php artisan config:clear && php artisan config:cache` |
| Upload fails instantly, blank error page | Request exceeds PHP or nginx limits | Raise `post_max_size`, `upload_max_filesize`, `client_max_body_size` |
| Upload rejected with "must be 2048 MB or smaller" | Over `MEDIA_MAX_UPLOAD_KB` | Raise it — and the server limits above with it |
| `NoSuchBucket` on first upload | Bucket was never created | Create it in the MinIO console / S3 |
| `SignatureDoesNotMatch` when playing a file | Presigned URL host differs from the host the browser reached | Use one endpoint hostname that resolves the same from app and browser |
| `The specified bucket does not exist` in production, works locally | Leftover `AWS_ENDPOINT` from MinIO | Empty `AWS_ENDPOINT`, set `AWS_USE_PATH_STYLE_ENDPOINT=false` |
| Playback link works, download gives 403 | IAM user lacks `s3:ListBucket` | Add it — the existence check runs before signing |
| Files upload but the client sees nothing | File uploaded with "Share with the client" unticked | It is internal by design; re-upload shared, or check `visible_to_client` |

---

## Deploy checklist

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://app.mpowerfounders.com

MAIL_MAILER=smtp                 # never 'log' in production
MAIL_FROM_ADDRESS="hello@mpowerfounders.com"
MAIL_ADMIN_ADDRESSES=studio@mpowerfounders.com

MEDIA_DISK=s3
AWS_ENDPOINT=                    # empty for real S3
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_BUCKET=mpower-media-production
```

```sh
php artisan migrate --force
php artisan db:seed --force      # idempotent: permissions, roles, pipeline stages
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci && npm run build
```

Then run the two verification commands above against production before telling anyone the platform
is live.
