<?php

declare(strict_types=1);

namespace App\Utility;

use PlainDataTransformer\Transform;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiRequest
{
    private ?Request $request;

    /**
     * @var array<string|int, mixed>|null
     */
    private ?array $data = null;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return array<string|int, mixed>
     */
    public function getData(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        if ($this->request === null) {
            $this->data = [];

            return $this->data;
        }

        $body = json_decode(Transform::toString($this->request->getContent()), true);
        $this->data = array_merge(
            Transform::toArray($this->request->query->all()),
            Transform::toArray($this->request->request->all()),
            Transform::toArray($body)
        );

        return $this->data;
    }

    /**
     * @param array<int, string> $params
     *
     * @return array<string, mixed>
     */
    public function getSelectedData(array $params): array
    {
        $data = $this->getData();
        $output = [];

        foreach ($params as $param) {
            $output[$param] = $data[$param] ?? null;
        }

        return $output;
    }

    public function getField(string $key): mixed
    {
        return $this->getRequest([$key])[$key];
    }

    public function getRequiredField(string $key): mixed
    {
        $value = $this->getField($key);

        if ($value === null) {
            throw new BadRequestException(sprintf('Missing parameter "%s".', $key));
        }

        return $value;
    }

    /**
     * @param array<int, string> $params
     *
     * @return array<string, mixed>
     */
    public function getRequest(
        array $params,
        bool $allowNullable = true
    ): array {
        $data = $this->getSelectedData($params);

        if ($allowNullable) {
            return $data;
        }

        return array_filter($data, fn ($item) => $item !== null);
    }
}
