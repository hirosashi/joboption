#!/bin/bash
# 準備サイト（さくら www/joboption）へ src/ を反映する。
# サーバ側の config/config.php と storage/（DB・ログ）は上書きしない。
set -euo pipefail
cd "$(dirname "$0")"
: "${SAKURA_SSH_HOST:?}" "${SAKURA_SSH_USER:?}"
KEY="${SAKURA_SSH_KEY_FILE:-$HOME/.ssh/sakura_jo}"
SSH=(ssh -i "$KEY" -o BatchMode=yes -o StrictHostKeyChecking=accept-new "$SAKURA_SSH_USER@$SAKURA_SSH_HOST")
REMOTE=www/joboption
STAMP=$(date +%Y%m%d_%H%M%S)

echo "== backup"
"${SSH[@]}" "mkdir -p ~/backups && tar czf ~/backups/joboption_$STAMP.tar.gz -C ~/www joboption && ls -la ~/backups/joboption_$STAMP.tar.gz"

echo "== upload src"
tar czf - -C src --exclude=config/config.php --exclude=config/config.local.php \
  --exclude='storage/db/*.sqlite*' --exclude='storage/logs/*.log' . \
  | "${SSH[@]}" "mkdir -p ~/$REMOTE && tar xzf - -C ~/$REMOTE"

echo "== upload tools/db"
tar czf - tools db | "${SSH[@]}" "mkdir -p ~/joboption_tools && tar xzf - -C ~/joboption_tools"

echo "== check"
"${SSH[@]}" "cd ~/$REMOTE && find . -name '*.php' -exec php -l {} \; | grep -v '^No syntax' || true; ls config"
