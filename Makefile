include .env

install: 
	@docker-compose up -d
	@docker exec -it ${NAME_CONTAINER}_php composer update
	@docker exec -it ${NAME_CONTAINER}_php chmod -R 777 storage/
	@docker exec -it ${NAME_CONTAINER}_php php artisan key:generate
	@docker exec -it ${NAME_CONTAINER}_php php artisan migrate
	@docker exec -it ${NAME_CONTAINER}_php php artisan db:seed

docker-up:
	@docker-compose up -d

docker-build:
	@docker-compose up -d --build

docker-stop:
	@docker-compose stop

docker-down:
	@docker-compose down

docker-status:
	@docker-compose ps

docker-logs:
	@docker-compose logs -f

docker-exec-php:
	@docker exec -it ${NAME_CONTAINER}_php8.0 bash

test:
	@docker exec -it ${NAME_CONTAINER}_php8.0 php artisan test --coverage-html tests/coverage/html

test-unit:
	@docker exec -it ${NAME_CONTAINER}_php8.0 php artisan test --testsuite=Unit

migrate:
	@docker exec -it ${NAME_CONTAINER}_php8.0 php artisan migrate

migrate-rollback:
	@docker exec -it ${NAME_CONTAINER}_php8.0 php artisan migrate:rollback

migrate-refresh:
	@docker exec -it ${NAME_CONTAINER}_php8.0 php artisan migrate:refresh
