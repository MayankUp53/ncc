# Customer/disposition PHP 8.2.35 update

This update covers `customer.php`, `dispose.php`, and their PHP dependencies in the current Ameyo CRM flow. Standalone CSS/JavaScript files, images, SQL files, nodeflows, older page copies, Apache configuration, and Git were not changed. The inline JavaScript in customer.php and dispose.php received the integration fixes described below. No files were deleted.

## Changed source files

1. `customer.php`
2. `dispose.php`
3. `readcrm.php`
4. `crmdb_connect.php`
5. `day.php`
6. `history.php`
7. `savedata.php`
8. `add_remove_applicable_dropdown.php`
9. `add_remove_response_dropdown.php`
10. `render.php`
11. `lib/debug_time.php`
12. `PageTimings/PagePerformance.php`
13. `PageTimings/configuration.php`
14. `PageTimings/browseRenderTimeLog.php`

`readcrm.php` is shared with other entry points; its existing globals and `readcrmconf()` interface remain available.

## Changes

- Replaced legacy short PHP opening tags in these files so they execute with `short_open_tag=Off`.
- Fixed configuration parsing, including blank lines, comments, malformed lines, values containing `=`, and missing configuration. PHP-only includes no longer emit trailing whitespace.
- Anchored includes and configuration paths to their source directories instead of changing the process working directory.
- Added `crm_request_value()` in the existing configuration helper to default missing optional inputs and reject arrays where strings are expected. Existing NCC age/gender validation and omitted optional-field behaviour are retained.
- Added defaults for missing/null display fields when rendering new or existing customers and removed an unused reference to a nonexistent disposition result column.
- Passed PostgreSQL connections explicitly. Customer rendering keeps shared connections open until request shutdown; separate timing connections use `PGSQL_CONNECT_FORCE_NEW`.
- Bound customer/history lookups, writes, dropdown values, and timing metadata as SQL parameters. Quotes, Japanese text and literal dollar placeholders stay in the values instead of entering SQL text.
- Saved customer and history updates in a single transaction in `dispose.php` and `savedata.php`. A failed update rolls back and prevents the Ameyo redirect.
- Updated timing code for `PgSql\Connection` objects, default constructor arguments, numeric timestamp handling, and missing request metadata. The optional MySQL branch uses MySQLi instead of removed `mysql_*` calls.
- Added JSON responses for invalid dropdown input and failed connections, preserving the existing request method and success messages.

## Additional review corrections

- Updated dropdown callbacks to accept JSON objects as well as strings; jQuery automatically parses the new JSON responses. Values containing ampersands/Japanese text are URL-encoded, and HTTP errors now show the existing failure messages.
- Preloaded dropdown queries before page output so query failures return a controlled response rather than passing false into pg_fetch functions.
- Preserved an existing customer ID when an inbound request identifies the customer by phone and omits its external customer ID.
- Preserved selfCallback passed in the query string. Built the Ameyo redirect with URL encoding and JSON-safe JavaScript strings, including sessions containing quotes or closing-script text.
- Stopped requests cleanly when configuration is missing. Database connection/query errors return false to their existing handlers and log generic messages instead of emitting SQL or customer data into responses.
- Made the PostgreSQL timing logger detect the known campaign/campaign_id schema variants. The bundled timing schema uses campaign, while the original logger expected campaign_id.

## Verification

Checks use a portable official **PHP 8.2.35** Windows runtime, verified against the official archive checksum. System PHP was not replaced.

- All 14 source files are linted with `short_open_tag=0`.
- **137 isolated endpoint checks passed**, covering existing/new customer rendering, nullable data, inbound customer lookup, invalid inputs, quick/normal disposition, optional field preservation/clearing, Japanese and quoted text binding, transactions/rollback, history, dropdown operations and timing endpoints.
- **15 parser/timing checks passed**, covering configuration parsing, no unintended output, missing configuration, disabled timing, timestamp precision and legacy timestamp parsing.
- Those endpoint checks stub PostgreSQL connections and queries. They perform no live writes.
- A second review passed **89 assertions against a separate temporary PostgreSQL 15.15 cluster**, using table definitions from ncc.sql and the bundled timing SQL, plus add_ncc_fields.sql. Only synthetic fixture data was written. Checks cover real customer/history creation, updates, Japanese/quoted text round-trips, dropdown soft deletion, preserved optional fields, constraint-triggered rollback, missing configuration, failed queries/connections, and both timing column variants.
- Node.js checks exercised **12 dropdown callback scenarios** with object/string responses and failure callbacks, plus redirect execution and URL decoding, including quoted session values. These use DOM stubs rather than a full browser.
- No production database was connected to and no Ameyo redirect was followed. The temporary PostgreSQL test server is stopped after verification. These checks do not certify the deployed database schema or external integration.
- Verification scripts are outside the project, in the backup directory's `verification` subdirectory; no test endpoints were added to the public application.

## Before deployment

Use PHP **8.2.35** with `pgsql` and `mbstring` enabled. Enable `mysqli` only if the optional MySQL timing integration is used. Configure the target web server's PHP handler separately; the bundled server configuration was not changed.

On staging, verify both existing and new customers, saving, quick and normal dispositions, callbacks/Ameyo redirects, all NCC fields, dropdown changes, history and timing logging. Check PHP logs with `E_ALL`, `display_errors=Off`, and `log_errors=On`.

Previously identified frontend issues remain outside this PHP update: the legacy edit/save JavaScript uses `Ajax.Request` without loading Prototype in `customer.php`, and some CSS image references point to missing paths. The personal-information columns used by savedata.php are absent from the supplied NCC dump, so that legacy endpoint still needs verification against the actual deployed schema; its SQL binding/rollback was covered by the isolated checks. The optional MySQL timing path was not tested against a live MySQL server.

## Backup and rollback

A full copy of all 154 application files and their SHA-256 manifest was saved before editing:

`C:\Users\MAYANK~1.IPS\AppData\Local\Temp\ncc-scoped-php82-originals-1_oi7q_o`

The backup excludes Git internals. It includes the existing SQL migrations and other user files. Restore only the 14 changed source paths to undo this update; remove this report if no longer needed. Keep a durable copy before relying on this temporary location for production rollback.
