<?php
/**
 * Erfurt_Sparql Query2 - RDFLiteral.
 * 
 * @package    ontowiki
 * @subpackage query2
 * @author     Jonas Brekle <jonas.brekle@gmail.com>
 * @copyright  Copyright (c) 2008, {@link http://aksw.org AKSW}
 * @license    http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * @version    $Id$
 */ 
class Erfurt_Sparql_Query2_RDFLiteral implements Erfurt_Sparql_Query2_GraphTerm, Erfurt_Sparql_Query2_PrimaryExpression
{
    protected $value = '';
    protected $datatype;
    protected $lang;
    protected $mode = 0;
    
    /**
     * @param string $str the literal value
     * @param null|string|Erfurt_Sparql_Query2_IriRef $meta if is null: untyped literal with no language tag. if is IriRef: typed literal. if is string: when string is one of ('int','boolean','float','decimal','string','time','date') convert to IriRef with XMLSchema#%s. else use as language tag.
     */
    public function __construct($str, $meta = null){
        if(!is_string($str)){
            throw new RuntimeException('Argument 1 passed to Erfurt_Sparql_Query2_RDFLiteral::__construct must be a string, instance of '.typeHelper($str).' given');
        }
        $this->value = $str;

        if($meta != null){
            if(is_string($meta)){
                switch($meta){
                    case 'int':
                    case 'boolean':
                    case 'float':
                    case 'decimal':
                    case 'string':
                    case 'time':
                    case 'date':
                        //a shortcut for XMLSchema types
                        //is a typed literal
                        $xmls='http://www.w3.org/2001/XMLSchema#';
                        $this->datatype = new Erfurt_Sparql_Query2_IriRef($xmls.$meta);
                        $this->mode = 2;
                    break;
                    default:
                        // is literal with language tag
                        $this->lang = $meta;
                        $this->mode = 1;
                    break;
                }
            } else if ($meta instanceof Erfurt_Sparql_Query2_IriRef){
                //is a typed literal
                $this->datatype = $meta;
                $this->mode = 2;
            } else {
                throw new RuntimeException('Argument 2 passed to Erfurt_Sparql_Query2_RDFLiteral::__construct must be an instance of Erfurt_Sparql_Query2_IriRef or string, instance of '.typeHelper($meta).' given');
            }
        }
    }
       
    /**
     * getSparql
     * build a valid sparql representation of this obj - should be like 'abc' or 'abc'^^<http://www.w3.org/2001/XMLSchema#string> or 'abc'@de
     * @return string
     */
    public function getSparql(){
        $sparql = '"'.$this->value.'"';
        
        switch($this->mode){
            case 0:
            break;
            case 1:
            $sparql .= '@'.$this->lang;
            break;
            case 2:
            $sparql .= '^^'.$this->datatype->getSparql();
            break;
        }
        
        return $sparql;
    }
    
    public function __toString(){
        return $this->getSparql();
    }
    
    /**
     * setValue
     * @param string $val
     * @return Erfurt_Sparql_Query2_RDFLiteral $this
     */
    public function setValue($val){
        if(is_string($val)){
            $this->value = $val;
        } else {
            //throw
        }
        return $this;
    }
    
    /**
     * getValue
     * @return string the value of the literal without datatype or language tag
     */
    public function getValue(){
        return $this->value;
    }
    
    /**
     * setDatatype
     * @param Erfurt_Sparql_Query2_IriRef $type
     * @return Erfurt_Sparql_Query2_RDFLiteral $this
     */
    public function setDatatype(Erfurt_Sparql_Query2_IriRef $type){
        $this->datatype = $type;
        return $this;
    }
    
    /**
     * getDatatype
     * @return Erfurt_Sparql_Query2_IriRef the datatype
     */
    public function getDatatype(){
        return $this->datatype;
    }
    
    /**
     * getLanguageTag
     * @return string the language tag
     */
    public function getLanguageTag(){
        return $this->lang;
    }
    
    /**
     * setLanguageTag
     * @param string $lang the language tag
     * @return Erfurt_Sparql_Query2_RDFLiteral $this
     */
    public function setLanguageTag($lang){
        if(is_string($lang)){
            $this->lang = $lang;
        }
        return $this;
    }
}
?>
