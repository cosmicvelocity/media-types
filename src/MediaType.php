<?php

namespace CosmicVelocity\MediaTypes;

use InvalidArgumentException;
use SplFileInfo;

/**
 * Abstract media types.
 *
 * @package CosmicVelocity\MediaTypes
 */
class MediaType
{

    /**
     * valid suffix.
     *
     * @var array
     */
    private static $validSuffix = [
        'xml',
        'json',
        'ber',
        'der',
        'fastinfoset',
        'wbxml',
        'zip',
        'cbor',
    ];

    /**
     * valid tree.
     *
     * @var array
     */
    private static $validTree = [
        'vnd',
        'prs',
        'x',
    ];

    /**
     * valid type.
     *
     * @var array
     */
    private static $validType = [
        'application',
        'audio',
        'example',
        'font',
        'image',
        'message',
        'model',
        'multipart',
        'text',
        'video',
    ];

    /**
     * valid pattern for type and sub type.
     *
     * @var string
     */
    private static $validTypeAndSubTypePattern = '/^[0-9a-z][0-9a-z\!#\$&\-\^_\.\+]{0,126}$/i';

    /**
     * parameters.
     *
     * @var array
     */
    private $parameters;

    /**
     * sub type.
     *
     * @var string
     */
    private $subType;

    /**
     * suffix.
     *
     * @var null|string
     */
    private $suffix;

    /**
     * tree.
     *
     * @var string
     */
    private $tree;

    /**
     * type.
     *
     * @var string
     */
    private $type;

    /**
     * MediaType constructor.
     *
     * @param string $type type.
     * @param string $subType sub type.
     * @param array|Parameter[] $parameters parameters.
     *
     * @throws InvalidArgumentException argument is invalid.
     */
    public function __construct($type, $subType, array $parameters = [])
    {
        if (!self::isValidType($type)) {
            throw new InvalidArgumentException("Type is not valid ({$type}).");
        }

        if (!self::isValidSubType($subType)) {
            throw new InvalidArgumentException("Sub type is not valid ({$subType}).");
        }

        $suffix = null;
        $tree = null;

        if (strpos($subType, '+') !== false) {
            $subParts = explode('+', $subType, 2);
            $suffix = strtolower(trim($subParts[1]));

            if (!self::isValidSuffix($suffix)) {
                throw new InvalidArgumentException("Suffix is not valid ({$suffix}).");
            }
        }

        if (strpos($subType, '.') !== false) {
            $subParts = explode('.', $subType, 2);
            $tree = strtolower(trim($subParts[0]));

            if (!self::isValidTree($tree)) {
                throw new InvalidArgumentException("Tree is not valid ({$tree}).");
            }
        }

        $this->type = $type;
        $this->subType = $subType;
        $this->suffix = $suffix;
        $this->tree = $tree;
        $this->parameters = [];

        foreach ($parameters as $index => $parameter) {
            if (!($parameter instanceof Parameter)) {
                $name = $index;
                $parameter = new Parameter($name, $parameter);
            } else {
                $name = $parameter->getName();
            }

            if (isset($this->parameters[$name])) {
                throw new InvalidArgumentException("Duplicate parameter name ({$name}).");
            }

            $this->parameters[$name] = $parameter;
        }
    }

    /**
     * Get the media type from the file.
     *
     * @param string|SplFileInfo $file The path of the file.
     *
     * @return MediaType|null Initialized MediaType object.
     *
     * @throws InvalidArgumentException $file is invalid.
     * @throws InvalidMediaTypeException The media type is invalid.
     */
    public static function fromFile($file)
    {
        if ($file instanceof SplFileInfo) {
            $path = $file->getRealPath();
        } else {
            $path = (string)$file;
        }

        if (!file_exists($path)) {
            throw new InvalidMediaTypeException("File not found ({$path}).");
        }

        if (function_exists('mime_content_type')) {
            $type = mime_content_type($path);

            if (($type !== false) && ($type !== '')) {
                return self::fromMime($type);
            }
        }

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);

            if ($finfo) {
                $type = finfo_file($finfo, $path);

                finfo_close($finfo);

                if (($type !== false) && ($type !== '')) {
                    return self::fromMime($type);
                }
            }
        }

        return null;
    }

    /**
     * Initialize from mime type.
     *
     * @param string $mimeType mime type.
     *
     * @return MediaType Initialized MediaType object.
     *
     * @throws InvalidArgumentException mime type is invalid.
     * @throws InvalidMediaTypeException The media type is invalid.
     */
    public static function fromMime($mimeType)
    {
        if (empty($mimeType)) {
            throw new InvalidArgumentException('$mimeType must not be empty.');
        }

        // divide the whole by slashes.
        $parts = explode('/', $mimeType, 2);

        // get the type.
        $type = strtolower(trim($parts[0]));

        if (empty($type)) {
            throw new InvalidMediaTypeException('$mimeType must not be empty.');
        }

        if (!self::isValidType($type)) {
            throw new InvalidMediaTypeException("Type is not valid ({$type}).");
        }

        if (empty($parts[1])) {
            throw new InvalidMediaTypeException('There is no subtype.');
        }

        // parse subtype and later.
        $subType = null;
        $parameters = [];

        $parts = explode(';', $parts[1]);

        $subType = strtolower(trim($parts[0]));

        if (!self::isValidSubType($subType)) {
            throw new InvalidMediaTypeException("Sub type is not valid ({$subType}).");
        }

        for ($i = 1; $i < count($parts); $i++) {
            $paramParts = explode('=', $parts[$i], 2);

            $name = trim($paramParts[0]);

            if (!empty($paramParts[1])) {
                $value = trim($paramParts[1]);

                if ((substr($value, 0, 1) === '"') && (substr($value, -1, 1) === '"')) {
                    $value = substr($value, 1, -1);
                }
            } else {
                $value = null;
            }

            $parameters[] = new Parameter($name, $value);
        }

        return new self($type, $subType, $parameters);
    }

    /**
     * Get whether the sub type is valid.
     *
     * @param string $subType sub type.
     *
     * @return bool True if valid, false if invalid.
     */
    public static function isValidSubType($subType)
    {
        return preg_match(self::$validTypeAndSubTypePattern, $subType) === 1;
    }

    /**
     * Get whether the suffix is valid.
     *
     * @param string $suffix suffix.
     *
     * @return bool True if valid, false if invalid.
     */
    public static function isValidSuffix($suffix)
    {
        return in_array($suffix, self::$validSuffix, true);
    }

    /**
     * Get whether the tree is valid.
     *
     * @param string $tree tree.
     *
     * @return bool True if valid, false if invalid.
     */
    public static function isValidTree($tree)
    {
        return in_array($tree, self::$validTree, true);
    }

    /**
     * Get whether the type is valid.
     *
     * @param string $type type.
     *
     * @return bool True if valid, false if invalid.
     */
    public static function isValidType($type)
    {
        return preg_match(self::$validTypeAndSubTypePattern, $type) && in_array($type, self::$validType, true);
    }

    public function __toString()
    {
        $s = "{$this->type}/{$this->subType}";

        if (!empty($this->parameters)) {
            $s = $s . '; ' . join('; ', array_map(function ($e) {
                    return (string)$e;
                }, $this->parameters));
        }

        return $s;
    }

    /**
     * Acquires the specified parameter.
     *
     * @param string $name parameter name.
     *
     * @return Parameter|null parameter.
     */
    public function getParameter($name)
    {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        } else {
            return null;
        }
    }

    /**
     * Gets the parameters.
     *
     * @return array parameters.
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Gets the sub type.
     *
     * @return string sub type.
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * Gets the suffix.
     *
     * @return string suffix.
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Gets the tree.
     *
     * @return string tree.
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * Gets the type.
     *
     * @return string type.
     */
    public function getType()
    {
        return $this->type;
    }

    public function isExperimental()
    {
        return substr($this->type, 0, 2) === 'x-' || substr($this->subType, 0, 2) === 'x-';
    }

    public function isUnregistered()
    {
        if ($this->type === 'application' && $this->subType === 'x-www-form-urlencoded') {
            return false;
        } else {
            return ($this->tree === 'x');
        }
    }

    public function isVendor()
    {
        return $this->tree === 'vnd';
    }

}
