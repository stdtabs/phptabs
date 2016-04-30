PhpTabs
=======

PhpTabs is a PHP library that reads tablature files. It provides direct methods to read a song name, get a list of instruments or whatever be your needs.

PhpTabs is currently compatible with Guitar Pro 3 format. 

[![Build Status](https://travis-ci.org/stdtabs/phptabs.svg?branch=master)](https://travis-ci.org/stdtabs/phptabs)


Table of contents
=================

- [Requirements](#requirements)
- [Installation](#installation)
  - [From Github](#from-github)
  - [With Composer](#with-composer)
- [Basic Usage](#basic-usage)
- [Options](#options)
  - [type](#type)
  - [debug](#debug)
  - [verbose](#verbose)
- [Methods](#methods)
  - [Error handling](#error-handling)
    - [hasError()](#haserror)
    - [getError()](#geterror)
  - [Accessing metadata](#accessing-metadata)
    - [getName()](#getname)
    - [getArtist()](#getartist)
    - [getAlbum()](#getalbum)
    - [getAuthor()](#getauthor)
    - [getCopyright()](#getcopyright)
    - [getWriter()](#getwriter)
    - [getComments()](#getcomments)
    - [getTranscriber()](#gettranscriber)
    - [getDate()](#getdate)
  - [Accessing tracks](#accessing-tracks)
    - [countTracks()](#counttracks)
    - [getTracks()](#gettracks)
    - [getTrack()](#gettrack)
  - [Accessing channels](#accessing-channels)
    - [countChannels()](#countchannels)
    - [getChannels()](#getchannels)
    - [getChannel()](#getchannel)
  - [Accessing measure headers](#accessing-measure-headers)
    - [countMeasureHeaders()](#countmeasureheaders)
    - [getMeasureHeaders()](#getmeasureheaders)
    - [getMeasureHeader()](#getmeasureheaderindex)

________________________________________________________________________

Requirements
------------

PHP >= 5.4.0
________________________________________________________________________

Installation
------------

*Installation with Composer is required if you want to run PHPUnit tests.*

###From Github

Download and extract archive from [https://github.com/stdtabs/phptabs/archive/master.zip](https://github.com/stdtabs/phptabs/archive/master.zip)

Then add this PHP line before usage:

```php
require_once "PhpTabs.php";
```


###With Composer

```sh
composer require stdtabs/phptabs:0.*
```

If Composer is not installed, you can download it from [https://getcomposer.org/composer.phar](https://getcomposer.org/composer.phar)

Then install the package directly with the archive:

```sh
php composer.phar require stdtabs/phptabs:0.*
```

[More informations about Composer](https://getcomposer.org/)
________________________________________________________________________

Basic Usage
-----------

```php
require_once 'vendor/autoload.php';

// Instanciates a tablature
$tablature = new PhpTabs("mytabs.gp3");

// Reads and prints information
echo $tablature->getName();
```

________________________________________________________________________

Options
-------

You can get or set options before instanciating PhpTabs object with the Config component.

```php
use PhpTabs\Component\Config;

// Setting option value
Config::set('<option name>', '<option value>');

// Getting option value
Config::get('<option name>');
```
________________________________________________________________________
###type
Useful to set one specific type of analyse

__Values__

- __meta__

  Only meta informations are read from the tablature file.
  
  Analyse is very fast.

- __channels__

  Only meta informations and channels are read from the tablature file.
  
  Analyse is fast.
 
- __default__ or  anything else
  
  The file is fully analyzed. 

__Example__

```php
// Setting type to meta will accelerate analyses
Config::set("type", "meta");
```
________________________________________________________________________
###debug
Useful to manage Exceptions

__Values__

- __true__

  The application stops.

  Exception message and stack trace are printed.
  
- __default__ or  anything else

  Nothing is printed and the application continues.

__Example__

```php
// Setting debug to true makes Exceptions as blocking events
Config::set("debug", true);

```

________________________________________________________________________
###verbose
Useful to print all logged events such as stream reads, internal notices
and warnings.

__Values__

- __true__

  The application prints all events.
 
- __default__ or  anything else
  
  Nothing is printed.

__Example__

```php
// Setting verbose to true 
Config::set("debug", true);
```




________________________________________________________________________

Methods
-------
________________________________________________________________________
###Error handling
________________________________________________________________________
####hasError()

__Type__ *boolean*

It returns true if an error has been set, otherwise false.

```php
$tablature->hasError();
```
________________________________________________________________________
####getError()

__Type__ *string*

It returns the last error message or an empty string if no error has been set.

```php
$tablature->getError();
```
________________________________________________________________________


###Accessing metadata
________________________________________________________________________
####getName()

__Type__ *string*

The name of the song.

__Example__

```php
$tablature->getName();
```
________________________________________________________________________
####getArtist()

__Type__ *string*

The interpreter of the song.

__Example__

```php
$tablature->getArtist();
```
________________________________________________________________________
####getAlbum()

__Type__ *string*

The name of the album.

__Example__

```php
$tablature->getAlbum();
```
________________________________________________________________________
####getAuthor()

__Type__ *string*

The author of the song.

__Example__

```php
$tablature->getAuthor();
```
________________________________________________________________________
####getCopyright()

__Type__ *string*

The copyright of the song.

__Example__

```php
$tablature->getCopyright();
```
________________________________________________________________________
####getWriter()

__Type__ *string*

The songwriter.

__Example__

```php
$tablature->getWriter();
```
________________________________________________________________________
####getComments()

__Type__ *string*

The tablature comments. They are compounded of several lines 
separated by a line break (```PHP_EOL```).

__Example__

```php
$tablature->getComments();
```
________________________________________________________________________
####getTranscriber()

__Type__ *string*


Person who has transcribed tablature

__Support__ 

Guitar Pro >= 4

__Example__

```php
$tablature->getTranscriber();
```
________________________________________________________________________
####getDate()

__Type__ *string*

Date when tablature or song has been written

__Support__ 

Guitar Pro >= 4

__Example__

```php
$tablature->getDate();
```
________________________________________________________________________

###Accessing tracks
________________________________________________________________________

####countTracks()

__Type__ *integer*

The number of tracks 

__Example__

```php
$tablature->countTracks();
```
________________________________________________________________________
####getTracks()

__Type__ *array*

An array of Track objects

There is one track object for each instrument of the song.

__Example__

```php
$tablature->getTracks();
```
________________________________________________________________________
####getTrack($index)

__Type__ *object*

__Parameter__ *integer* $index 

The music sheet for one instrument.

__Example__

```php
// Get the first track
$tablature->getTrack(0);
```
________________________________________________________________________

###Accessing channels
________________________________________________________________________

####countChannels()

__Type__ *integer*

The number of channels 

__Example__

```php
$tablature->countChannels();
```
________________________________________________________________________
####getChannels()

__Type__ *array*

An array of Channel objects

There is one channel object for each track of the song.

__Example__

```php
$tablature->getChannels();
```
________________________________________________________________________
####getChannel($index)

__Type__ *object*

__Parameter__ *integer* $index 

The instrument and sound parameters for one track.

__Example__

```php
// Get the first channel
$tablature->getChannel(0);
```
________________________________________________________________________

###Accessing measure headers
________________________________________________________________________

####countMeasureHeaders()

__Type__ *integer*

The number of measure headers 

__Example__

```php
$tablature->countMeasureHeaders();
```
________________________________________________________________________
####getMeasureHeaders()

__Type__ *array*

An array of MeasureHeader objects

__Example__

```php
$tablature->getMeasureHeaders();
```
________________________________________________________________________
####getMeasureHeader($index)

__Type__ *object*

__Parameter__ *integer* $index 

Measure header contains global informations about the measure.

__Example__

```php
// Get the first measure header
$tablature->getMeasureHeader(0);
```
________________________________________________________________________

