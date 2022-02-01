# WordPress Plugin

The objective of this project is to create a wordpress plugin that sends a discord message to each new comment on wordpress. All this using the discord webhook as well as PHP.

## Installation

We need to install [docker compose](https://docs.docker.com/compose/install/) into you computer.
After that you can start container.

```bash
cd wordpress
docker compose up -d
```

Now we have docker container up.

We need to install python manually into wordpress container.

```bash
docker ps
# list docker container to recover wordpress container id
docker exec -it container_id /bin/bash
```

The last command will open a shell.

```bash
chmod u+x /var/www/html/wp-content/plugins/discord_plugin/config.sh 
sh /var/www/html/wp-content/plugins/discord_plugin/config.sh

exit
```

## Usage

Now you can start commenting on [wordpress](http://localhost:8000) posts.
