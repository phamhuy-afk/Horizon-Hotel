#!/usr/bin/env bash
set -euo pipefail

cd /home/baobao/Quanlyks

echo "=== Kiem tra Git ==="

if [ -n "$(git status --porcelain)" ]; then
    echo "Co thay doi chua commit. Dung deploy."
    exit 1
fi

echo "=== Lay thong tin tu GitHub ==="

git fetch origin main

TARGET=$(git rev-parse origin/main)
CURRENT=$(git rev-parse HEAD)

# Khong co code moi thi khong can deploy
if [ "$TARGET" = "$CURRENT" ]; then
    echo "Website da o phien ban moi nhat."
    exit 0
fi

echo "=== Kiem tra GitHub Actions CI ==="

API="https://api.github.com/repos/phamhuy-afk/Horizon-Hotel/actions/workflows/docker-ci.yml/runs?branch=main&event=push&per_page=100"

RESULT=$(curl -fsSL --retry 2 \
    -H "Accept: application/vnd.github+json" \
    "$API")

# Chi chap nhan commit da qua CI thanh cong
if ! jq -e --arg sha "$TARGET" '
    any(.workflow_runs[];
        .head_sha == $sha
        and .status == "completed"
        and .conclusion == "success"
    )
' <<< "$RESULT" > /dev/null; then

    echo "Commit chua qua CI. Khong deploy."
    exit 0
fi

echo "=== CI thanh cong! Cap nhat website ==="

# Chi cap nhat dung commit da kiem tra
git merge --ff-only "$TARGET"

echo "=== Kiem tra Docker Compose ==="

docker compose config --quiet

echo "=== Build va cap nhat container web ==="

docker compose up -d --build --no-deps web

echo "=== Trang thai he thong ==="

docker compose ps

echo "=== Deploy hoan thanh ==="
