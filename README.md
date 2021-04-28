PhpTabs
=======

[![Latest Stable Version](https://poser.pugx.org/stdtabs/phptabs/version.svg)](https://github.com/stdtabs/phptabs/releases)
[![Build Status](https://api.travis-ci.com/stdtabs/phptabs.svg?branch=master)](https://travis-ci.com/stdtabs/phptabs)
[![License](https://poser.pugx.org/stdtabs/phptabs/license.svg)](https://packagist.org/packages/stdtabs/phptabs)

[PhpTabs](https://phptabs.readthedocs.io/) is a PHP library for reading and writing scores and MIDI files.
It provides direct methods to read a song name, get a list of instruments or whatever be your needs.

PhpTabs currently supports the following file formats:

- Guitar Pro 3 (.gp3)
- Guitar Pro 4 (.gp4)
- Guitar Pro 5 (.gp5)
- MIDI (.mid, .midi)

Any questions?

- Read the [PhpTabs Manual](https://phptabs.readthedocs.io/)
- [Open an issue on github](https://github.com/stdtabs/phptabs/issues)
- [Contribute code](https://github.com/stdtabs/phptabs/pulls)
- Contribute documentation in the /docs

Table of contents
=================

__The documentation below contains only basic examples. If you want to 
see more examples and the complete API behind the library, read the 
[PhpTabs Manual](https://phptabs.readthedocs.io/).__

Before version 1.0.0, the old manual [PhpTabs Manual](https://stdtabs.github.io/)

- [Requirements](#requirements)
- [Installation](#installation)
  - [Composer](#composer)
  - [Alternative](#alternative)
- [Testing](#testing)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
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
    - [getTrack()](#gettrackindex)
  - [Accessing channels](#accessing-channels)
    - [countChannels()](#countchannels)
    - [getChannels()](#getchannels)
    - [getChannel()](#getchannelindex)
  - [Accessing instruments](#accessing-instruments)
    - [countInstruments()](#countinstruments)
    - [getInstruments()](#getinstruments)
    - [getInstrument()](#getinstrumentindex)
  - [Accessing measure headers](#accessing-measure-headers)
    - [countMeasureHeaders()](#countmeasureheaders)
    - [getMeasureHeaders()](#getmeasureheaders)
    - [getMeasureHeader()](#getmeasureheaderindex)
  - [Saving data](#saving-data)
    - [save()](#savefilename)
    - [export()](#exportformat)
    - [convert()](#converttype)

________________________________________________________________________

Requirements
------------

PhpTabs requires PHP 7.2+ and 8.0.

Until PhpTabs 0.6.1, it was maintained for PHP versions 7.0 and 7.1.

Until PhpTabs 0.6.0, it was maintained for PHP versions 5.4, 5.5, 5.6
and HHVM.

________________________________________________________________________

Installation
------------

### Composer

```sh
composer require stdtabs/phptabs
```

### Alternative

Download and extract an archive from [https://github.com/stdtabs/phptabs/releases](https://github.com/stdtabs/phptabs/releases)

Then add this PHP line before usage:

```php
// Use standalone bootstrap
require_once 'src/PhpTabs/bootstrap.php';
```

________________________________________________________________________

Testing
-------

To run tests, you should install PHPUnit first.

```sh
composer require phpunit/phpunit
```

Then run the test suite with:

```sh
vendor/bin/phpunit
```
________________________________________________________________________

Basic Usage
-----------

```php
require_once 'src/PhpTabs/bootstrap.php';

use PhpTabs\PhpTabs;

// Instanciates a tablature
$tablature = new PhpTabs("mytabs.gp3");

// Reads information
echo $tablature->getName();
```

________________________________________________________________________

Methods
-------

### Accessing metadata
________________________________________________________________________
#### getName()

__Type__ *string*

The name of the song.

__Example__

```php
$tablature->getName();
```
________________________________________________________________________
#### getArtist()

__Type__ *string*

The interpreter of the song.

__Example__

```php
$tablature->getArtist();
```
________________________________________________________________________
#### getAlbum()

__Type__ *string*

The name of the album.

__Example__

```php
$tablature->getAlbum();
```
________________________________________________________________________
#### getAuthor()

__Type__ *string*

The author of the song.

__Example__

```php
$tablature->getAuthor();
```
________________________________________________________________________
#### getCopyright()

__Type__ *string*

The copyright of the song.

__Example__

```php
$tablature->getCopyright();
```
________________________________________________________________________
#### getWriter()

__Type__ *string*

The songwriter.

__Example__

```php
$tablature->getWriter();
```
________________________________________________________________________
#### getComments()

__Type__ *string*

The tablature comments. They are compounded of several lines 
separated by a line break (```PHP_EOL```).

__Example__

```php
$tablature->getComments();
```
________________________________________________________________________
#### getTranscriber()

__Type__ *string*


Person who has transcribed tablature

__Support__ 

Guitar Pro >= 4

__Example__

```php
$tablature->getTranscriber();
```
________________________________________________________________________
#### getDate()

__Type__ *string*

Date when tablature has been transcribed

__Support__ 

Guitar Pro >= 4

__Example__

```php
$tablature->getDate();
```
________________________________________________________________________

### Accessing tracks
________________________________________________________________________

#### countTracks()

__Type__ *integer*

The number of tracks 

__Example__

```php
$tablature->countTracks();
```
________________________________________________________________________
#### getTracks()

__Type__ *array*

An array of Track objects

There is one track object for each instrument of the song.

__Example__

```php
$tablature->getTracks();
```
________________________________________________________________________
#### getTrack($index)

__Type__ *object*

__Parameter__ *integer* $index 

The music sheet for one instrument.

__Example__

```php
// Get the first track
$tablature->getTrack(0);
```
________________________________________________________________________

### Accessing channels
________________________________________________________________________

#### countChannels()

__Type__ *integer*

The number of channels 

__Example__

```php
$tablature->countChannels();
```
________________________________________________________________________
#### getChannels()

__Type__ *array*

An array of Channel objects

There is one channel object for each track of the song.

__Example__

```php
$tablature->getChannels();
```
________________________________________________________________________
#### getChannel($index)

__Type__ *object*

__Parameter__ *integer* $index 

The instrument and sound parameters for one track.

__Example__

```php
// Get the first channel
$tablature->getChannel(0);
```
________________________________________________________________________

### Accessing instruments
________________________________________________________________________

#### countInstruments()

__Type__ *integer*

The number of instruments

__Example__

```php
$tablature->countInstruments();
```
________________________________________________________________________
#### getInstruments()

__Type__ *array*

A list of instrument arrays

```php
array(
  'id' => <integer InstrumentId>, 
  'name' => <string InstrumentName>
)
```

__Example__

```php
$tablature->getInstruments();
```
________________________________________________________________________
#### getInstrument($index)

__Type__ *array*

__Parameter__ *integer* $index 

An instrument array

```php
array(
  'id' => <integer InstrumentId>, 
  'name' => <string InstrumentName>
)
```

__Example__

```php
// Get the first instrument
$tablature->getInstrument(0);
```
________________________________________________________________________

### Accessing measure headers
________________________________________________________________________

#### countMeasureHeaders()

__Type__ *integer*

The number of measure headers 

__Example__

```php
$tablature->countMeasureHeaders();
```
________________________________________________________________________
#### getMeasureHeaders()

__Type__ *array*

An array of MeasureHeader objects

__Example__

```php
$tablature->getMeasureHeaders();
```
________________________________________________________________________
#### getMeasureHeader($index)

__Type__ *object*

__Parameter__ *integer* $index 

Measure header contains global informations about the measure.

__Example__

```php
// Get the first measure header
$tablature->getMeasureHeader(0);
```

________________________________________________________________________

### Saving data
________________________________________________________________________

#### save($filename)

__Type__ *bool*

__Parameter__ *string* $filename 

This method records data as binary to the disk or buffer.
It implicitly converts filetype if the specified file extension is
different from the original (see examples below).

Following parameters are allowed:

| Parameter        | Type      | Description                      |
|:-----------------|:----------|:---------------------------------|
| filename.ext     | *bool*    | A file_put_contents() return     |

__Example__

```php
// Instanciate a GP3 tab
$tab = new PhpTabs('mytab.gp3');

// Save as GP3
$tab->save('newfile.gp3');

// Convert and save as GP5
$tab->save('newfile.gp5');

```
________________________________________________________________________

#### convert($type)
________________________________________________________________________

__Type__ *string*

__Parameter__ *string* $type 

This method returns data as a binary string into a specified format.

Following formats are allowed:

| Parameter        | Type      | Description                       |
|:-----------------|:----------|:----------------------------------|
| null             | *string*  | A binary string, original format  |
| gp3              | *string*  | A binary string, GP3 formatted    |
| gp4              | *string*  | A binary string, GP4 formatted    |
| gp5              | *string*  | A binary string, GP5 formatted    |
| mid              | *string*  | A binary string, MIDI formatted   |
| midi             | *string*  | A binary string, MIDI formatted   |

__Example__

```php
// Instanciate a GP3 tab
$tab = new PhpTabs('mytab.gp3');

// Convert as GP3
echo $tab->convert('gp3');

// Convert as GP5
echo $tab->convert('gp5');

// Convert as MIDI
echo $tab->convert('mid');

// Render as original format
// Should be equal as file_get_contents('mytab.gp3')
echo $tab->convert();

```

A lot more examples on [PhpTabs Manual](https://phptabs.readthedocs.io/).
