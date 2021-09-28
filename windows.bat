REM root
rmdir /S/Q %~dp0atk4
mklink /D  %~dp0atk4 %~dp0vendor\xepan\atk4\public\atk4

REM admin
rmdir /S/Q %~dp0admin\atk4
rmdir /S/Q %~dp0admin\vendor
rmdir /S/Q %~dp0admin\websites
rmdir /S/Q %~dp0admin\xepantemplates


mklink /D  %~dp0admin\atk4 %~dp0vendor\xepan\atk4\public\atk4
mklink /D  %~dp0admin\vendor %~dp0vendor
mklink /D  %~dp0admin\websites %~dp0websites
mklink /D  %~dp0admin\xepantemplates %~dp0xepantemplates

REM install

rmdir /S/Q %~dp0install\atk4
rmdir /S/Q %~dp0install\vendor
rmdir /S/Q %~dp0install\websites
rmdir /S/Q %~dp0install\xepantemplates

mklink /D  %~dp0install\atk4 %~dp0vendor\xepan\atk4\public\atk4
mklink /D  %~dp0install\vendor %~dp0vendor
mklink /D  %~dp0install\websites %~dp0websites
mklink /D  %~dp0install\xepantemplates %~dp0xepantemplates