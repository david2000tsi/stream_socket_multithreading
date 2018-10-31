
# Stream Socket Multithreading Server/Client

This is a basic implementation of stream sockets and thread resources.

Before anything, in command line (linux environment), run composer dump-autoload command to generate autoload files:

```
composer dump-autoload
```

Use files inside tests/ directory to run server/client:

```
cd <project_path>/tests/
```

Start Server:

To accept one connection at a time:

```
php ServerSingleThreadTest.php
```

To accept many connections:

```
php ServerMultiThreadTest.php
```

Run Client:

```
php ClientTest.php
```
