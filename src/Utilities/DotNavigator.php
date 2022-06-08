<?php

namespace GSpataro\Utilities;

abstract class DotNavigator
{
    /**
     * Store data
     *
     * @var array
     */

    protected array $data = [];

    /**
     * Store read only mode status
     *
     * @var bool
     */

    protected bool $readOnly = false;

    /**
     * Initialize the data array
     *
     * @param array $data
     * @return void
     */

    public function init(array $data): void
    {
        if (!empty($this->data)) {
            throw new Exception\DataAlreadyInitializedException(
                "The DotNavigator data array as already initialized and, for safety reasons, you can't overwrite it."
            );
        }

        $this->data = $data;
    }

    /**
     * Set an item
     *
     * @param string $tag
     * @param mixed $value
     * @return void
     */

    public function set(string $tag, mixed $value): void
    {
        if ($this->readOnly) {
            throw new Exception\ReadOnlyEnabledException(
                "The DotNavitator is in read only mode. You can't set variables."
            );
        }

        $keys = explode(".", $tag);
        $reference = &$this->data;

        foreach ($keys as $key) {
            $reference = &$reference[$key];
        }

        $reference = $value;
    }

    /**
     * Get an item
     *
     * @param string $tag
     * @return mixed
     */

    public function get(string $tag): mixed
    {
        $keys = explode(".", $tag);
        $reference = $this->data;

        foreach ($keys as $key) {
            if (!isset($reference[$key])) {
                $reference = null;
                break;
            }

            $reference = $reference[$key];
        }

        return $reference;
    }

    /**
     * Verify if an item is set
     *
     * @param string $tag
     * @return bool
     */

    public function has(string $tag): bool
    {
        $keys = explode(".", $tag);
        $reference = $this->data;
        $exists = true;

        foreach ($keys as $key) {
            if (!array_key_exists($key, $reference)) {
                $exists = false;
                break;
            }

            $reference = $reference[$key];
        }

        return $exists;
    }

    /**
     * Delete an item
     *
     * @param string $tag
     * @return void
     */

    public function unset(string $tag): void
    {
        if ($this->readOnly) {
            throw new Exception\ReadOnlyEnabledException(
                "The DotNavigator class is in read only mode. You can't unset variables."
            );
        }

        $keys = explode(".", $tag);
        $reference = &$this->data;

        for ($i = 0; $i < count($keys); $i++) {
            $key = $keys[$i];

            if (!isset($reference[$key])) {
                $reference = null;
                break;
            }

            if ($i < count($keys) - 1) {
                $reference = &$reference[$key];
            } else {
                unset($reference[$key]);
            }
        }
    }

    /**
     * Get all the data
     *
     * @return array
     */

    public function getAll(): array
    {
        return $this->data;
    }
}
