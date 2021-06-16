up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear docker-pull docker-build docker-up docker-init
test: dashboard-test

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

docker-init: composer-install

composer-install:
	docker-compose run --rm php-cli composer install

dashboard-test:
	docker-compose run --rm php-cli php bin/phpunit

build-production:
	docker build --pull -f dashboard/docker/production/nginx/nginx.docker -t ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/php/php-fpm.docker -t ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/php/php-cli.docker -t ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/postgres/postgres.docker -t ${REGISTRY_ADDRESS}/postgres:${IMAGE_TAG} dashboard

push-production:
	docker push ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/postgres:${IMAGE_TAG}

deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_APP_SECRET=${DASHBOARD_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_DB_PASSWORD=${DASHBOARD_DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose up --build -d'
