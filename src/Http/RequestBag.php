<?php

declare(strict_types=1);

namespace Glavweb\ExtraBundle\Http;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Replacement for removed Request::get() (attributes → query → request).
 */
final class RequestBag
{
    public static function get(Request $request, string $key, mixed $default = null): mixed
    {
        if ($request->files->has($key)) {
            return $request->files->get($key);
        }

        if ($request->attributes->has($key)) {
            return $request->attributes->get($key);
        }

        if ($request->query->has($key)) {
            return self::getInputValue($request->query, $key);
        }

        if ($request->request->has($key)) {
            return self::getInputValue($request->request, $key);
        }

        return $default;
    }

    /**
     * InputBag::get() кидает BadRequestException на массиве (например, attachmentFiles[0]=19) —
     * в этом случае отдаём значение как массив.
     */
    private static function getInputValue(InputBag $bag, string $key): mixed
    {
        try {
            return $bag->get($key);
        } catch (BadRequestException) {
            return $bag->all($key);
        }
    }
}
