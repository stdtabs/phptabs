<?php

namespace PhpTabs\Component;

use Exception;

use PhpTabs\Component\Tablature;

class Writer
{
  /** @var string $path */
  private $path;

  /** @var Tablature */
  private $tablature;

  /** @var array List of supported writers */
  private $writers = array(
    'gp3' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro3Writer',
    'gp4' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro4Writer',
    'gp5' => 'PhpTabs\\Writer\\GuitarPro\\GuitarPro5Writer'
  );

  /**
   * @param Tablature $tablature
   */
  public function __construct(Tablature $tablature)
  {
    $this->tablature = $tablature;
  }

  /**
   * Builds content in $format
   * 
   * @param string $format
   * @return string A binary chain
   */
  public function build($format)
  {
    if(!isset($this->writers[$format]))
    {
      $message = sprintf('Output format %s is not supported', $format);

      throw new Exception($message);
    }

    return (new $this->writers[$format]($this->tablature->getSong()))->getContent();
  }

  /**
   * @param string $filename
   */
  public function save($path = null)
  {
    if($path == 'php://output')
    {
      print($this->build($this->tablature->getFormat()));
      
      return true;
    }
    else if(null === $path)
    {
      return $this->build($this->tablature->getFormat());
    }
    else if(is_string($path))
    {
      $parts = pathinfo($path);

      if(!isset($parts['basename'], $parts['extension']))
      {
        $message = sprintf('Destination path %s is not complete', $path);

        throw new Exception($message);
      }

      $this->path = $path;

      return $this->record($this->build($parts['extension']));
    }

    throw new Exception('Save path is not allowed');
  }

  private function record($content)
  {
    $dir = pathinfo($this->path, PATHINFO_DIRNAME);

    if(!is_dir($dir) || !is_writable($dir))
    {
      throw new Exception('Save directory error');
    }
    else if(is_file($this->path) && !is_writable($this->path))
    {
      $message = sprintf('File "%s" still exists and is not writable', $this->path);

      throw new Exception($message);
    }

    file_put_contents($this->path, $content);
  }
}
