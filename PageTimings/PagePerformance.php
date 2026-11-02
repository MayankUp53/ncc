<?php
require_once __DIR__ . '/configuration.php';
date_default_timezone_set('Asia/Tokyo');

class PagePerformance {
    public $startTimePHP = 0;
    public $endTimePHP = 0;
    public $totalTime = 0;
    public $dbResource = array('dbtype' => '', 'resource' => null);
    const CRM_PROPS_FILE = 'crmprops';

    public function __construct($initiate = false) {
        global $debug_enable, $js_debug_enable, $startTime;
        $this->startTimePHP = $this->current_millis($startTime ?? null);
        if ($initiate && $debug_enable && $js_debug_enable) {
            echo '<script src="PageTimings/PagePerformance.js"></script><script>var obj = new PagePerformance();obj.directory="PageTimings";</script>';
        }
    }

    public function debugEnd() {
        global $debug_enable, $php_debug_enable, $js_debug_enable, $show_on_screen, $db_config, $setup_context, $db;
        $this->endTimePHP = $this->current_millis();
        $this->totalTime = $this->endTimePHP - $this->startTimePHP;
        if (!$debug_enable) { return; }
        $uniqueId = date('YmdHis') . sprintf('%03d', (int) (microtime(true) * 1000) % 1000) . mt_rand(100, 999);
        if ($php_debug_enable) {
            $values = array();
            foreach (array('phone', 'campaignId', 'sessionId', 'userId', 'crm_push_generated_time', 'crm-push-received-time', 'client-server-time-diff', 'network-latency') as $key) {
                $values[$key] = crm_request_value($_SESSION ?? array(), $key, crm_request_value($_REQUEST, $key));
            }
            $pushReceived = $values['crm-push-received-time'];
            $urlTimestamp = crm_request_value($_REQUEST, 'url-time-stamp');
            if ($urlTimestamp !== '') { $pushReceived = (strtotime($urlTimestamp) ?: 0) * 1000; }
            $latency = is_numeric($values['network-latency']) ? $values['network-latency'] : '0';
            $difference = is_numeric($values['client-server-time-diff']) ? $values['client-server-time-diff'] : '0';
            $resource = $this->dbResource['resource'];
            $config = $db_config;
            if ($resource instanceof \PgSql\Connection || $resource instanceof \mysqli) {
                $config['database'] = $this->dbResource['dbtype'];
                $owned = false;
            } else {
                $resource = $this->connect($config);
                $owned = true;
            }
            if ($resource !== false) {
                $postgres = $config['database'] === 'postgres';
                $campaignColumn = 'campaign_id';
                if ($postgres) {
                    // The bundled timing schema uses campaign; some deployments use campaign_id.
                    $metadata = @pg_meta_data($resource, 'crm_serve_history');
                    if ($metadata === false || (!isset($metadata['campaign_id']) && !isset($metadata['campaign']))) {
                        error_log('Timing history schema is unavailable or has no campaign column.');
                        if ($owned) { $this->disconnect($config, $resource); }
                        return;
                    }
                    $campaignColumn = isset($metadata['campaign_id']) ? 'campaign_id' : 'campaign';
                }
                $params = array($uniqueId, 'http://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? ''), $values['phone'], $values['campaignId'], $values['sessionId'], $values['userId'], $this->startTimePHP, $this->endTimePHP, $this->totalTime, $values['crm_push_generated_time'], $pushReceived, $difference, $latency);
                if ($postgres) {
                    foreach (array(6, 7, 9, 10) as $index) { $params[$index] = $this->millisecondsToPostgresTimestamp($params[$index]); }
                } else {
                    foreach (array(9, 10) as $index) { $params[$index] = is_numeric($params[$index]) ? $params[$index] : 0; }
                }
                $placeholders = $postgres ? implode(',', array_map(function ($n) { return '$' . $n; }, range(1, count($params)))) : implode(',', array_fill(0, count($params), '?'));
                $sql = 'INSERT INTO crm_serve_history (id,requested_url,phone,' . $campaignColumn . ',session_id,user_id,serve_start_time,serve_end_time,total_serve_time,push_sent_time,push_received_time,client_server_time_diff,network_latency) VALUES (' . $placeholders . ')';
                $this->query($config, $sql, $resource, $params);
                if ($owned) { $this->disconnect($config, $resource); }
            }
        }
        if ($js_debug_enable) {
            echo '<script>var uniqueId=' . json_encode($uniqueId) . ';var phpExeTime=' . json_encode($this->totalTime) . ';var showOnScreen=' . json_encode((bool) $show_on_screen) . ';obj.debugEnd();</script>';
        }
    }

    public function current_millis($microTime = null) {
        if (is_string($microTime) && strpos($microTime, ' ') !== false) {
            list($usec, $sec) = explode(' ', $microTime, 2);
            return round(((float) $usec + (float) $sec) * 1000);
        }
        return round(microtime(true) * 1000);
    }

    public function connect($db_config) {
        if ($db_config['database'] === 'postgres') {
            // Own connections must not share the caller's pg_connect object.
            return crm_pg_connect(crm_db_connection_string(array(
                'host' => $db_config['dbhost'],
                'user' => $db_config['dbuser'],
                'dbname' => $db_config['dbname'],
                'password' => $db_config['dbpassword'],
                'port' => $db_config['dbport'] ?? ''
            )), PGSQL_CONNECT_FORCE_NEW);
        }
        if ($db_config['database'] === 'mysql') {
            try { return mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpassword'], $db_config['dbname']); }
            catch (mysqli_sql_exception $error) { error_log('Timing database connection failed.'); }
        }
        return false;
    }

    public function disconnect($db_config, $res) {
        if ($res === false || $res === null) { return; }
        if ($db_config['database'] === 'postgres') { pg_close($res); }
        elseif ($db_config['database'] === 'mysql') { mysqli_close($res); }
    }

    public function query($db_config, $query, $res, array $params = array()) {
        if ($res === false || $res === null) { return false; }
        if ($db_config['database'] === 'postgres') {
            return $params ? crm_pg_query($res, $query, $params) : crm_pg_query($res, $query);
        }
        if ($db_config['database'] === 'mysql') {
            try { return $params ? mysqli_execute_query($res, $query, $params) : mysqli_query($res, $query); }
            catch (mysqli_sql_exception $error) { error_log('Timing database query failed.'); }
        }
        return false;
    }

    public function millisecondsToPostgresTimestamp($milliseconds) {
        $milliseconds = is_numeric($milliseconds) ? (float) $milliseconds : 0.0;
        $wholeMilliseconds = (int) round($milliseconds);
        $seconds = (int) floor($wholeMilliseconds / 1000);
        $fraction = $wholeMilliseconds - $seconds * 1000;
        return date('Y-m-d H:i:s', $seconds) . sprintf('.%03d', $fraction);
    }
}
