@echo off
chcp 65001 >nul
title Instalação - Sistema de Avaliação EN_430 (PHP 8)

echo ===========================================================
echo   🪟 Instalação e Publicação no Windows 10
echo   Sistema de Avaliação — Introdução à Enfermagem (EN_430)
echo   PHP 8 + Apache + SQLite
echo ===========================================================
echo.
echo   Executando script PowerShell com política RemoteSigned...
echo   (Janela do PowerShell será aberta)
echo.

:: Caminho do script PS1 (mesmo diretório deste .bat)
set "SCRIPT_PS1=%~dp0instalar_publicar_windows.ps1"

:: Executar com política que permite scripts locais
powershell -ExecutionPolicy RemoteSigned -File "%SCRIPT_PS1%"

echo.
if %ERRORLEVEL% EQU 0 (
    echo   ✅ Script concluído com sucesso!
) else (
    echo   ⚠️  Script retornou código de erro: %ERRORLEVEL%
    echo   Verifique as mensagens acima.
)
echo.
pause
