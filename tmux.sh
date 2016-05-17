#!/bin/sh

tmux new -s ec -d
tmux split-window -h -p 20

tmux send -t ec.1 'docker-compose run --rm node bin/gulp watch' ENTER

tmux select-pane -t 0
tmux attach
