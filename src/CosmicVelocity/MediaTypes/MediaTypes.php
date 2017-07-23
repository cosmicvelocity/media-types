<?php

namespace CosmicVelocity\MediaTypes;

/**
 * Interface MediaTypes
 *
 * @package CosmicVelocity\MediaTypes
 */
interface MediaTypes
{

    /**
     * Get MediaType from the file path.
     *
     * @param string $path path.
     *
     * @return MediaType MediaType object.
     *
     * @throws InvalidMediaTypeException An exception occurs while getting the media type.
     */
    public function getMediaType($path);

}
