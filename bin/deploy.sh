#!/usr/bin/env bash

# if [[ "false" != "$TRAVIS_PULL_REQUEST" ]]; then
#     echo "Not deploying pull requests."
#     exit
# fi
 
if [[ "master" != "$TRAVIS_BRANCH" ]]; then
    echo "Not on the 'master' branch."
    exit
fi

set -e

git clone -b dist --quiet "https://github.com/${TRAVIS_REPO_SLUG}.git" dist
npm run dist
cd dist
## すべての変更を含むワークツリーの内容をインデックスに追加.
git add -A
git commit -m "Update from travis $TRAVIS_COMMIT"
git push --quiet "https://${GH_TOKEN}@github.com/${TRAVIS_REPO_SLUG}.git" dist 2> /dev/null