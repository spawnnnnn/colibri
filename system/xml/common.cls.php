<?php
    
    // need a system::collection namespace
    Core::Using("System::Collections");

    class XMLNode {
        
        private $_document;
        private $_node;
        
        public function __construct(DOMNode $node, DOMDocument $dom = null) {
            $this->_node = $node;
            $this->_document = $dom;
        }
        
        public static function Load($xmlFile, $isFile = true) {
            $dom = new DOMDocument(); 
            if(!$isFile)
                $dom->loadXML($xmlFile);
            else {
                if(file_exists($xmlFile))
                    $dom->load($xmlFile);
                else
                    throw new BaseException('File '.$xmlFile.' does not exists');
            }
            
            return new XMLNode($dom->documentElement, $dom);
        }
        
        public static function LoadNode($xmlString, $encoding = 'utf-8') {
            $dom = new DOMDocument('1.0', $encoding);
            $dom->loadXML('<'.'?xml version="1.0" encoding="'.$encoding.'"?'.'>'.$xmlString);
            return new XMLNode($dom->documentElement, $dom);
        }        
        
        public static function LoadHTML($htmlFile, $isFile = true, $encoding = 'utf-8') {
            libxml_use_internal_errors(true);
            
            $dom = new DOMDocument('1.0', $encoding);
            if(!$isFile)
                $dom->loadHTML($htmlFile);
            else {
                if(file_exists($htmlFile))
                    $dom->loadHTMLFile($htmlFile);
                else
                    throw new BaseException('File '.$htmlFile.' does not exists');
            }
            
            return new XMLNode($dom->documentElement, $dom);
        }
        
        public function Save($filename = "") {
            if(!Variable::IsEmpty($filename))
                $this->_document->save($filename);
            else
                return $this->_document->saveXML(null, LIBXML_NOEMPTYTAG);
        }
        
        public function SaveHTML($filename = "") {
            if(!Variable::IsEmpty($filename))
                $this->_document->saveHTMLFile($filename);
            else
                return $this->_document->saveHTML();
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'type':
                    return $this->_node->nodeType;
                case 'value':
                    return $this->_node->nodeValue;
                case 'name':
                    return $this->_node->nodeName;
                case 'data':
                    return $this->_node->data;
                case 'encoding':
                    return $this->_document->encoding ? $this->_document->encoding : 'utf-8';
                case 'attributes':
                    if(!is_null($this->_node->attributes))
                        return new XMLNodeAttributeList($this->_document, $this->_node, $this->_node->attributes);
                    else
                        return null;
                case 'root':
                    return $this->_document ? new XMLNode($this->_document->documentElement, $this->_document) : null;
                case 'parent':
                    return $this->_node->parentNode ? new XMLNode($this->_node->parentNode, $this->_document) : null;
                case 'nodes':
                    if($this->_node->childNodes)
                        return new XMLNodeList($this->_node->childNodes, $this->_document);
                    else
                        return null;
                case 'firstchild':                                                
                    return $this->_node->firstChild ? new XMLNode($this->_node->firstChild, $this->_document) : null;
                case 'elements':                                                
                    return $this->Query('./child::*', true);
                case 'children':                                                
                    return $this->Query('./child::*');
                case 'texts':                                                
                    return $this->Query('./child::text()');
                case 'document':
                    return $this->_document;
                case 'raw':
                    return $this->_node;
                case 'xml':
                    return $this->_document->saveXML($this->_node, LIBXML_NOEMPTYTAG);
                case 'html':
                    return $this->_document->saveHTML($this->_node);
                case 'next':
                    return $this->_node->nextSibling ? new XMLNode($this->_node->nextSibling, $this->_document) : null;
                case 'prev':
                    return $this->_node->previousSibling ? new XMLNode($this->_node->previousSibling, $this->_document) : null;
                default:
                    $item = $this->Item($property);
                    if(is_null($item)) {
                        $items = $this->getElementsByName($property);
                        if($items->count > 0)
                            $item = $items->first;
                        else {
                            if($this->type == 1)
                                $item = $this->attributes->$property;
                        }
                    }
                    return $item;
            }
            return null;
        }
        
        public function __set($property, $value) {
            switch(strtolower($property)) {
                case 'value': {
                    $this->_node->nodeValue = $value;
                    break;
                }
                case 'cdata': {
                    $this->_node->appendChild($this->_document->createCDATASection($value));
                    break;
                }
            }
        }
        
        public function Item($name) {
            $list = $this->Items($name);
            if($list->count > 0)
                return $list->first;
            else
                return null;
        }
        
        public function Items($name) {
            return $this->Query('./child::'.$name);
        }
        
        public function IsChildOf(XMLNode $node) {
            $p = $this;
            while($p->parent) {
                if($p->raw === $node->raw)
                    return true;
                $p = $p->parent;
            }
            return false;
        }
        
        public function Append($nodes) {
            if($nodes instanceof XMLNode) {
                if($nodes->name == 'html') {
                    $nodes = $nodes->body;
                    foreach($nodes->children as $node) {
                        $this->_node->appendChild($this->_document->importNode($node->raw, true));
                    }
                }
                else
                    $this->_node->appendChild($this->_document->importNode($nodes->raw, true));
            }
            else if($nodes instanceof XMLNodeList) {
                foreach($nodes as $node) {
                    
                    if($node->name == 'html') {
                        $node = $node->body;
                        foreach($node->children as $n) {
                            $this->_node->appendChild($this->_document->importNode($n->raw, true));
                        }
                    }
                    else
                        $this->_node->appendChild($this->_document->importNode($node->raw, true));
                }
            }
        }
        
        public function Insert($nodes, XMLNode $relation) {
            if($nodes instanceof XMLNode) {
                $this->_node->insertBefore($this->_document->importNode($nodes->raw, true), $relation->raw);
            }
            else if($nodes instanceof XMLNodeList) {
                foreach($nodes as $node) {
                    $this->_node->insertBefore($this->_document->importNode($node->raw, true), $relation->raw);
                }
            }
        }
        
        public function Remove() {
            $this->_node->parentNode->removeChild($this->_node);
        }
        
        public function ReplaceTo($node) {
            $_node = $node->raw;
            $_node = $this->_document->importNode($_node, true);
            $this->_node->parentNode->replaceChild($_node, $this->_node);
            $this->_node = $_node;
        }
        
        public function getElementsByName($name) {
            return $this->Query('./child::*[@name="'.$name.'"]', true);
        }

        public function Query($query, $returnAsNamedMap = false) { // , $ns = '', $nsUri = ''
            $xq = new XMLQuery($this, $returnAsNamedMap);
            return $xq->query($query);
        }  
        
    }
    
    class XMLNodeListIterator implements Iterator {
        
        private $_class;
        private $_current = 0;
        
        public function __construct($class = null) {
            $this->_class = $class;
        }
        
        public function rewind() {
            $this->_current = 0;
            return $this->_current;
        }
        
        public function current() {
            if($this->valid())
                return $this->_class->Item($this->_current);
            else    
                return false;
        }
        
        public function key() {
            return $this->_current;
        }
        
        public function next() {
            $this->_current++;
            if($this->valid())
                return $this->_class->Item($this->_current);
            else    
                return false;
        }
        
        public function valid() {
            return $this->_current >= 0 && $this->_current < $this->_class->count;
        }

    } 
    
    class XMLAttribute {
        
        private $_data;
        
        public function __construct(DOMNode $data) {
            $this->_data = $data;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'value':
                    return $this->_data->nodeValue;
                case 'name':
                    return $this->_data->nodeName;
                case 'type':
                    return $this->_data->nodeType;
            }
            return null;
        }
        
        public function __set($property, $value) { 
            switch(strtolower($property)) {
                case 'value':
                    $this->_data->nodeValue = $value;
            }
        }
        
        public function Remove() {
            $this->_data->parentNode->removeAttributeNode($this->_data);
        }
        
    }
    
    class XMLNodeAttributeList implements IteratorAggregate {
        
        private $_document;
        private $_node;
        private $_data;
        private $_count;
        
        public function __construct(DOMDocument $document, DOMNode $node, DOMNamedNodeMap $xmlattributes) {
            $this->_document = $document;
            $this->_node = $node;
            $this->_data = $xmlattributes;
            
            $this->_count = 0;
            foreach($xmlattributes as $xa) $this->_count++;
            
        }  

        public function getIterator() {
            return new XMLNodeListIterator($this);
        }
        
        public function Item($index) {
            return $this->_data->item($index);
        } 
        
        public function __get($property) {
            
            switch(strtolower($property)) {
                case 'count':
                    return $this->_count;
                default: 
                    $attr = $this->_data->getNamedItem($property);
                    if(!is_null($attr))
                        return new XMLAttribute($this->_data->getNamedItem($property));
                    return null;
            }
        }
        
        public function Append($name, $value) {
            $attr = $this->_document->createAttribute(strtolower($name));
            $attr->value = $value;
            $this->_node->appendChild($attr);
        }
        
    }
    
    class XMLNodeList implements IteratorAggregate {
        
        private $_data;
        private $_document;
        
        public function __construct(DOMNodeList $nodelist, DOMDocument $dom) {
            $this->_data = $nodelist;
            $this->_document = $dom;
        }
        
        public function getIterator() {
            return new XMLNodeListIterator($this);
        }
        
        public function Item($index) {
            if($this->_data->item($index))
                return new XMLNode($this->_data->item($index), $this->_document);
            return null;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'last':
                    return $this->Item($this->count-1);
                case 'first':
                    return $this->Item(0);
                case 'document':
                    return $this->_document;
                case 'count':
                    return $this->_data->length;
            }
            return null;
        }
        
        public function Remove() {
            foreach($this as $d) {
                $d->Remove();
            }
        }
        
    }

    class XMLNamedNodeList extends ObjectCollection {
        
        private $_data;
        private $_document;
        
        public function __construct(DOMNodeList $nodelist, DOMDocument $dom) {
            $this->_document = $dom;
            
            $data = array();
            foreach($nodelist as $node) {
                $data[$node->nodeName] = $node;
            }

            parent::__construct($data);
        }
        
        public function Item($key) {
            $v = parent::Item($key);
            if(Variable::IsNull($v)) return null;
            return new XMLNode($v, $this->_document);
        }
        
        public function ItemAt($index) {
            return new XMLNode(parent::ItemAt($index), $this->_document);
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'document':
                    return $this->_document;
                default:
                    return parent::__get($property);
            }
        }
        
    }

    class XMLQuery {
        
        private $_contextNode;
        private $_operator;
        private $_returnAsNamedMap;
        
        public function __construct(XMLNode $node, $returnAsNamedMap = false) {
            $this->_returnAsNamedMap = $returnAsNamedMap;
            $this->_contextNode = $node;
            $this->_operator = new DOMXPath($this->_contextNode->document);
        }
        
        public function query($xpathQuery) {
            $res = $this->_operator->query($xpathQuery, $this->_contextNode->raw);
            if(!$res)
                return new XMLNamedNodeList(new DOMNodeList(), $this->_contextNode->document);
            if($this->_returnAsNamedMap)
                return new XMLNamedNodeList($res, $this->_contextNode->document);
            return new XMLNodeList($res, $this->_contextNode->document);
        }
        
        
        
    }
    

?>
