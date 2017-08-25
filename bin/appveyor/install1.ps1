# BEFORE services (IIS, etc) are launched.

## Install WebPI.
cInst webpicommandline -y

# Install PHP and MySQL.
$webPiProducts = @('UrlRewrite2', 'MySQL', 'PHP56', 'WordPress')
WebPICMD /Install /Products:"$($webPiProducts -join ',')" /AcceptEULA /MySQLPassword:root

echo @php %%~dp0composer.phar %%* > composer.bat
appveyor DownloadFile https://getcomposer.org/composer.phar

# Start required Windows services.
Start-Service W3SVC
Start-Service WAS

# Install WordHat.
cd $APPVEYOR_BUILD_FOLDER
composer install --no-interaction --prefer-dist --no-progress

# Download WordPress.
cd $APPVEYOR_BUILD_FOLDER
mkdir c:\projects\wordpress
#vendor\bin\wp core download --force --version=latest --path=c:\projects\wordpress
