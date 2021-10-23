<?php

declare(strict_types=1);

namespace Shart;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Shart\Helpers\Revision;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * @return mixed
     */
    protected function load(string $class, string $id, array $params = [])
    {
        $revision = new Revision($id, $class);
        $latest = $revision->latest();
        $model = $class::query();
        $model->where('id', $latest);

        foreach ($params as $key => $value) {
            $model->where($key, $value);
        }

        if ($result = $model->first()) {
            return $result;
        }

        $this->throwHttpException(Response::HTTP_NOT_FOUND, 'Entry Not Found');
    }

    /**
     * @param $data
     * @param nullint $status
     */
    protected function newResponse($data = null, int $status = 200)
    {
        return new Response($data, null === $data ? Response::HTTP_NO_CONTENT : $status);
    }

    protected function throwHttpException(int $status, string $message)
    {
        throw new HttpException($status, $message);
    }
}
