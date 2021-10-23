<?php

declare(strict_types=1);

namespace Shart;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Http\Response;
use Shart\Services\LogService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Viloveul\Query\Search\DoctrineConnection;
use Viloveul\Query\Search\Expression;
use Viloveul\Query\Search\Parameter;

trait IndexAction
{
    /**
     * @param Authenticatable $auth
     *
     * @return mixed
     */
    public function index(ConnectionResolverInterface $db, LogService $logService, ? Authenticatable $auth)
    {
        if (null === $this->indexName) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Page Not Found');
        }

        return $this->search($this->indexName, $db->connection(), $logService, $auth);
    }

    /**
     * @param Authenticatable $auth
     */
    protected function search(string $name, ConnectionInterface $connection, LogService $logService, ? Authenticatable $auth)
    {
        $paths = [
            'config.json' => 'file://'.realpath(sprintf('%s/../database/queries/%s/config.json', __DIR__, $name)),
            'search.sql' => 'file://'.realpath(sprintf('%s/../database/queries/%s/search.sql', __DIR__, $name)),
            'count.sql' => 'file://'.realpath(sprintf('%s/../database/queries/%s/count.sql', __DIR__, $name)),
        ];
        $keys = array_keys($paths);
        $dbpath = database_path(sprintf('queries/%s', $name));

        foreach ($keys as $key) {
            if (is_file($dbpath.\DIRECTORY_SEPARATOR.$key)) {
                $paths[$key] = 'file://'.$dbpath.\DIRECTORY_SEPARATOR.$key;
            }
        }

        $doctrine = new DoctrineConnection($connection->getDoctrineConnection());
        $parameter = new Parameter($_GET);

        if (null != $auth) {
            $parameter->addFilter('roles', $auth->get('roles', []));
            $parameter->addFilter('username', $auth->getAuthIdentifier());
        }

        $search = new Expression($paths['search.sql'], $doctrine, app(Factory::class)->store());
        $search->configure($paths['config.json']);
        $search->withCount($paths['count.sql']);
        $search->withPrefix($connection->getTablePrefix());
        $search->listen(function ($sql, $bindings, $time) use ($logService) {
            $logService->query($sql, $bindings, $time);
        });
        $search->execute();

        return [
            'total' => $search->getTotal(),
            'items' => $search->getData(),
        ];
    }
}
