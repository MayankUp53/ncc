<?php
$file = __DIR__ . '/crmprops';
$user = $host = $addNewnumbers = $dbname = $password = '';
$port = '';
$ameyohost = $ameyodbhost = $ameyodbname = $enablecrmdebug = $crmtype = '';

function readcrmconf($returntype = 0) {
    global $file;
    if (!is_readable($file) || ($fp = fopen($file, 'r')) === false) {
        if ($returntype == 1) { return false; }
        exit('CRMプロパティファイルのオープンエラー。ファイルの存在を確認してください');
    }
    $keys = array(
        'addNewnumbers' => 'addNewnumbers',
        'pgConnectionString.host' => 'host',
        'pgConnectionString.user' => 'user',
        'pgConnectionString.password' => 'password',
        'pgConnectionString.port' => 'port',
        'pgConnectionString.dbname' => 'dbname',
        'pgConnectionString.ameyohost' => 'ameyohost',
        'pgConnectionString.ameyodbhost' => 'ameyodbhost',
        'pgConnectionString.ameyodbname' => 'ameyodbname',
        'enableCrmDebug' => 'enablecrmdebug',
        'crmType' => 'crmtype'
    );
    while (($line = fgets($fp)) !== false) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || $line[0] === ';' || strncmp($line, '//', 2) === 0) { continue; }
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) { continue; }
        list($key, $value) = array_map('trim', $parts);
        if (!isset($keys[$key])) { continue; }
        if ($key === 'addNewnumbers' && !in_array($value, array('true', 'false'), true)) { continue; }
        $GLOBALS[$keys[$key]] = $value;
    }
    fclose($fp);
    return true;
}

/** Keep missing optional HTTP fields out of PHP 8's null/string conversions. */
function crm_request_value(array $source, string $key, string $default = ''): string {
    $value = $source[$key] ?? $default;
    if (!is_string($value) && !is_int($value) && !is_float($value)) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        exit('Invalid request parameter: ' . $key);
    }
    return (string) $value;
}

/** Build a libpq connection string from crmprops, quoting spaces, quotes and backslashes. */
function crm_db_connection_string(?array $settings = null): string {
    if ($settings === null) {
        $settings = array();
        foreach (array('host', 'user', 'dbname', 'password', 'port') as $key) {
            $settings[$key] = $GLOBALS[$key] ?? '';
        }
    }
    $parts = array();
    foreach (array('host', 'user', 'dbname', 'password', 'port') as $key) {
        $value = (string) ($settings[$key] ?? '');
        if (($key === 'password' || $key === 'port') && $value === '') { continue; }
        $parts[] = $key . "='" . str_replace(array('\\', "'"), array('\\\\', "\\'"), $value) . "'";
    }
    return implode(' ', $parts);
}

/** Return database failures to the caller without emitting connection/customer data into responses. */
function crm_pg_connect(string $connectionString, int $flags = 0) {
    $connection = @pg_connect($connectionString, $flags);
    if ($connection === false) { error_log('CRM database connection failed.'); }
    return $connection;
}

function crm_pg_query($connection, string $query, array $params = array()) {
    $result = $params ? @pg_query_params($connection, $query, $params) : @pg_query($connection, $query);
    if ($result === false) { error_log('CRM database query failed.'); }
    return $result;
}
