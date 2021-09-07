up: docker-up dashboard-profiles-up
down: docker-down
restart: docker-down docker-up dashboard-profiles-up
init: docker-down-clear docker-pull docker-build docker-up dashboard-init
test: dashboard-test
test-unit: dashboard-test-unit
test-init: dashboard-test-db-init dashboard-test

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

dashboard-init: dashboard-composer-install dashboard-assets-install dashboard-oauth-keys dashboard-wait-db dashboard-migrations dashboard-fixtures dashboard-profiles-up

dashboard-composer-install:
	docker-compose run --rm php-cli composer install

dashboard-assets-install:
	docker-compose run --rm node yarn install
	docker-compose run --rm node npm rebuild node-sass

dashboard-oauth-keys:
	docker-compose run --rm php-cli mkdir -p var/ouath
	docker-compose run --rm php-cli openssl genrsa -out var/ouath/private.key 2048
	docker-compose run --rm php-cli openssl rsa -in var/ouath/private.key -pubout -out var/ouath/public.key

dashboard-wait-db:
	until docker-compose exec -T postgres pg_isready --timeout=0 --dbname=dashboard ; do sleep 1 ; done

dashboard-migrations:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction

dashboard-fixtures:
	docker-compose run --rm php-cli php bin/console doctrine:fixtures:load --no-interaction

dashboard-assets-dev:
	docker-compose run --rm node npm run dev

dashboard-profiles-up:
	docker-compose run --name node-watch -d node-watch
	docker-compose run --name queue-worker -d queue-worker

dashboard-test-db-init: dashboard-test-drop-db dashboard-test-db dashboard-test-schema dashboard-test-fixtures

dashboard-test:
	docker-compose run --rm php-cli php bin/phpunit

dashboard-test-unit:
	docker-compose run --rm php-cli php bin/phpunit --testsuite=unit

dashboard-test-drop-db:
	docker-compose run --rm php-cli php bin/console --env=test doctrine:database:drop --if-exists --force --no-interaction

dashboard-test-db:
	docker-compose run --rm php-cli php bin/console --env=test doctrine:database:create --if-not-exists --no-interaction

dashboard-test-schema:
	docker-compose run --rm php-cli php bin/console --env=test doctrine:schema:update --force --no-interaction

dashboard-test-fixtures:
	docker-compose run --rm php-cli php bin/console --env=test doctrine:fixtures:load --no-interaction

build-production:
	docker build --pull -f dashboard/docker/production/nginx/nginx.docker -t ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/php/php-fpm.docker -t ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/php/php-cli.docker -t ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/postgres/postgres.docker -t ${REGISTRY_ADDRESS}/postgres:${IMAGE_TAG} dashboard
	docker build --pull -f dashboard/docker/production/redis/redis.docker -t ${REGISTRY_ADDRESS}/redis:${IMAGE_TAG} dashboard
	docker build --pull -f centrifugo/docker/production/centrifugo.docker -t ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG} centrifugo

push-production:
	docker push ${REGISTRY_ADDRESS}/nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/php-cli:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/postgres:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/redis:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/centrifugo:${IMAGE_TAG}

deploy-production:
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -o StrictHostKeyChecking=no -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_APP_SECRET=${DASHBOARD_APP_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_DB_PASSWORD=${DASHBOARD_DB_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_REDIS_PASSWORD=${DASHBOARD_REDIS_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_MAILER_DSN=${DASHBOARD_MAILER_DSN}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "DASHBOARD_OAUTH_GITHUB_SECRET=${DASHBOARD_OAUTH_GITHUB_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_BASE_URL=${STORAGE_BASE_URL}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_HOST=${STORAGE_FTP_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_USERNAME=${STORAGE_FTP_USERNAME}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "STORAGE_FTP_PASSWORD=${STORAGE_FTP_PASSWORD}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_ALLOWED_ORIGINS=${CENTRIFUGO_ALLOWED_ORIGINS}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_WS_HOST=${CENTRIFUGO_WS_HOST}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_API_KEY=${CENTRIFUGO_API_KEY}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "CENTRIFUGO_SECRET=${CENTRIFUGO_SECRET}" >> .env'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose up --build -d'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'until docker-compose exec -T postgres pg_isready --timeout=0 --dbname=dashboard ; do sleep 1 ; done'
	ssh -o StrictHostKeyChecking=no ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --no-interaction'
