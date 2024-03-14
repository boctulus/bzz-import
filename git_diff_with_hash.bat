@echo off

setlocal enabledelayedexpansion

set "num_of_commits_ahead=%1"

for /f "usebackq" %%G in (`git rev-parse HEAD~%num_of_commits_ahead%`) do set "commit_hash=%%G"

echo Commit Hash: %commit_hash%
git diff --name-only HEAD HEAD~%num_of_commits_ahead%
