<?php

namespace PhpTabs\Component;

use Exception;

use PhpTabs\Component\Tablature;
use PhpTabs\Component\Dumper\DumperBase;
use PhpTabs\Component\Serializer\Text;
use PhpTabs\Component\Serializer\Xml;

class Dumper extends DumperBase
{
  protected $song;

  public function __construct(Tablature $tablature)
  {
    $this->song = $tablature->getSong();
  }

  /**
   * Dumps a song into an array
   *  and returns a representation
   * 
   * @param string $format array|xml|json|var_export|serialize|text
   *
   * @return array
   * 
   * @throws Exception if format is not supported
   */
  public function dump($format = 'array')
  {
    switch($format)
    {
      case 'array':
        return $this->dumpSong();
      case 'xml':
        return (new Xml())->serialize($this->dump('array'));
      case 'json':
        return json_encode($this->dump('array'));
      case 'var_export':
        return var_export($this->dump('array'), true);
      case 'serialize':
        return serialize($this->dump('array'));
      case 'text':
        return (new Text())->serialize($this->dump('array'));
      default:
        $message = sprintf('%s does not support "%s" format', __METHOD__, $format);
        throw new Exception($message);
        break;
    }
  }
}
