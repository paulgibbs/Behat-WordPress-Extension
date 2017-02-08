#!/bin/bash

# Exit if anything fails AND echo each command before executing
# http://www.peterbe.com/plog/set-ex
set -ex

WH_WORDPRESS_DIR=${WH_WORDPRESS_DIR-/tmp/wordpress}
NAP_LENGTH=1
SELENIUM_PORT=4444

# Wait for a specific port to respond to connections.
wait_for_port() {
  local PORT=$1
  while echo | telnet localhost $PORT 2>&1 | grep -qe 'Connection refused'; do
    echo "Connection refused on port $PORT. Waiting $NAP_LENGTH seconds..."
    sleep $NAP_LENGTH
  done
}

rm -f /tmp/.X0-lock
Xvfb > /dev/null 2>&1 &
export DISPLAY=localhost:0.0

# Start Selenium
vendor/bin/selenium-server-standalone > /dev/null 2>&1 &
wait_for_port $SELENIUM_PORT
sleep 1
