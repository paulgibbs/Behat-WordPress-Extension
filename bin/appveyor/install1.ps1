# BEFORE services (IIS, etc) are launched.

## Install WebPI.
cInst webpicommandline -y

# Install environment dependencies via WebPI.
WebPICMD /Install /Application:"PHP70" /AcceptEULA

echo (Get-Website -Name "Default Web Site").PhysicalPath
echo (get-webapplication foo).PhysicalPath
echo @php %%~dp0composer.phar %%* > composer.bat
appveyor DownloadFile https://getcomposer.org/composer.phar

# Start Windows services for IIS.
Start-Service W3SVC
Start-Service WAS

# Install WordHat.
cd $APPVEYOR_BUILD_FOLDER
composer install --no-interaction --prefer-dist --no-progress
