<?php
    
    // Класс для создания документов MS Word
    class WordDocument extends OfficeDocument{
        public function __construct( $filename, $template_path = '/template/' ){
          parent::__construct( $filename, $template_path );
          // Описываем связи для Word
          $this->word_rels = array(
            "rId1" => array(
              "http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles",
              "styles.xml"
            ),
            "rId2" => array(
              "http://schemas.microsoft.com/office/2007/relationships/stylesWithEffects",
              "stylesWithEffects.xml",
            ),
            "rId3" => array(
              "http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings",
              "settings.xml",
            ),
            "rId4" => array(
              "http://schemas.openxmlformats.org/officeDocument/2006/relationships/webSettings",
              "webSettings.xml",
            ),
            "rId6" => array(
              "http://schemas.openxmlformats.org/officeDocument/2006/relationships/fontTable",
              "fontTable.xml",
            ),
            "rId7" => array(
              "http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme",
              "theme/theme1.xml",
            ),
          );
        }
        public function assign( $content = '', $return = false ){
          // Проверяем, является ли $text файлом. Если да, то подключаем изображение
          if( is_file( $content ) ){
            // Берем шаблон абзаца
            $block = file_get_contents( $this->path . 'image.xml' );
            list( $width, $height ) = getimagesize( $content );
            $rid = "rId" . count( $this->word_rels ) . 'i';
            $this->word_rels[$rid] = array(
              "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image",
              "media/" . $content,
              // Указываем непосредственно путь к файлу
              $content
            );
            $xml = $this->pparse( array(
              '{WIDTH}' => $width * $this->px_emu,
              '{HEIGHT}' => $height * $this->px_emu,
              '{RID}' => $rid,
            ), $block );
          }
          else{
            // Берем шаблон абзаца
            $block = file_get_contents( $this->path . 'p.xml' );
            $xml = $this->pparse( array(
              '{TEXT}' => $content,
            ), $block );
          }
          // Если нам указали, что нужно возвратить XML, возвращаем
          if( $return )
            return $xml;
          else
            $this->content .= $xml;
        }
        // Упаковываем архив
        public function create(){
          $this->rels['rId1'] = array(
            'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument', 'word/document.xml' );
          // Добавляем связанные документы MS Office
          $this->add_rels( "_rels/.rels", $this->rels );
          // Добавляем связанные документы MS Office Word
          $this->add_rels( "_rels/document.xml.rels", $this->word_rels, 'word/' );
          // Добавляем содержимое
          $this->addFromString("word/document.xml", str_replace( '{CONTENT}', $this->content, file_get_contents( $this->path . "word/document.xml" ) ) );
          $this->close();
        }
    }
?>
