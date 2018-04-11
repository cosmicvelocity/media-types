<?php

namespace CosmicVelocity\MediaTypes;

use InvalidArgumentException;

/**
 * It performs media type detection using mapping with php code.
 *
 * @package CosmicVelocity\MediaTypes
 */
class PhpArrayMediaTypes implements MediaTypes
{

    /**
     * Extension and media type mapping.
     *
     * @var array
     */
    private $extensionToType;

    /**
     * PhpRepository constructor.
     *
     * @param string|null $path Path of extension mapping file.
     *
     * @throws InvalidArgumentException The file is invalid.
     */
    public function __construct($path = null)
    {
        if (is_array($path)) {
            $this->extensionToType = $path;
        } else {
            if (empty($path)) {
                $path = realpath(__DIR__ . '/resources/types.php');
            }

            if (!file_exists($path)) {
                throw new InvalidArgumentException("File not found ({$path}).");
            }

            $this->extensionToType = include $path;
        }
    }

    /**
     * Add a mapping.
     *
     * @param string $extension extension.
     * @param string $type type.
     */
    public function addMapping($extension, $type)
    {
        $this->extensionToType[$extension] = $type;
    }

    /**
     * @inheritdoc
     */
    public function getMediaType($path)
    {
        $extension = substr(strrchr($path, '.'), 1);

        if ($extension === false) {
            throw new InvalidArgumentException("The extension can not be found in the specified file path ({$path}).");
        }

        $extension = strtolower($extension);

        if (!isset($this->extensionToType[$extension])) {
            throw new InvalidArgumentException("A media type corresponding to the extension could not be found ({$extension}).");
        }

        return MediaType::fromMime($this->extensionToType[$extension]);
    }

    /**
     * Get the extension of the file corresponding to mime type.
     *
     * @param string $mimeType Mime type.
     * @return array Extension that matches Mime type.
     */
    public function matchesExtension($mimeType)
    {
        $extensions = array_keys($this->extensionToType, $mimeType, true);

        if (0 < count($extensions)) {
            $extensions = array_map(function ($i) {
                return strtolower($i);
            }, $extensions);

            if (sort($extensions, SORT_NATURAL)) {
                $extensions = array_values($extensions);
            }
        }

        return $extensions;
    }

}
