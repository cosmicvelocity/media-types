# MediaTypes
メディアタイプを処理するためのPHPライブラリです。

## 特徴
- RFC6838 仕様をサポートします。
- MIMEタイプ文字列の解析を行えます。
- 組み込みマッピングリストによるファイル名からの自動検出をサポートします。
- Fileinfo拡張による自動検出をサポートします。

## インストール
composer を使っている場合は、下記のような記述を追加する事で導入できます。

      "repositories": [
        {
          "type": "vcs",
          "url": "https://github.com/cosmicvelocity/MediaTypes.git"
        }
      ],
      "require": {
        "cosmicvelocity/media-types": ">=1.0"
      }

## 使い方
- ファイル名から検出する場合。

      $mediaTypes = new PhpArrayMediaTypes();
      $mediaType = $mediaTypes->getMediaType('sample.txt');
      
      $mediaType->getType(); // text

- 独自のマッピングから検出する場合。

      $mediaTypes = new PhpArrayMediaTypes([
         'hoge' => 'application/prs.hoge+xml'
      ]);
      $mediaType = $mediaTypes->getMediaType('sample.hoge');

      $mediaType->getType();    // application
      $mediaType->getSubType(); // prs.hoge+xml
      $mediaType->getTree();  // prs
      $mediaType->getSuffix();  // xml

- MIMEタイプを解析する場合。

      $mediaType = MediaType::fromMime('application/calendar+json; charset=utf-8');

      $mediaType->getType();    // application
      $mediaType->getSubType(); // calendar+json 
      $mediaType->getSuffix();  // json 
      $mediaType->getParameter('charset')->getValue(); // utf-8

- ファイルから検出する場合。

      $mediaType = MediaType::fromFile('sample.json');
      
      $mediaType->getType();    // text
      $mediaType->getSubType(); // plain
