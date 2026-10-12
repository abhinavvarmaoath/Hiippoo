# Modernisation notes

Running log for the Hiippoo modernisation effort. Keep this updated as work
progresses so a new session can pick up without re-discovering everything.

## What this is

A 2016 class project (SJSU) — a study-group site: sign up, create/join a
team, group chat per team, per-team file sharing, a location page, two
embedded games, a contact form. Confirmed with the user: no live deployment,
no real users or data. Plain procedural PHP, no framework, no build step.

## Decisions made this session

- **Dead/portfolio repo, no live users or data** (user-confirmed). Changes
  were made freely on that basis.
- **Rotate the leaked DB password and personal emails now, not later**
  (user-confirmed). Done: `db.php` reads `DB_USER`/`DB_PASSWORD` from the
  environment with no hardcoded fallback; contact-form addresses replaced
  with `example.com` placeholders. This does not purge the old values from
  earlier git history (nobody asked for a history rewrite, and this branch
  is shared) — only from the tree going forward.
- **`videocall.php` sidebar link**: removed outright (user chose this over
  stubbing it).
- **Scope approved**: safety net (schema, tests, CI, Docker), the
  non-behavioral security fixes, the PHP 8.x runtime port, dropping unused
  vendored libraries, and the password-hashing migration. Not approved/not
  attempted: dedup refactor of the repeated header/sidebar/DB-connect
  boilerplate, join-team semantics (anyone who types an existing team name
  joins it — flagged, not changed, needs a product decision), any UI
  framework upgrade.

## What was done (see git log for full detail per commit)

Runtime: ported `login.php`/`signup.php` off the removed `mysql_*` API;
fixed a separate PHP 8 fatal in `emailsent.php` (unquoted array keys parsed
as constants). App now runs on PHP 8.3 without fataling.

Security: fixed the login auth-bypass (session username was set before the
password check); added `exit` after every auth-gate redirect (pages were
rendering their protected body regardless); added the missing auth check to
`add_file.php`/`list_files.php`/`get_file.php` (no check at all before);
added team-membership checks everywhere a teamid/file id is accepted as
input, closing a cross-team IDOR on chat and files; parameterized every
remaining SQL query (several were unauthenticated injection points);
escaped all HTML output (stored XSS via username, team name, chat
messages, filenames); added CSRF protection to the forms that cause a real
state change (team create/join, chat post, file upload — not the three
static `.html` entry points, see below); migrated password hashing from
unsalted MD5 to `password_hash()`/Argon2id.

Correctness: fixed `emailsent.php` sending the contact email twice; fixed
a mysqli footgun in the SQL-parameterization pass that was silently
storing every uploaded file with zero bytes of data (bind_param type 'b'
needs `send_long_data()` to actually send anything — switched to 's').
Caught by re-running the full upload/download flow at the end, not by the
parameterization work itself — worth remembering if this pattern (`bind_param`
with a blob column) comes up again.

Correction to the Phase 2 report: `signup.php`'s duplicate-username check
is **not** dead code as I originally claimed — `header()` without `exit`
still lets the rest of the script run, and the fetch/compare genuinely
executes and genuinely rejects duplicates. Verified by creating a duplicate
username against a scratch DB and confirming no second row was inserted.

Tooling: added `schema.sql` (no schema file existed before), Composer
(`phpunit` 12.5, `phpstan` 2.3), `docker-compose` + `Dockerfile` for local
dev, GitHub Actions CI (lint + PHPStan + PHPUnit against a MariaDB service
container), and HTTP-level characterization tests covering signup, login,
team create/join, chat, file upload/download, logout.

Cleanup: removed CKEditor, jQuery UI, jQuery File Upload and Morris.js
(~5.4MB, 389 files, zero references from any first-party page); removed
tracked `.DS_Store` files.

## Known limitation from this session

**Could not run `composer install`, PHPStan, or PHPUnit in this sandbox.**
It consistently hits GitHub's unauthenticated API rate limit resolving
these packages' dist archives (not a transient blip — failed identically
three times), and this session had no way to supply a GitHub token
(extracting one via `gh auth token` was correctly blocked by the sandbox's
own safety controls). `phar.phpunit.de` is blocked by this session's egress
policy; direct GitHub release URLs 404'd.

Everything was instead verified manually: a scratch MariaDB + `php -S`
instance, driven with `curl`, re-created and re-run after essentially every
commit in this session, including a full fresh end-to-end pass at the end
(signup → login → create team → chat → upload/download a file → logout,
plus deliberate SQL-metacharacter and XSS-payload inputs, plus deliberate
cross-team access attempts). That pass is what caught the file-upload blob
bug above. `composer.lock` is fully resolved and should install cleanly
in a normal environment — the committed CI workflow should work fine on
GitHub Actions since it isn't subject to the same rate limit. Worth an
early check next session: does CI actually go green.

## Open questions / not done

- **Join-team semantics**: `created.php`/`joined.php` both just insert a
  `(username, team)` row — anyone who knows or guesses a team name joins
  it outright, no invite or approval. Flagged in Phase 2, never got an
  explicit decision. Needs product input before changing.
- **CSRF on signup.html/login.html/email.html**: these are static `.html`,
  not PHP, so a session token can't be embedded without converting them to
  `.php` and fixing every link that points at the old filenames. Judged
  lower value anyway (signup/login CSRF has little attack value here; the
  contact form just sends an email) and left alone. Revisit if that
  reasoning doesn't hold up.
- **Dedup refactor**: the ~15 template pages still each paste the same
  ~300-line header/sidebar/footer and repeat `db_connect()` boilerplate.
  Flagged as a high-value, low-risk refactor in Phase 2; not started.
- **Bootstrap 3 → 5, jQuery 1.x → 3.x**: flagged, not started — this is a
  real UI effort, not a quick fix, and nobody's asked for it yet.
- **File storage in MySQL BLOBs**: noted in `schema.sql` as a scalability
  ceiling, not something to fix without a reason to.

## What's left to do

Nothing from the approved scope is outstanding. Next steps are whichever
of the open items above get approved, plus confirming CI actually passes
once it runs somewhere with normal GitHub access.
