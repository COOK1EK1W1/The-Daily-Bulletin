@echo off
set ip_address_string="IPv4 Address"
for /f "usebackq tokens=2 delims=:" %%f in (`ipconfig ^| findstr /c:%ip_address_string%`) do set ip_address=%%f
echo Bulletin address at%ip_address%
set ip_address=%ip_address:~1%
pause
