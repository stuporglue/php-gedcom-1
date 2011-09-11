<?php
/**
 *
 */

namespace Gedcom\Parser\Indi;

/**
 *
 *
 */
abstract class Lds extends \Gedcom\Parser\Component
{

    /**
     *
     *
     */
    public static function &parse(\Gedcom\Parser &$parser)
    {
        $record = $parser->getCurrentLineRecord();
        $depth = (int)$record[0];
        
        $className = '\\Gedcom\\Record\\Indi\\' . ucfirst(strtolower(trim($record[1])));
        $lds = new $className();
        
        $parser->forward();
        
        while(!$parser->eof())
        {
            $record = $parser->getCurrentLineRecord();
            $recordType = strtoupper(trim($record[1]));
            $currentDepth = (int)$record[0];
            
            if($currentDepth <= $depth)
            {
                $parser->back();
                break;
            }
            
            switch($recordType)
            {
                case 'STAT':
                    $lds->stat = trim($record[2]);
                break;
                
                case 'DATE':
                    $lds->date = trim($record[2]);
                break;
                
                case 'PLAC':
                    $lds->plac = trim($record[2]);
                break;
                
                case 'TEMP':
                    $lds->temp = trim($record[2]);
                break;
                
                case 'SOUR':
                    $sour = \Gedcom\Parser\SourRef::parse($parser);
                    $lds->addSour($sour);
                break;
                
                case 'NOTE':
                    $note = \Gedcom\Parser\NoteRef::parse($parser);
                    $lds->addNote($note);
                break;
                
                default:
                    $self = get_called_class();
                    $method = 'parse' . $recordType;
                    
                    if(method_exists($self, $method))
                        $self::$method($parser, $lds);
                    else
                        $parser->logUnhandledRecord($self . ' @ ' . __LINE__);
            }
            
            $parser->forward();
        }
        
        return $lds;
    }
}
