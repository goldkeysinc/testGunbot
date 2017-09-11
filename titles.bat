@ECHO OFF
SETLOCAL EnableExtensions DisableDelayedExpansion

rem Part 1: ALL USEFUL WINDOW TITLES
echo(
set "_myExcludes=^\"conhost ^\"dwm ^\"nvxdsync ^\"nvvsvc ^\"dllhost ^\"taskhostex"
for /F "tokens=1,2,8,* delims=," %%G in ('
  tasklist /V /fo:csv ^| findstr /V "%_myExcludes%"
                                        ') do (
    if NOT "%%~J"=="N/A" echo %%H %%J
)
