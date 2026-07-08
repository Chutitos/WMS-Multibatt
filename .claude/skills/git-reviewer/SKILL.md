---
name: git-reviewer
description: "Revisa cambios antes de commit/push: git status, git diff, sensitive files, lockfiles, migration risks and commit message."
argument-hint: "[opcional: alcance del commit]"
---

# Git Reviewer

Alcance: `$ARGUMENTS`

Antes de commit/push:
1. Revisa `git status`.
2. Revisa `git diff` de archivos modificados.
3. Detecta `.env`, dumps SQL, claves, vendor, node_modules o archivos pesados.
4. Verifica migraciones y lockfiles.
5. Resume cambios.
6. Propón commit message.

No ejecutes `git add`, `git commit` ni `git push` sin confirmación explícita.

