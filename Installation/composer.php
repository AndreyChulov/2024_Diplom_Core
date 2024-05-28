<?php
echo 'Downloading composer installer...' . PHP_EOL;
copy('https://getcomposer.org/installer', 'composer-setup.php');
echo 'Composer installer downloaded...' . PHP_EOL;

echo 'Checking composer installer sha384.' . PHP_EOL;
if (
    hash_file('sha384', 'composer-setup.php') ===
    'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') {
    echo 'Installer verified';
} else {
    echo 'Installer corrupt';
    unlink('composer-setup.php');
}
echo PHP_EOL;

echo 'Installing composer...' . PHP_EOL;
require "composer-setup.php";
echo 'Installing composer done.' . PHP_EOL;

echo 'Deleting composer installer...' . PHP_EOL;
unlink('composer-setup.php');
echo 'Composer installer deleted...' . PHP_EOL;
