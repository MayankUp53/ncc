# NCC CRM

Custom PHP/PostgreSQL CRM integrated with Ameyo. This project does not use CakePHP.

The `customer.php` and `dispose.php` flow and its PHP dependencies have been updated for PHP 8.2.35. Other legacy entry points remain in the repository and have not received the same compatibility update.

## Runtime

- PHP 8.2.35 with `pgsql` and `mbstring`.
- PostgreSQL with the existing NCC application schema and Ameyo disposition data.
- An authenticated Ameyo session supplying the customer/call parameters to `customer.php`.

The application retains its existing deployment layout and database configuration. Copy `crmprops.sample` to `crmprops`, set deployment-specific values, and review `config.php` and `crmdb_connect.php` before deploying. Use the target server's PHP 8.2 handler rather than the bundled legacy PHP 5 server configuration.

`add_ncc_fields.sql` contains additional NCC fields for the existing customer/history tables. Review the deployed schema and back up the database before applying it; it is not a complete fresh-install schema.

## Verification and changes

See [PHP82_CUSTOMER_DISPOSE.md](PHP82_CUSTOMER_DISPOSE.md) for the exact changed files, PHP 8.2.35 checks, isolated PostgreSQL verification, and remaining staging checks. Production PostgreSQL/Ameyo integration still requires verification in the deployment environment.

Production configuration, private keys, logs, historical source backups, and full customer-data database dumps are excluded from Git. Keep those deployment files and data backups separately.
