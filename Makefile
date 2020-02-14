up: docker-up
init: docker-down-clear manager-clear docker-pull docker-build docker-up manager-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-duild:
	docker-compose build

cli:
	docker-compose run --rm manager-php-cli php bin/app.php

build-production:
	docker build --pull --file=manager/docker/production/nginx.docker --tag registry/manager-nginx:0 manager
	docker build --pull --file=manager/docker/production/php-fpm.docker --tag registry/manager-php-fpm:0 manager
	docker build --pull --file=manager/docker/production/php-cli.docker --tag registry/manager-php-cli:0 manager

push-production:
	docker push registry/manager-nginx:0
	docker push registry/manager-php-fpm:0
	docker push registry/manager-php-cli:0

deploy-production:
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose --build -d'
