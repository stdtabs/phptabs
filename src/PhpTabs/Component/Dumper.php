<?php

namespace PhpTabs\Component;

use Exception;
use PhpTabs\Component\Tablature;
use PhpTabs\Component\Dumper\DumperBase;
use PhpTabs\Component\Serializer\Text;
use PhpTabs\Component\Serializer\Xml;

class Dumper extends DumperBase
{
  /** @var \Phptabs\Model\Song */
  protected $song;

  /**
   * @param Tablature The tablature to dump
   */
  public function __construct(Tablature $tablature)
  {
    $this->song = $tablature->getSong();
  }

  /**
   * Returns a representation of the song into a desired format
   * 
   * @param string $format array|xml|json|var_export|serialize|text
   *
   * @return mixed array|xml|json|var_export|serialize|text
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
        return (new Xml())->serialize($this->dump());
      case 'json':
        return json_encode($this->dump());
      case 'var_export':
        return var_export($this->dump(), true);
      case 'serialize':
        return serialize($this->dump());
      case 'text':
        return (new Text())->serialize($this->dump());
      default:
        $message = sprintf('%s does not support "%s" format', __METHOD__, $format);
        throw new Exception($message);
        break;
    }
  }
}
