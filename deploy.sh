#!/usr/bin/env bash
set -euo pipefail

cd /home/baobao/Quanlyks

echo "=== Kiem tra thay doi chua commit ==="
if [ -n "$(git status --porcelain)" ]; then
    echo "Project co thay doi chua commit. Dung deploy."
    exit 1
fi

echo "=== Lay code moi tu GitHub ==="
git fetch origin main

echo "=== Cap nhat source ==="
git pull --ff-only origin main

echo "=== Kiem tra Docker Compose ==="
docker compose config --quiet

echo "=== Build va cap nhat website ==="
docker compose up -d --build --no-deps web

echo "=== Kiem tra trang thai ==="
docker compose ps

echo "=== Hoan thanh ==="
