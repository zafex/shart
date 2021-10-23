<?php

declare(strict_types=1);

namespace Shart\Services;

use Closure;
use Illuminate\Events\NullDispatcher;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Shart\Authority;

class LogService
{
    /**
     * @var array
     */
    protected $auditLogs = [];

    /**
     * @var mixed
     */
    protected $authority;

    /**
     * @var mixed
     */
    protected $browser;

    /**
     * @var mixed
     */
    protected $identity;

    /**
     * @var mixed
     */
    protected $ip;

    /**
     * @var array
     */
    protected $queryLogs = [];

    /**
     * @var mixed
     */
    protected $url;

    public function __construct(Request $request, Authority $authority)
    {
        $this->authority = $authority;
        $this->identity = $request->getRequestIdentity();
        $this->ip = implode(', ', $request->getClientIps());
        $this->browser = $request->userAgent();
        $this->url = $request->fullUrl();
    }

    public function audit(string $id, string $name, string $action, array $params = [])
    {
        $key = 'table:'.$name.'-'.$id;

        if (\array_key_exists($key, $this->auditLogs)) {
            $idAudit = $this->auditLogs[$key]['ref']['id'];
        } else {
            $idAudit = Uuid::uuid4()->toString();
            $this->auditLogs[$key] = [
                'ref' => [
                    'id' => $idAudit,
                    'id_request' => $this->identity,
                    'id_session' => $this->authority->getSession(),
                    'id_object' => $id,
                    'entity' => $name,
                    'action' => $action,
                    'address' => $this->ip,
                    'browser' => $this->browser,
                    'url' => $this->url,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->authority->getAuthor(),
                ],
                'details' => [],
            ];
        }

        foreach ($params as $column => $value) {
            if (null !== $column && null !== $value) {
                $ckey = 'column:'.$column.'-'.$idAudit;

                if (\array_key_exists($ckey, $this->auditLogs[$key]['details'])) {
                    $this->auditLogs[$key]['details'][$ckey]['value'] = $value;
                } else {
                    $this->auditLogs[$key]['details'][$ckey] = [
                        'id' => Uuid::uuid4()->toString(),
                        'id_audit' => $idAudit,
                        'name' => $column,
                        'value' => $value,
                    ];
                }
            }
        }
    }

    public function execute()
    {
        $this->handleQuery();
        $this->handleAudit();
        $this->auditLogs = [];
        $this->queryLogs = [];
        $this->identity = Uuid::uuid4()->toString();
    }

    /**
     * @param array $params
     * @param $time
     */
    public function query(string $sql, ? array $params, $time = null)
    {
        $id = Uuid::uuid4()->toString();
        $details = [];
        $seq = -1;

        foreach ($params ?: [] as $param) {
            if (null !== $param) {
                $details[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'id_query' => $id,
                    'param' => $param,
                    'order' => ++$seq,
                ];
            }
        }

        $this->queryLogs[] = [
            'ref' => [
                'id' => $id,
                'id_request' => $this->identity,
                'id_session' => $this->authority->getSession(),
                'sql' => $sql,
                'time' => $time,
                'address' => $this->ip,
                'browser' => $this->browser,
                'url' => $this->url,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $this->authority->getAuthor(),
            ],
            'details' => $details,
        ];
    }

    protected function handle(Closure $handler)
    {
        $dispatcher = app('db')->getEventDispatcher();

        if ($dispatcher) {
            app('db')->setEventDispatcher(new NullDispatcher($dispatcher));
        }

        try {
            app('db')->transaction($handler);
        } finally {
            if ($dispatcher) {
                app('db')->setEventDispatcher($dispatcher);
            }
        }
    }

    protected function handleAudit()
    {
        $logs = [];
        $details = [];

        foreach ($this->auditLogs as $object) {
            $logs[] = $object['ref'];

            foreach ($object['details'] as $detail) {
                $details[] = $detail;
            }
        }

        $this->handle(function () use ($logs, $details) {
            app('db')->table('log_audit')->insert($logs);
            app('db')->table('log_audit_detail')->insert($details);
        });
    }

    protected function handleQuery()
    {
        $logs = [];
        $details = [];

        foreach ($this->queryLogs as $object) {
            $logs[] = $object['ref'];

            foreach ($object['details'] as $detail) {
                $details[] = $detail;
            }
        }

        $this->handle(function () use ($logs, $details) {
            app('db')->table('log_query')->insert($logs);
            app('db')->table('log_query_detail')->insert($details);
        });
    }
}
