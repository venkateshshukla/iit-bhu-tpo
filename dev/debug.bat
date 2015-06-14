@echo off
type styles\*.css ..\..\services\thundersky\console\ui\css\thundersky-styles.css > ..\ui\css\executive-ui.css
type ..\ui\js\jquery-1.6.1.js ..\ui\js\jquery.tmpl.js ..\ui\js\jquery.cookie.js ..\ui\js\json2.js ..\..\services\snowblozm\js\Snowblozm.class.js ..\..\services\firespark\js\dist\jquery-firespark.js init.js helpers\*.js services\*.js workflows\*.js > ..\ui\js\executive-ui.js
pause
