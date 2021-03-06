# cosmicvelocity/media-types
A PHP library for handling media types.

- Supports the RFC 6838 specification.
- MIME type character string analysis can be performed.
- Supports automatic detection from filename with built-in mapping list.
- Supports automatic detection by Fileinfo extension.

## Installation
If composer is used, it can be introduced by adding the following description.

      "require": {
        "cosmicvelocity/media-types": ">=1.0"
      }

## How to use
- When detecting from file name.

      $mediaTypes = new PhpArrayMediaTypes();
      $mediaType = $mediaTypes->getMediaType('sample.txt');
      
      $mediaType->getType(); // text

- To detect from your own mapping.

      $mediaTypes = new PhpArrayMediaTypes([
         'hoge' => 'application/prs.hoge+xml'
      ]);
      $mediaType = $mediaTypes->getMediaType('sample.hoge');

      $mediaType->getType();    // application
      $mediaType->getSubType(); // prs.hoge+xml
      $mediaType->getTree();  // prs
      $mediaType->getSuffix();  // xml

- When analyzing MIME type.

      $mediaType = MediaType::fromMime('application/calendar+json; charset=utf-8');

      $mediaType->getType();    // application
      $mediaType->getSubType(); // calendar+json 
      $mediaType->getSuffix();  // json 
      $mediaType->getParameter('charset')->getValue(); // utf-8

- When detecting from a file.

      $mediaType = MediaType::fromFile('sample.json');
      
      $mediaType->getType();    // text
      $mediaType->getSubType(); // plain
