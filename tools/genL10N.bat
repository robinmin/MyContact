@echo off
echo Generating L10N string list......
set DIR_OLD=%CD%

cd C:\Projects\CodeX\myFramework\resource\js
set TMP_FILE01=%DIR_OLD%\js.l10n.txt

findstr /I /N /C:"L10N\(" *.js|gawk --re-interval -f %DIR_OLD%\genL10N.awk>%TMP_FILE01%

cd %DIR_OLD%
@echo on