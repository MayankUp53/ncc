# NCC CRM

Custom PHP/PostgreSQL CRM integrated with Ameyo. This project does not use CakePHP.

The `customer.php` and `dispose.php` flow and its PHP dependencies have been updated for PHP 8.2.35. Other legacy entry points remain in the repository and have not received the same compatibility update.

## Runtime

- PHP 8.2.35 with `pgsql` and `mbstring`.
- PostgreSQL with the existing NCC application schema and Ameyo disposition data.
- An authenticated Ameyo session supplying the customer/call parameters to `customer.php`.

The application retains its existing deployment layout. Copy `crmprops.sample` to `crmprops` and set deployment-specific values. `crmdb_connect.php` and the updated CRM endpoints use those same settings, including the password and optional `pgConnectionString.port`. Keep the host as a hostname/IP; configure the port separately. `config.php` holds Ameyo application settings. Use the target server's PHP 8.2 handler rather than the bundled legacy PHP 5 server configuration.

`add_ncc_fields.sql` contains additional NCC fields for the existing customer/history tables. Review the deployed schema and back up the database before applying it; it is not a complete fresh-install schema.

## Docker

The Dockerfile uses PHP 8.2.35 with Apache and the PostgreSQL/mbstring extensions. It connects to your existing PostgreSQL and Ameyo servers using `crmprops`; it does not start a database or import migrations.

On the server, run these commands from the project directory after creating/configuring `crmprops`:

```sh
docker build -t ncc-crm:8.2.35 .
docker run -d --name ncc-crm --restart unless-stopped \
  -p 127.0.0.1:18084:80 \
  --add-host=host.docker.internal:host-gateway \
  --mount type=bind,source="$(pwd)/crmprops",target=/var/www/html/ncc/crmprops,readonly \
  ncc-crm:8.2.35
```

If PostgreSQL runs directly on the same server as Docker, set these values in the host's `crmprops` file:

```ini
pgConnectionString.host = host.docker.internal
pgConnectionString.port = 5432
pgConnectionString.user = YOUR_POSTGRES_USER
pgConnectionString.password = YOUR_POSTGRES_PASSWORD
pgConnectionString.dbname = ncc
```

The `--add-host` option resolves this name to Docker's host gateway, avoiding a hardcoded bridge IP such as `172.17.0.1`. MySQL and PostgreSQL can use the same host address when both run on that server; their listening ports differ. Use the actual PostgreSQL port if it is not 5432. PostgreSQL must listen on an interface reachable from Docker, with the relevant container subnet permitted in `pg_hba.conf` and the host firewall.

If the Ameyo PostgreSQL database also runs on the Docker host, `pgConnectionString.ameyodbhost` can use `host.docker.internal` too. Keep `pgConnectionString.ameyohost` as the browser-reachable Ameyo domain because it is used for redirects. For a remote database, keep its actual hostname/IP; for another container on the same user-defined Docker network, use that database container's network name.

The `--mount` option mounts only the **configuration file** `crmprops`, read-only. PostgreSQL data remains on the existing database server; no database data directory is mounted into this PHP container. The real local `crmprops` values are not changed automatically by these examples.

Host port `18084` is reserved for NCC in this example; confirm it is available on the server. The container's internal port remains `80`, which does not conflict with other containers. Only the host's loopback interface publishes this port, for the existing Apache reverse proxy on the same host.

Add the following block **inside your existing `c047.ameyoj.com` HTTPS VirtualHost**, alongside the other application routes. Keep the existing SSL certificates, forwarded headers and port 18080–18083 routes as they are:

```apache
# NCC CRM — port 18084
RedirectMatch 301 ^/ncc$ /ncc/
ProxyPass        /ncc/ http://127.0.0.1:18084/ncc/
ProxyPassReverse /ncc/ http://127.0.0.1:18084/ncc/
```

The backend `/ncc/` prefix is required because the Docker image serves the application from `/var/www/html/ncc`. A copy of this host-side snippet is in `docker/host-apache-ncc.conf`; it is separate from the Apache configuration inside the container.

Validate the host Apache configuration with `apachectl configtest`, then reload the host's Apache service using its normal service command. These are server-side steps; this project does not change or reload your running host configuration.

The customer page is `https://c047.ameyoj.com/ncc/customer.php`, with the call/customer parameters supplied by Ameyo. Use `docker logs ncc-crm` for application/Apache errors. The container must be able to reach the database hosts configured in `crmprops`; `127.0.0.1` inside a container refers to that container.

Credentials, database dumps, private keys, logs and old source backups are excluded from the build context. Runtime `crmprops` is mounted read-only and blocked from direct HTTP access. Legacy `index.php` setup and `ping.php` configuration-output endpoints are also blocked in this container. Existing legacy/IVR routes still require separate deployment verification; the Dockerfile does not extend their PHP compatibility scope.

The Docker files were prepared without building or starting a container locally.

## Verification and changes

See [PHP82_CUSTOMER_DISPOSE.md](PHP82_CUSTOMER_DISPOSE.md) for the exact changed files, PHP 8.2.35 checks, isolated PostgreSQL verification, and remaining staging checks. Production PostgreSQL/Ameyo integration still requires verification in the deployment environment.

Production configuration, private keys, logs, historical source backups, and full customer-data database dumps are excluded from Git. Keep those deployment files and data backups separately.
