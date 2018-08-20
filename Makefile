.PHONY: up start stop down reboot console composer migrate seed tests nginx-reload fix-permissions

# Set dir of Makefile to a variable to use later
MAKEPATH := $(abspath $(lastword $(MAKEFILE_LIST)))
PWD := $(dir $(MAKEPATH))
CONTAINER_FPM := "blogmvc_fpm"
CONTAINER_NGINX := "blogmvc_web"
UID := 1000

up:
	docker-compose up -d

start:
	docker-compose start

stop:
	docker-compose stop

down:
	docker-compose down

reboot:
	docker-compose down && docker-compose up -d

cmd=""
console:
	docker exec -it \
		-u $(UID) \
		$(CONTAINER_FPM) \
		php bin/console $(cmd) \
		2>/dev/null || true

migrate:
	docker exec -it \
		-u $(UID) \
		$(CONTAINER_FPM) \
		php bin/console doctrine:migrations:migrate \
		2>/dev/null || true

seed:
	docker exec -it \
		-u $(UID) \
		$(CONTAINER_FPM) \
		php bin/console doctrine:fixtures:load \
		2>/dev/null || true

cmd=""
composer:
	docker exec -it \
		-u $(UID) \
		$(CONTAINER_FPM) \
		composer $(cmd) \
		2>/dev/null || true

tests:
	docker exec -it \
		-u $(UID) \
		$(CONTAINER_FPM) \
		./bin/phpunit \
		2>/dev/null || true

nginx-reload:
	docker kill -s HUP $(CONTAINER_NGINX) 2>/dev/null || true

fix-permissions:
	docker exec -it $(CONTAINER_FPM) chown -R 1000:100 ./var/log 2>/dev/null || true && \
	docker exec -it $(CONTAINER_FPM) chown -R 1000:100 ./var/cache 2>/dev/null || true && \
	docker exec -it $(CONTAINER_FPM) find ./var -type d -exec chmod 755 {} \; 2>/dev/null || true && \
	docker exec -it $(CONTAINER_FPM) find ./vendor -type d -exec chmod 755 {} \; 2>/dev/null || true
