<?php

namespace League\JsonReference\Loader;

use League\JsonReference\JsonDecoder\JsonDecoder;
use League\JsonReference\JsonDecoderInterface;
use League\JsonReference\LoaderInterface;
use League\JsonReference\SchemaLoadingException;

final class FileGetContentsWebLoader implements LoaderInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var JsonDecoderInterface
     */
    private $jsonDecoder;

    /**
     * @param string               $prefix
     * @param JsonDecoderInterface $jsonDecoder
     */
    public function __construct($prefix, JsonDecoderInterface $jsonDecoder = null)
    {
        $this->prefix      = $prefix;
        $this->jsonDecoder = $jsonDecoder ?: new JsonDecoder();
    }

    /**
     * {@inheritdoc}
     */
    public function load($path)
    {
        $uri = $this->prefix . $path;
        set_error_handler(function () use ($uri) {
            throw SchemaLoadingException::create($uri);
        });
        $response = file_get_contents($uri);
        restore_error_handler();

        if (!$response) {
            throw SchemaLoadingException::create($uri);
        }

        return $this->jsonDecoder->decode($response);
    }
}
