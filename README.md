Simplelock
==========

## Overview

A simple locking/semaphore library for PHP 5.3+ with different adapters. Helps to keep code thread safe and prevent flooding or multiple execution of time consumptive code that could otherwise break your application.

You can set locks for a specific time duration. Locks can also be autoreleased on script termination. This assures that a task can only executed once at a time.

## Installation

### With Composer

Just add `pteich/simplelock` to your composer.json require:

```
"pteich/simplelock":  "dev-master"
```

### Without Composer

Download archive from Github and expand it to a directory. There is no autoloader provided so you'll have to deal with it for your own. You can register Simplelock directory with your PSR-4 compatible autoloader.

 
## Usage

```php
// create apc adapter
$adapter = new \Simplelock\Adapter\Apc();

// create simplelock object using $adapter and use autounlock feature
$lock = new \Simplelock(
	$adapter,
	true
);

$ttl = 60; // seconds to lock
$mykey = 'my key'; // key for this lock

if (!$lock->locked($mykey)) {
	// lock now		
	$lock->lock($mykey,$ttl);
	// do hard work here
}
```

## Adapters

First argumente if the Simplelock constructor is an adapter object. Supported adapters are:
 
* **mock** - unlock always, for testing purposes
* **file** - file backend, keeps semaphore files in configurable directory
* **apc** - apc backend, keeps values in memory

### APC Adapter

No config must be provided. APC extension is required. Values are delete from memory on unlock.

### File Adapter

Provide an array with `path` key pointing to a valid directory to store semaphore files. This adapter deletes files on unlock.

Example:
```php

$adapter = new \Simplelock\Adapter\File(array(
	'path' => __DIR__ . '/lock'
));

```

### Mock Adapter

This adapter keeps everything always unlocked. Useful for testing or to disable locking at all without removing calls to Simplelock.

### Autounlock

If true is provided as second parameter to the Simplelock constructor all locks are automatically released on script termination.
