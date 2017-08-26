# Transmission

You can use this package to communicate with your transmission installation's web/RPC server.

You can set up transmission by going into preferences > remote > enable remote access.

 This package was written against Transmission's [RPC spec](https://trac.transmissionbt.com/browser/branches/1.7x/doc/rpc-spec.txt), if you ever need more info on what each call does or what data it returns, that's the best place to start.
 ## Installation
 
 First you'll need to pull in the library
 
 ```bash
 composer require happydemon/transmission
 ```
 
 ## Short intro
 
 Next you'll need to set up the `Transmission` object.
 
 ```php
 $transmission = new \HappyDemon\Transmission\Transmission();
 ```
 
 By not defining any config, the object will use sensible defaults to connect to transmission.
 
 Let's retrieve the list of torrents we have in Transmission
 

 ```php
 var_dump($transmission->torrents()->get());
 ```
 
## Config

When initialising a `Transmission` object you can pass an array with several config options;

**ssl** *boolean*

Is the transmission web server served over https?

**host** *string*

The host/IP the transmission web server is running on (defaults to 127.0.0.1).

**port** *string*

What port is the transmission web server running on (defaults to 9091).

**url** *string*

What endpoint is the transmission web server running on (defaults to /transmission/rpc).

**username** *string*

What username is used to authenticate? (empty by default)

**password** *string*

What password is used to authenticate? (empty by default)

## Main torrent methods

#### Retrieving torrents
Retrieves the list of torrents you see in transmission.

```php
$transmission->torrents()->get();
```

This will always return an array with `HappyDemon\Services\Transmission\Torrents\Entity` objects.
You can check out the class to see what properties are available to it.

You could also use it to retrieve a single or multiple torrents that you have the ID of:


```php
// Get the torrent with id 1
$transmission->torrents()->get(1);


// Get a few torrents
$transmission->torrents()->get([1,6,12]);
```

### Adding torrents

```php
$torrent = $transmission->torrents()->addFromUrl($urlToTorrent);
$torrent = $transmission->torrents()->addFile($filePath);
$torrent = $transmission->torrents()->addFromBase64($fileBlob);
```

Using any of these methods will let you add new torrents.
Each time it will return a `HappyDemon\Services\Transmission\Torrents\Entity` object.
The catch is, only 3 properties will be set though: id, hashString & name.

## Torrent entity methods

These are the methods that are available on a `HappyDemon\Services\Transmission\Torrents\Entity` object.

### Actions

#### start
```php
$torrent->start();
```
Starts the specific torrent.

#### stop
```php
$torrent->stop();
```
Stops the specific torrent.

#### verify
```php
$torrent->verify();
```
Verifies the specific torrent.

#### reannounce
```php
$torrent->announce();
```
Reannounces the specific torrent.

#### remove
```php
$torrent->remove();
```
Removes the specific torrent.

#### move
```php
$torrent->move();
```
Moves the specific torrent to a different location on your file system.

#### update
Allows you to update some torrent-specific settings.
```php
$torrent->settings($properties);
```

You could also update a singular torrent-setting like this:

```php
// remove the download limit
$torrent->setDownloadLimited(false);
```
```php
// set the bandwidth priority
$torrent->setBandwidthPriority(1);
```
```php
// set the download limit (in K/s)
$torrent->setDownloadLimit(1024*3);
```
```php
// Mark files as wanted
// Use an empty array for all files, use an array of ids to be more selective [0,2]
// In this case we only want to download the second file
$torrent->setFilesWanted([1]);
```
```php
// Mark files as unwanted
// Use an empty array for all files, use an array of ids to be more selective [0,2]
// In this case we don't want to download the first file
$torrent->setFilesUnwanted([0]);
```
```php
// true if session upload limits are honored
$torrent->setHonorsSessionLimits(true);
```
```php
// Set a new directory for the torrent's contents
$torrent->setLocation('c:/downloads');
```
```php
// Set the peer limit (max amount of peer)
$torrent->setPeerLimit(40);
```
```php
// Set the priority for files to high
// Use en empty array to update priority for all files
// or use the file ids [0,2]
$torrent->setPriorityHigh([]);
```
```php
// Set the priority for files to low
// Use en empty array to update priority for all files
// or use the file ids [0,2]
$torrent->setPriorityLow(false);
```
```php
// Set the priority for files to normall
// Use en empty array to update priority for all files
// or use the file ids [0,2]
$torrent->setPriorityNormal(false);
```
```php
// Set the seed ratio limit
$torrent->setSeedRatioLimit(1.2);
```
```php
// Set the seed ratio mode
// 0 = global, 1 = see seedRatioLimit, 2 = unlimitted
$torrent->setSeedRatioMode(1);
```
```php
// Change the upload limit (in K/s)
$torrent->setUploadLimit(512);
```
```php
// Should the torrent's upload be limitted?
$torrent->setUploadLimited(false);
```

### Getters
The entity has a lot of properties, however I've added a few getters for ease-of-use:

#### status
```php
$torrent->status();
```
Will return the torrents status as a string, whereas `$torrent->status` only returns a number.

#### activityDate
```php
$torrent->activityDate();
```
Will return a `DateTime` object, representing the last time torrent activity happened

#### addedDate
```php
$torrent->addedDate();
```
Will return a `DateTime` object, representing the date/time the torrent was added.

#### doneDate
```php
$torrent->doneDate();
```
Will return a `DateTime` object, representing time the torrent was completed.

#### percentDone
```php
$torrent->percentDone();
```
Will return the percentage that the torrent is completed whereas `$torrent->percentDone` would return this as a float.
