<?php

    /**************************************
    /
    /  *** PHP Library PHPDocx ***
    /  Version: 0.9.2
    /  Author: Alexey Kichaev
    /  Home page: http://webli.ru/phpdocx/
    /  License: MIT or GPL
    /  GITHub: https://github.com/alkich/PHPDocx
    /  Date: 14/03/2012
    /
    /***************************************/
    // Общий класс для создания генераторов MS Office документов
    class OfficeDocument extends ZipArchive{
        // Путь к шаблону
        protected $path;
        // Содержимое документа
        protected $content;
        // Множитель для перевода размеров изображений из пикселей в EMU
        protected $px_emu = 8625;
        // Делаем приватно, чтобы не было возможности вшить дрянь в документ
        protected $rels = array();
        public function __construct($filename, $template_path = '/template/' ){
          // Путь к шаблону
          $this->path = dirname(__FILE__) . $template_path;
          // Если не получилось открыть файл, то жизнь бессмысленна.
          if ( $this->open( $filename, ZIPARCHIVE::CREATE) !== TRUE) {
            die("Unable to open <$filename>\n");
          }
          // Описываем связи для документа MS Office
          $this->rels = array_merge( $this->rels, array(
            'rId3' => array(
              'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties',
              'docProps/app.xml' ),
            'rId2' => array(
              'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties',
              'docProps/core.xml' ),
          ) );
          // Добавляем типы данных
          $this->addFile($this->path . "[Content_Types].xml" , "[Content_Types].xml" );
        }
        // Генерация зависимостей
        protected function add_rels( $filename, $rels, $path = '' ){
          // Шапка XML
          $xmlstring = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
          // Добавляем документы по описанным связям
          foreach( $rels as $rId => $params ){
            // Если указан путь к файлу, берем. Если нет, то берем из репозитория
            $pathfile = empty( $params[2] ) ? $this->path . $path . $params[1] : $params[2];
            // Добавляем документ в архив
            if( $this->addFile( $pathfile ,  $path . $params[1] ) === false )
              die('Не удалось добавить в архив ' . $path . $params[1] );
            // Прописываем в связях
            $xmlstring .= '<Relationship Id="' . $rId . '" Type="' . $params[0] . '" Target="' . $params[1] . '"/>';
          }
          $xmlstring .= '</Relationships>';
          // Добавляем в архив
          $this->addFromString( $path . $filename, $xmlstring );
        }
        protected function pparse( $replace, $content ){
          return str_replace( array_keys( $replace ), array_values( $replace ), $content );
        }
    }

?>